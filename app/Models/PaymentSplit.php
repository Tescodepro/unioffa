<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentSplit extends Model
{

    use HasFactory;

    public $incrementing = false; // UUID

    protected $keyType = 'string';
    protected $fillable = [
        'name',
        'split_code',
        'payment_type',
        'student_type',
        'center',
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

}
