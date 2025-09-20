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

        // Start counting from the current number of users
        $count = User::where('user_type_id', $userType->id)
            ->whereYear('created_at', $year)
            ->count() + 1;

        do {
            $uniqueId = 'UOO/'.$prefix.'/'.$year.'/'.str_pad($count, 5, '0', STR_PAD_LEFT);
            $count++; // 🔹 increment until we find a free one
        } while (User::where('username', $uniqueId)->exists());

        return $uniqueId;
    }
}
