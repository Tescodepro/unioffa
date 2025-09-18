<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'user_application_id',
        'date_of_birth',
        'gender',
        'address',
        'state_of_origin',
        'nationality'
    ];
    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
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
