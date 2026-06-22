<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummerRegistration extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;

    protected $fillable = [
        'student_id',
        'academic_session',
        'status',
        'courses',
        'summary_fee',
        'course_fee_total',
        'total_fee',
        'payment_status',
        'reason_for_increase',
    ];

    public function casts(): array
    {
        return [
            'courses' => 'array',
            'summary_fee' => 'decimal:2',
            'course_fee_total' => 'decimal:2',
            'total_fee' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
