<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'state_id',
        'lga_id',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'unique_code',
        'referrals_count',
    ];

    /**
     * Get the state associated with the application.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the LGA associated with the application.
     */
    public function lga()
    {
        return $this->belongsTo(Lga::class);
    }

    /**
     * Get the full name of the applicant.
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;

        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }

        $name .= ' ' . $this->last_name;

        return $name;
    }
}
