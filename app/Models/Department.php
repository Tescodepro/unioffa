<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory;

    public $incrementing = false; // UUIDs

    protected $keyType = 'string';

    protected $fillable = [
        'department_name',
        'department_code',
        'qualification',
        'faculty_id',
        'department_description',
    ];

    // Auto-generate UUID
    protected static function booted()
    {
        static::creating(function ($department) {
            if (empty($department->id)) {
                $department->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
