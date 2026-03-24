<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseRegistrationSetting extends Model
{
    use HasFactory;

    public $incrementing = false; // UUID

    protected $keyType = 'string';

    protected $fillable = [
        'campus_id',
        'entry_mode',
        'semester',
        'session',
        'closing_date',
        'late_registration_fee',
    ];

    protected $casts = [
        'entry_mode' => 'array',
        'closing_date' => 'datetime',
        'late_registration_fee' => 'decimal:2',
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

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public static function getActiveForStudent($student, $session, $semester)
    {
        return self::where('campus_id', $student->campus_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('entry_mode')
                  ->orWhereJsonContains('entry_mode', $student->entry_mode);
            })
            ->where(function ($q) use ($session) {
                $q->whereNull('session')
                  ->orWhere('session', $session);
            })
            ->where(function ($q) use ($semester) {
                $q->whereNull('semester')
                  ->orWhere('semester', $semester);
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
