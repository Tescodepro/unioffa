<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApplicationTypeAssignment extends Model
{
    protected $fillable = ['user_id', 'application_setting_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applicationSetting()
    {
        return $this->belongsTo(ApplicationSetting::class);
    }
}
