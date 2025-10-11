<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdmissionList extends Model
{
    use HasFactory;

    protected $table = 'admission_lists';

    protected $fillable = [
        'user_id',
        'approved_department_id',
        'session_admitted',
        'admission_status',
    ];

    public $incrementing = false;   // UUIDs aren’t auto-incrementing

    protected $keyType = 'string';  // UUIDs are strings

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'approved_department_id');
    }

    // link to application setting
    public function applicationSetting()
    {
        return $this->belongsTo(ApplicationSetting::class);
    }

    // add admission relationship
    public function admissionList()
    {
        return $this->hasOne(AdmissionList::class, 'user_application_id', 'id');
    }

    public function userApplication()
    {
        return $this->belongsTo(UserApplications::class, 'user_id', 'user_id')
            ->where('academic_session', $this->session);
    }
}
