<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseOfStudy extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'user_application_id',
        'first_department_id',
        'second_department_id',

    ];

    public function firstDepartment()
    {
        return $this->belongsTo(Department::class, 'first_department_id');
    }

    public function secondDepartment()
    {
        return $this->belongsTo(Department::class, 'second_department_id');
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
