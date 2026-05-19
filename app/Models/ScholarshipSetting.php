<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipSetting extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'academic_session',
        'application_type',
        'min_jamb_score',
        'form_fields',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'form_fields' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function applications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }
}
