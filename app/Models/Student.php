<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_GRADUATED = 2;
    const STATUS_SPILLED = 3;

    public $incrementing = false; // because UUID

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'campus_id',
        'matric_no',
        'programme',
        'entry_mode',
        'stream',
        'jamb_registration_number',
        'sex',
        'department_id',
        'level',
        'admission_session',
        'address',
        'admission_date',
        'status',
    ];

    // Auto-generate UUID
    protected static function booted()
    {
        static::creating(function ($student) {
            if (empty($student->id)) {
                $student->id = (string) Str::uuid();
            }
        });
    }

    // 🔹 Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // if department becomes a table later
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function hostelAssignment()
    {
        return $this->hasOne(StudentHostelAssignment::class);
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class);
    }

    public static function generateMatricNo(string $departmentCode, string $admissionYear, string $entryModeCode): string
    {
        return DB::transaction(function () use ($departmentCode, $admissionYear, $entryModeCode) {
            $entryMode = EntryMode::where('code', $entryModeCode)->first();
            if (! $entryMode) {
                Log::error('Invalid entry mode specified: '.$entryModeCode);
                $prefix = '';
            } else {
                $prefix = $entryMode->matric_prefix;
            }

            // Modify department code based on prefix
            $modifiedCode = $prefix.$departmentCode;

            // Get department and faculty
            $department = Department::where('department_code', $departmentCode)->firstOrFail();
            $faculty = $department->faculty;

            // Count students in this department + year
            $allMatricNumbers = self::where('department_id', $department->id)
                ->whereYear('admission_session', $admissionYear)
                ->pluck('matric_no')
                ->filter(function ($matric) {
                    return preg_match('/^\d{2}\/[A-Z]{2,5}\/(T|D|DP|DE|TR)?[A-Z]{2,5}\/\d{3}$/', $matric);
                });
            $count = $allMatricNumbers->count() + 1;

            // Format year (last 2 digits only)
            [$startYear] = explode('/', $admissionYear);
            $yearShort = substr(trim($startYear), -2); // e.g., 2023 -> "23"

            // Format sequence (3 digits: 001, 002, etc.)
            $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

            // Generate matric number
            $matricNo = strtoupper("{$yearShort}/{$faculty->faculty_code}/{$modifiedCode}/{$sequence}");

            // Check for duplicates in users and students tables
            $maxAttempts = 9990; // Max sequence number

            while ($count <= $maxAttempts) {
                $existsInUsers = User::where('username', $matricNo)->exists();
                $existsInStudents = self::where('matric_no', $matricNo)->exists();

                if (! $existsInUsers && ! $existsInStudents) {
                    return $matricNo; // Matric number is unique
                }

                // Increment sequence and try again
                $count++;
                $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
                $matricNo = strtoupper("{$yearShort}/{$faculty->faculty_code}/{$modifiedCode}/{$sequence}");
            }

            Log::error('Unable to generate unique matric number. Maximum student limit reached.');
        });
    }

    public function hasMatricNumber(): bool
    {
        // If has no matric number -> false
        if (empty($this->matric_no)) {
            return false;
        }

        // Reject application-style IDs (e.g. UOO/APP/2025/00001)
        if (preg_match('/^UOO\/APP\//i', $this->matric_no)) {
            return false;
        }

        // Valid matric pattern examples:
        // 24/FAS/CSC/001
        // 25/ENG/DPCSC/112
        // 23/SCI/TRCSC/005
        $pattern = '/^\d{2}\/[A-Z]{2,5}\/(T|D|DP|DE|TR)?[A-Z]{2,5}\/\d{3}$/i';

        return preg_match($pattern, $this->matric_no) === 1;
    }

    /**
     * Determine if this student is affected by specific semester overrides.
     */
    public function isSemesterAffected($activeSemester): bool
    {
        if (! $activeSemester) {
            return false;
        }

        // Semesters should not be applicable for REGULAR and DIPLOMA programmes.
        if (in_array(strtoupper($this->programme), ['REGULAR', 'DIPLOMA'])) {
            return false;
        }

        $semesterStreams = $activeSemester->stream ?? [];
        $semesterCampuses = $activeSemester->campus_id ?? [];
        $semesterProgrammes = $activeSemester->programme ?? [];

        $isSpecificOverride = ! empty($semesterStreams) || ! empty($semesterCampuses) || ! empty($semesterProgrammes);

        if (! $isSpecificOverride) {
            return false;
        }

        $matchesStream = $this->matchesStream($semesterStreams);
        $matchesCampus = empty($semesterCampuses) || in_array($this->campus_id, $semesterCampuses);
        $matchesProgramme = empty($semesterProgrammes) || in_array($this->programme, $semesterProgrammes);

        return $matchesStream && $matchesCampus && $matchesProgramme;
    }

    public function matchesStream($semesterStreams): bool
    {
        return empty($semesterStreams) || in_array((string) $this->stream, $semesterStreams);
    }

    // 🔹 Graduation & Duration Logic

    public function programmeDuration()
    {
        return ProgrammeDuration::where('department_id', $this->department_id)
            ->where('programme', $this->programme)
            ->first();
    }

    public function getMaxLevel(): int
    {
        return $this->programmeDuration()?->max_level ?? 400;
    }

    public function hasGraduated(): bool
    {
        if ($this->status == self::STATUS_GRADUATED) {
            return true;
        }

        return $this->level > $this->getMaxLevel();
    }

    // 🔹 Financial / Debt Logic

    /**
     * Calculate the total outstanding debt for this student since their admission session.
     */
    public function getOutstandingDebt(): float
    {
        $admissionSession = $this->admission_session;
        if (! $admissionSession) {
            return 0.0;
        }

        $currentSessionName = activeSession()->name ?? null;

        // Get all academic sessions from admission until PREVIOUS session
        $query = AcademicSession::where('name', '>=', $admissionSession);
        
        if ($currentSessionName) {
            $query->where('name', '<', $currentSessionName);
        }

        $sessions = $query->orderBy('name', 'asc')->pluck('name');

        $totalDebt = 0.0;

        foreach ($sessions as $sessionName) {
            // Get required fees for this session based on matching criteria
            $requiredFees = PaymentSetting::getFeesForStudent($this, $sessionName)->sum('amount');
            
            $paidAmount = Transaction::where('user_id', $this->user_id)
                ->where('session', $sessionName)
                ->where('payment_status', 1)
                ->sum('amount');

            if ($paidAmount < $requiredFees) {
                $totalDebt += ($requiredFees - $paidAmount);
            }
        }

        return $totalDebt;
    }

    public function isBlockedByDebt(): bool
    {
        return $this->getOutstandingDebt() > 0;
    }
}
