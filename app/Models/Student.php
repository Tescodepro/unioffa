<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'profile_picture',
        'department_id',
        'level',
        'sex',
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

    public static function generateMatricNo(string $departmentCode, int $admissionYear): string
    {
        // Get department and faculty
        $department = Department::where('department_code', $departmentCode)->firstOrFail();
        $faculty = $department->faculty;

        // Count students in this department + year
        $count = self::where('department_id', $department->id)
            ->whereYear('admission_date', $admissionYear)
            ->count() + 1;

        // Format year (last 2 digits only)
        $yearShort = substr((string) $admissionYear, -2);

        // Format sequence (3 digits: 001, 002, etc.)
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);

        // Return matric number
        return strtoupper("{$yearShort}/{$faculty->faculty_code}/{$department->department_code}/{$sequence}");
    }
}
