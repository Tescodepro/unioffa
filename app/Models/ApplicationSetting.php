<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApplicationSetting extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'application_code',
        'modules_enable',
        'application_fee',
        'admission_duration',
        'acceptance_fee',
        'academic_session',
        'enabled',
        'description',
    ];

    protected $casts = [
        'modules_enable' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'application_setting_id');
    }

    public function userApplications()
    {
        return $this->hasMany(UserApplications::class, 'application_setting_id');
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
