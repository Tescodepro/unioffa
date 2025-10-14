<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faculty extends Model
{
    use HasFactory;

    public $incrementing = false; // UUIDs, not auto-increment

    protected $keyType = 'string';

    protected $fillable = [
        'faculty_name',
        'faculty_code',
        'description',
    ];

    // Auto-generate UUID
    protected static function booted()
    {
        static::creating(function ($faculty) {
            if (empty($faculty->id)) {
                $faculty->id = (string) Str::uuid();
            }
        });
    }

    // 🔹 Relationships
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    // app/Models/Faculty.php
    public function students()
    {
        return $this->hasMany(Student::class);
    }

}
