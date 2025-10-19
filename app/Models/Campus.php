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
        'slug',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // return the campus id and slug when campus name as a single value
    public static function getCampusDetail($value)
    {
        // Try to find campus by id or name
        $campus = static::where('id', $value)
            ->orWhere('name', $value)
            ->first();

        return $campus; // return the model itself (can be null)
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
