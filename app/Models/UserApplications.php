<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserApplications extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    

    protected $fillable = [
        'id',
        'user_id',
        'application_setting_id',
        'academic_session',
        'submitted_by',
        'remarks',
        'is_approved'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function applicationSetting()
    {
        return $this->belongsTo(ApplicationSetting::class, 'application_setting_id');
    }

    // relationships to modules
     public function profile()
    {
        return $this->hasOne(Profile::class, 'user_application_id');
    }

    public function olevels()
    {
        return $this->hasMany(Olevel::class, 'user_application_id');
    }

    public function jambDetail()
    {
        return $this->hasOne(JambDetail::class, 'user_application_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_application_id');
    }

    public function educationHistories()
    {
        return $this->hasMany(EducationHistory::class, 'user_application_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'session', 'academic_session')
                    ->where('user_id', Auth::id())
                    ->whereIn('payment_type', ['application', 'acceptance']);
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
