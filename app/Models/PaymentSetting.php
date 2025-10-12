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
        'session',
        'installmental_allow_status',
        'number_of_instalment',
        'list_instalment_percentage',
    ];

    protected $casts = [
        'level' => 'array',
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

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
