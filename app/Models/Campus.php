<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campus extends Model
{
    use HasFactory;

    // UUID instead of auto-increment
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'address',
        'phone_number',
        'email',
        'direction',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'campus_id', 'id');
    }

    // Automatically generate UUID on create
    protected static function booted()
    {
        static::creating(function ($campus) {
            if (empty($campus->id)) {
                $campus->id = (string) Str::uuid();
            }
        });
    }
}
