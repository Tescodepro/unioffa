<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Olevel extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'user_application_id',
        'exam_type',
        'exam_year',
        'subjects',
    ];

    protected $casts = [
        'subjects' => 'array', // 👈 this makes subjects usable as array
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function application() {
        return $this->belongsTo(UserApplications::class, 'user_application_id');
    }

    public function userApplication()
    {
        return $this->belongsTo(UserApplications::class, 'user_application_id');
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
