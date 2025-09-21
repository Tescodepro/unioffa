<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get the "administrator" user type
        $adminType = UserType::where('name', 'administrator')->first();

        if (! $adminType) {
            $this->command->warn('⚠️ UserType "administrator" not found. Please run UserTypeSeeder first.');

            return;
        }

        // Seed a single demo administrator
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // unique check
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'Kamaldeeen',
                'middle_name' => null,
                'last_name' => 'Gbolagade',
                'email' => 'admin@unioffa.edu.ng',
                'phone' => '+2349036154339',
                'username' => 'ADMIN001',
                'registration_no' => null, // admins usually don’t have matric no
                'password' => Hash::make('Admin@123'), // default password
                'user_type_id' => $adminType->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
