<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class Student extends Model
{
    use HasFactory;

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

    public static function generateMatricNo(string $departmentCode, int $admissionYear, string $programme): string
    {
        return DB::transaction(function () use ($departmentCode, $admissionYear, $programme) {
            // Validate programme
            $validProgrammes = ['TOPUP', 'IDELUTME', 'IDELDE', 'UTME', 'TRANSFER', 'DIPLOMA', 'DE'];
            if (! in_array($programme, $validProgrammes)) {
                Log::error('Invalid programme specified: ' . $programme);
            }

            // Modify department code based on programme
            $modifiedCode = match ($programme) {
                'TOPUP' => 'T' . $departmentCode,        // e.g., CSC -> TCSC
                'IDELUTME', 'IDELDE' => 'D' . $departmentCode, // e.g., CSC -> DCSC
                'DIPLOMA' => 'DP' . $departmentCode,     // e.g., CSC -> DPCSC
                'DE' => 'DE' . $departmentCode,          // e.g., CSC -> DECSC
                'TRANSFER' => 'TR' . $departmentCode,      // e.g., CSC -> TRCSC
                default => $departmentCode,              // UTME, Transfer: no change
            };

            // Get department and faculty
            $department = Department::where('department_code', $departmentCode)->firstOrFail();
            $faculty = $department->faculty;

            // Count students in this department + year
            $matricPattern = '^[0-9]{2}\\/[A-Za-z]{2,5}\\/(?:T|D|DP|DE|TR)?[A-Za-z]{2,5}\\/[0-9]{3}$';
            $count = self::where('department_id', $department->id)
                ->whereYear('admission_date', $admissionYear)
                 ->whereRaw('matric_no REGEXP ?', [$matricPattern])
                ->lockForUpdate()
                ->count() + 1;

            // Format year (last 2 digits only)
            $yearShort = substr((string) $admissionYear, -2); // e.g., 2023 -> "23"

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

    public static function hasMatricNumber(): bool
    {
        $user = Auth::user();
        // If user not logged in, or has no linked student record → false
        if (! $user || ! $user->student) {
            return false;
        }
        $matric_no = $user->student->matric_no;
        // Empty or null → not a real matric number
        if (empty($matric_no)) {
            return false;
        }
        // Reject application-style IDs (e.g. UOO/APP/2025/00001)
        if (preg_match('/^UOO\/APP\//i', $matric_no)) {
            return false;
        }
        // Valid matric pattern examples:
        // 24/FAS/CSC/001
        // 25/ENG/DPCSC/112
        // 23/SCI/TRCSC/005
        $pattern = '/^\d{2}\/[A-Z]{2,5}\/(T|D|DP|DE|TR)?[A-Z]{2,5}\/\d{3}$/i';

        return preg_match($pattern, $matric_no) === 1;
    }
}
