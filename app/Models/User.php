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
        'campus_id',
        'referee_code',
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

    public function staff()
    {
        return $this->hasOne(\App\Models\Staff::class, 'user_id', 'id');
    }

    // In App/Models/User.php

    public function results()
    {
        // usage: hasMany(RelatedModel, ForeignKeyOnResult, LocalKeyOnUser)
        return $this->hasMany(Result::class, 'matric_no', 'username');
    }

    public function studentProfile()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    public function isAssignedToCourse($courseId)
    {
        return $this->courses()->where('course_id', $courseId)->exists();
    }

    public function hasRole($roleName)
    {
        return $this->userType->name === $roleName;
    }

    public function hasAnyRole(array $roleNames)
    {
        return in_array($this->userType->name, $roleNames);
    }

    public function hasPermission($permission)
    {
        $this->loadMissing('userType.permissions');
        return $this->userType?->permissions->contains('identifier', $permission) ?? false;
    }

    public function canAccessRoute(string $routeName): bool
    {
        $permissions = \Illuminate\Support\Facades\Cache::rememberForever('route_permissions_map', function () {
            return \App\Models\RoutePermission::pluck('permission_identifier', 'route_name')->all();
        });

        $requiredPermission = $permissions[$routeName] ?? null;

        if ($requiredPermission) {
            return $this->hasPermission($requiredPermission);
        }

        return true; // If no permission is mapped, assume allowed or handle accordingly
    }

    /**
     * Application types (entry modes) assigned to this programme-director.
     */
    public function assignedApplicationTypes()
    {
        return $this->belongsToMany(
            ApplicationSetting::class,
            'user_application_type_assignments',
            'user_id',
            'application_setting_id'
        );
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
