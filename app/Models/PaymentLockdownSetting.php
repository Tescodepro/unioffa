<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentLockdownSetting extends Model
{
    use HasFactory;

    public $incrementing = false; // UUID

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'payment_types',
        'deadline',
        'campus_ids',
        'faculty_ids',
        'department_ids',
        'levels',
        'admission_sessions',
        'genders',
        'entry_modes',
        'programmes',
        'is_active',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'payment_types' => 'array',
        'campus_ids' => 'array',
        'faculty_ids' => 'array',
        'department_ids' => 'array',
        'levels' => 'array',
        'admission_sessions' => 'array',
        'genders' => 'array',
        'entry_modes' => 'array',
        'programmes' => 'array',
        'is_active' => 'boolean',
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

    /**
     * Retrieve the active lockdown matching the student and optional fee type.
     */
    public static function getLockdownForStudent(Student $student, ?string $paymentType = null): ?self
    {
        $lockdowns = self::where('is_active', true)
            ->orderBy('deadline', 'asc')
            ->get();

        foreach ($lockdowns as $lockdown) {
            /** @var self $lockdown */
            // 1. Payment Type matching
            if ($paymentType !== null && ! empty($lockdown->payment_types)) {
                $lockdownPaymentTypes = array_map('strtolower', $lockdown->payment_types);
                if (! in_array(strtolower($paymentType), $lockdownPaymentTypes)) {
                    continue;
                }
            }

            // 2. Campus matching
            if (! empty($lockdown->campus_ids)) {
                if (! in_array($student->campus_id, $lockdown->campus_ids)) {
                    continue;
                }
            }

            // 3. Faculty matching
            if (! empty($lockdown->faculty_ids)) {
                $studentFacultyId = $student->department->faculty_id ?? null;
                if (! $studentFacultyId || ! in_array($studentFacultyId, $lockdown->faculty_ids)) {
                    continue;
                }
            }

            // 4. Department matching
            if (! empty($lockdown->department_ids)) {
                if (! in_array($student->department_id, $lockdown->department_ids)) {
                    continue;
                }
            }

            // 5. Level matching
            if (! empty($lockdown->levels)) {
                if (! in_array((string) $student->level, $lockdown->levels) && ! in_array((int) $student->level, $lockdown->levels)) {
                    continue;
                }
            }

            // 6. Admission Session matching
            if (! empty($lockdown->admission_sessions)) {
                if (! in_array($student->admission_session, $lockdown->admission_sessions)) {
                    continue;
                }
            }

            // 7. Gender matching
            if (! empty($lockdown->genders)) {
                $studentSex = strtolower($student->sex ?? '');
                $lockdownGenders = array_map('strtolower', $lockdown->genders);
                if (! in_array($studentSex, $lockdownGenders)) {
                    continue;
                }
            }

            // 8. Entry Mode matching
            if (! empty($lockdown->entry_modes)) {
                if (! in_array($student->entry_mode, $lockdown->entry_modes)) {
                    continue;
                }
            }

            // 9. Programme matching
            if (! empty($lockdown->programmes)) {
                if (! in_array($student->programme, $lockdown->programmes)) {
                    continue;
                }
            }

            // If we reached here, the lockdown applies to the student!
            return $lockdown;
        }

        return null;
    }
}
