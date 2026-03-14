<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentSetting extends Model
{
    use HasFactory;

    public $incrementing = false; // UUID

    protected $keyType = 'string';

    protected $fillable = [
        'faculty_id',
        'department_id',
        'level',
        'sex',
        'matric_number',
        'payment_type',
        'amount',
        'description',
        'student_type',
        'entry_mode',
        'session',
        'semester',
        'installmental_allow_status',
        'number_of_instalment',
        'list_instalment_percentage',
    ];

    protected $casts = [
        'level' => 'array',
        'entry_mode' => 'array',
        'student_type' => 'array',
        'list_instalment_percentage' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public static function getPaymentTypes()
    {
        // Fetch distinct payment types from the database
        $types = static::query()
            ->distinct()
            ->pluck('payment_type')
            ->filter()
            ->values();

        // Define the defaults you always want available
        $defaultTypes = ['acceptance', 'tuition', 'medical'];

        // Merge defaults and fetch types, remove duplicates, and resort
        return collect($defaultTypes)->merge($types)->unique()->sort()->values();
    }

    /**
     * Get all payment settings applicable to a specific student for the current active session/semester.
     * This replicates the exact logic used in the Student Dashboard to calculate fees.
     */
    public static function getFeesForStudent($student, $currentSession = null, $currentSemester = null)
    {
        $currentSession = $currentSession ?? activeSession()->name ?? null;
        $activeSemester = activeSemester();
        $currentSemester = $currentSemester ?? $activeSemester?->code ?? null;

        if (! $currentSession) {
            return collect(); // Empty collection if no active session
        }

        // Determine if student is semester-affected based on all override fields
        // A "specific override" has at least one non-empty override field.
        $isSpecificOverride = false;
        $studentIsSemesterAffected = false;
        if ($currentSemester && $activeSemester) {
            $semesterStreams = $activeSemester->stream ?? [];
            $semesterCampuses = $activeSemester->campus_id ?? [];
            $semesterProgrammes = $activeSemester->programme ?? [];

            $isSpecificOverride = ! empty($semesterStreams) || ! empty($semesterCampuses) || ! empty($semesterProgrammes);

            $matchesStream = empty($semesterStreams) || in_array((string) $student->stream, $semesterStreams);
            $matchesCampus = empty($semesterCampuses) || in_array($student->campus_id, $semesterCampuses);
            $matchesProgramme = empty($semesterProgrammes) || in_array($student->programme, $semesterProgrammes);

            $studentIsSemesterAffected = $isSpecificOverride && $matchesStream && $matchesCampus && $matchesProgramme;
        }

        // Determine Effective Level for Payment
        // Rule: If student is admitted in current session AND level is 200 or 300 (DE/Transfer), pay 100 level fees.
        $effectiveLevel = (int) $student->level;
        if ($student->admission_session === $currentSession && in_array($effectiveLevel, [200, 300])) {
            $effectiveLevel = 100;
        }

        return self::query()
            ->where('session', $currentSession) // session must always match
            ->when($student->programme, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('student_type')
                        ->orWhereJsonContains('student_type', $student->programme);
                });
            }, function ($q) {
                $q->whereNull('student_type');
            })
            ->when($effectiveLevel, function ($q) use ($effectiveLevel) {
                $q->where(function ($sub) use ($effectiveLevel) {
                    $sub->whereNull('level')
                        ->orWhereJsonContains('level', $effectiveLevel);
                });
            }, function ($q) {
                $q->whereNull('level');
            })
            ->when($student->department?->faculty_id, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('faculty_id')
                        ->orWhere('faculty_id', $student->department->faculty_id);
                });
            })
            ->when($student->department_id, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('department_id')
                        ->orWhere('department_id', $student->department_id);
                });
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('sex')
                    ->orWhere('sex', $student->sex);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('matric_number')
                    ->orWhere('matric_number', $student->matric_no);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('entry_mode')
                    ->orWhereJsonContains('entry_mode', $student->entry_mode);
            })
            ->where(function ($q) use ($studentIsSemesterAffected, $currentSemester) {
                if ($studentIsSemesterAffected) {
                    // Student matched a specific semester override → ONLY that semester's fees
                    $q->where('semester', $currentSemester);
                } else {
                    // Student is on global semester → session-wide fees only
                    $q->whereNull('semester');
                }
            })
            ->get();
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
