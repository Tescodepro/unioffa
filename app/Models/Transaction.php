<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'id',
        'description',
        'refernce_number',
        'amount',
        'payment_status',
        'payment_type',
        'payment_method',
        'session',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'manual';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)); // 6-char random

        return "{$prefix}-{$date}-{$random}";
    }


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
