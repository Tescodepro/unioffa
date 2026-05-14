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
        'faculty_ids',
        'department_ids',
        'level',
        'sexes',
        'matric_numbers',
        'payment_type',
        'amount',
        'description',
        'student_type',
        'entry_mode',
        'session',
        'semesters',
        'installmental_allow_status',
        'number_of_instalment',
        'list_instalment_percentage',
    ];

    protected $casts = [
        'level' => 'array',
        'entry_mode' => 'array',
        'student_type' => 'array',
        'list_instalment_percentage' => 'array',
        'faculty_ids' => 'array',
        'department_ids' => 'array',
        'sexes' => 'array',
        'matric_numbers' => 'array',
        'semesters' => 'array',
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
        // Rule: Semesters should not be applicable for REGULAR and DIPLOMA programmes.
        $studentIsSemesterAffected = false;
        if ($currentSemester && $activeSemester && ! in_array(strtoupper($student->programme), ['REGULAR', 'DIPLOMA'])) {
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
                        ->orWhere('student_type', '[]')
                        ->orWhereJsonContains('student_type', $student->programme);
                });
            }, function ($q) {
                $q->whereNull('student_type')->orWhere('student_type', '[]');
            })
            ->when($effectiveLevel, function ($q) use ($effectiveLevel) {
                $q->where(function ($sub) use ($effectiveLevel) {
                    $sub->whereNull('level')
                        ->orWhere('level', '[]')
                        ->orWhereJsonContains('level', $effectiveLevel);
                });
            }, function ($q) {
                $q->whereNull('level')->orWhere('level', '[]');
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('faculty_ids')
                    ->orWhere('faculty_ids', '[]')
                    ->orWhereJsonContains('faculty_ids', $student->department->faculty_id);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('department_ids')
                    ->orWhere('department_ids', '[]')
                    ->orWhereJsonContains('department_ids', $student->department_id);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('sexes')
                    ->orWhere('sexes', '[]')
                    ->orWhereJsonContains('sexes', $student->sex);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('matric_numbers')
                    ->orWhere('matric_numbers', '[]')
                    ->orWhereJsonContains('matric_numbers', $student->matric_no);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('entry_mode')
                    ->orWhere('entry_mode', '[]')
                    ->orWhereJsonContains('entry_mode', $student->entry_mode);
            })
            ->where(function ($q) use ($studentIsSemesterAffected, $currentSemester) {
                if ($studentIsSemesterAffected) {
                    // Student matched a specific semester override → show that semester's fees + session-wide fees
                    $q->where(function ($sq) use ($currentSemester) {
                        $sq->whereJsonContains('semesters', $currentSemester)
                            ->orWhereNull('semesters')
                            ->orWhere('semesters', '[]');
                    });
                } else {
                    // Student is on global semester → session-wide fees only
                    $q->where(function ($sq) {
                        $sq->whereNull('semesters')
                            ->orWhere('semesters', '[]');
                    });
                }
            })
            ->get();
    }

    public function faculties()
    {
        return Faculty::whereIn('id', $this->faculty_ids ?? [])->get();
    }

    public function departments()
    {
        return Department::whereIn('id', $this->department_ids ?? [])->get();
    }
}
