<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    public $incrementing = false; // since we're using UUID

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'course_title',
        'course_code',
        'course_unit',
        'course_status',
        'department_id',
        'level',
        'semester',
        'active_for_register',
        'other_departments',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'other_departments' => 'array',
    ];

    // 🔹 Auto-generate UUID on create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // 🔹 Relationships
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Accessor for full display name
    public function getFullNameAttribute()
    {
        return "{$this->course_code} - {$this->course_title}";
    }

    public function lecturers()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }
}
