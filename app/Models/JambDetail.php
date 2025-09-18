<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JambDetail extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'registration_number',
        'jamb_type', // 'utme' or 'direct_entry
        'exam_year',
        'score',
        'subject_scores',
        'user_application_id'
    ];

    protected $casts = [
        'subject_scores' => 'array',
    ];

    public function userApplication()
    {
        return $this->belongsTo(UserApplications::class, 'user_application_id');
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
}
