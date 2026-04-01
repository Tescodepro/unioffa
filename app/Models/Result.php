<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory, HasUuids;

    /**
     * Indicates the primary key is not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'id',
        'student_id',
        'matric_no',
        'course_id',
        'course_code',
        'course_title',
        'course_unit',
        'session',
        'semester',
        'ca',
        'exam',
        'total',
        'grade',
        'remark',
        'status',
        'uploaded_by',
    ];

    /**
     * Relationships
     */

    // Student that owns the result
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    // The course this result belongs to
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // The staff or lecturer who uploaded the result
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // The user (student) this result belongs to, linked via matric_no
    public function user()
    {
        return $this->belongsTo(User::class, 'matric_no', 'username');
    }

    /**
     * Accessors & Mutators
     */

    // Automatically compute total if not manually set
    public function getTotalAttribute($value)
    {
        if ($value === null && $this->ca !== null && $this->exam !== null) {
            return (float) $this->ca + (float) $this->exam;
        }

        return $value;
    }

    /**
     * Helper Methods
     */

    // Compute grade based on total score using GradingSystem
    public function computeGrade()
    {
        $score = $this->total ?? (($this->ca ?? 0) + ($this->exam ?? 0));

        // Get point and grade from GradingSystem
        $grading = GradingSystem::where('min_score', '<=', $score)
            ->orderBy('min_score', 'desc')
            ->first();

        if ($grading) {
            return [$grading->grade, $grading->description ?? ''];
        }

        return ['F', 'Fail'];
    }

    /**
     * Boot logic for automatic grading.
     */
    protected static function booted()
    {
        static::creating(function ($result) {
            if (empty($result->id)) {
                $result->id = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::saving(function ($result) {
            if ($result->ca !== null && $result->exam !== null) {
                $result->total = $result->ca + $result->exam;
                [$grade, $remark] = $result->computeGrade();
                $result->grade = $grade;
                $result->remark = $remark;
            }
        });
    }
}
