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
        // Get the "student" user type
        $studentType = UserType::where('name', 'student')->first();

        if (!$studentType) {
            $this->command->warn('⚠️ UserType "student" not found. Please run UserTypeSeeder first.');
            return;
        }

        // Seed a single demo student
        User::updateOrCreate(
            ['email' => 'student@example.com'], // unique check
            [
                'id'              => (string) Str::uuid(),
                'first_name'      => 'Olamilekan',
                'middle_name'     => null,
                'last_name'       => 'Tesing',
                'email'           => 'student@gmail.com',
                'phone'           => '08010000002',
                'username'        => 'STU001', // matric no
                'registration_no' => 'MAT12345',
                'password'        => Hash::make('password'),
                'user_type_id'    => $studentType->id,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        );
    }
}
