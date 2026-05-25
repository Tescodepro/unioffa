<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgrammeDuration extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'department_id',
        'programme',
        'duration',
        'max_level',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
