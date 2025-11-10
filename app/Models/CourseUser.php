<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseUser extends Model
{
    use HasFactory;

    protected $table = 'course_user';
    protected $fillable = ['course_id', 'user_id'];

    public $incrementing = false; // since it's UUID
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = Str::uuid();
            }
        });
    }
}
