<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AcademicSession extends Model
{
    use HasFactory;

    protected $table = 'academic_sessions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'status', 'status_upload_result', 'lecturar_ids', 'students_ids'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // 🔹 Scope for active session
    public function scopeActive($query)
    {
        return $query->where('status', '1')->first();
    }
}
