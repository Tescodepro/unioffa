<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Staff extends Model
{
    use HasFactory;

    public $incrementing = false;   // ✅ Disable auto-increment
    protected $keyType = 'string';  // ✅ UUID is a string

    protected $fillable = [
        'user_id',
        'faculty_id',
        'department_id',
        'staff_no',
        'first_name',
        'last_name',
        'other_name',
        'email',
        'phone',
        'designation',
        'status',
        'date_of_employment',
    ];

    protected static function boot()
    {
        parent::boot();

        // ✅ Automatically generate UUID when creating
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** Relationships **/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /** Accessor for full name **/
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->other_name} {$this->last_name}");
    }
}
