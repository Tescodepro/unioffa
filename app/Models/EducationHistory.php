<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class EducationHistory extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'institution_name',
        'qualification',
        'start_date',
        'end_date',
        'grade',
        'certificate_path',
        'user_application_id',
    ];
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
