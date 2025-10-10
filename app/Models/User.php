<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'profile_picture',
        'date_of_birth',
        'state_of_origin',
        'lga',
        'nationality',
        'religion',
        'username',
        'registration_no',
        'password',
        'user_type_id',
        'campus_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function hasUserType(array|string $types): bool
    {
        $typeName = $this->userType?->name;

        if (is_array($types)) {
            return in_array($typeName, $types);
        }

        return $typeName === $types;
    }


    public function admissionList()
    {
        return $this->hasOne(AdmissionList::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function userApplications()
    {
        return $this->hasMany(UserApplications::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function applications()
    {
        return $this->hasMany(UserApplications::class, 'user_id');
    }

    // In User model
    public function courseOfStudy()
    {
        return $this->hasOne(CourseOfStudy::class, 'user_id');
    }

    public function applicationSetting()
    {
        return $this->belongsTo(ApplicationSetting::class, 'application_setting_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class); // If extra student info is in 'students' table
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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
}
