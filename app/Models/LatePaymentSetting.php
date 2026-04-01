<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LatePaymentSetting extends Model
{
    use HasFactory;

    public $incrementing = false; // UUID

    protected $keyType = 'string';

    protected $fillable = [
        'campus_id',
        'payment_type',
        'entry_mode',
        'semester',
        'session',
        'closing_date',
        'late_fee_amount',
        'increment_amount',
        'increment_date',
    ];

    protected $casts = [
        'entry_mode' => 'array',
        'closing_date' => 'datetime',
        'late_fee_amount' => 'decimal:2',
        'increment_amount' => 'decimal:2',
        'increment_date' => 'datetime',
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

    public static function getActiveForStudent($student, $session, $semester, $paymentType)
    {
        return self::where('campus_id', $student->campus_id)
            ->where('payment_type', $paymentType)
            ->where(function ($q) use ($student) {
                $q->whereNull('entry_mode')
                    ->orWhere('entry_mode', '[]')
                    ->orWhereJsonContains('entry_mode', $student->entry_mode);
            })
            ->where(function ($q) use ($session) {
                $q->whereNull('session')
                    ->orWhere('session', '[]') // Might happen through UI
                    ->orWhere('session', $session);
            })
            ->where(function ($q) use ($semester) {
                $q->whereNull('semester')
                    ->orWhere('semester', '[]') // Might happen through UI
                    ->orWhere('semester', $semester);
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
