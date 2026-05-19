<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;

class UniqueIdService
{
    public function generate(string $userTypeName): string
    {
        $userType = UserType::where('name', $userTypeName)->first();

        if (! $userType) {
            throw new \Exception("Invalid user type: {$userTypeName}");
        }

        $prefix = strtoupper(substr($userType->name, 0, 3));
        $year = now()->format('Y');

        // If the user is an applicant, use the active academic session year instead of the calendar year
        if ($userTypeName === 'applicant') {
            $prefix = 'REG'; // Use REG instead of APP to start fresh numbering
            $activeSetting = \App\Models\ApplicationSetting::where('enabled', true)->latest()->first();
            if ($activeSetting && $activeSetting->academic_session) {
                // Extracts "2025" from "2025/2026"
                $year = explode('/', $activeSetting->academic_session)[0];
            }
        }

        // Start counting based on the username prefix instead of created_at to avoid resetting counts across calendar years
        $count = User::where('user_type_id', $userType->id)
            ->where('username', 'LIKE', "UOO/{$prefix}/{$year}/%")
            ->count() + 1;

        do {
            $uniqueId = 'UOO/'.$prefix.'/'.$year.'/'.str_pad($count, 5, '0', STR_PAD_LEFT);
            $count++; // 🔹 increment until we find a free one
        } while (User::where('username', $uniqueId)->exists());

        return $uniqueId;
    }
}
