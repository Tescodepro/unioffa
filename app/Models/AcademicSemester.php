<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AcademicSemester extends Model
{
    use HasFactory;

    protected $table = 'academic_semesters';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'code', 'status', 'status_upload_result', 'lecturar_ids', 'students_ids', 'stream', 'campus_id', 'programme'];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'status_upload_result' => 'boolean',
            'students_ids' => 'array',
            'lecturar_ids' => 'array',
            'stream' => 'array',
            'campus_id' => 'array',
            'programme' => 'array',
        ];
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

    // 🔹 Scope for active semester
    public function scopeActive($query)
    {
        return $query->where('status', '1')->first();
    }
}
