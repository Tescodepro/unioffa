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
        // Create default accounts
        // $this->createAccount('administrator', [
        //     'first_name' => 'Admin',
        //     'last_name' => 'Officer',
        //     'email' => 'admin@unioffa.edu.ng',
        //     'phone' => '+234900988888',
        //     'username' => 'ADMIN001',
        //     'password' => 'Admin@Unioffa123',
        // ]);

        // $this->createAccount('registrar', [
        //     'first_name' => 'Registrar',
        //     'last_name' => 'Officer',
        //     'email' => 'registrar@unioffa.edu.ng',
        //     'phone' => '+234901000111',
        //     'username' => 'REG001',
        //     'password' => 'Registrar@UniOffa123',
        // ]);

        // $this->createAccount('vice-chancellor', [
        //     'first_name' => 'Vice',
        //     'last_name' => 'Chancellor',
        //     'email' => 'vc@unioffa.edu.ng',
        //     'phone' => '+234902222333',
        //     'username' => 'VC001',
        //     'password' => 'Vc@Unioffa123',
        // ]);

        $this->createAccount('bursary', [
            'first_name' => 'Bursary',
            'last_name' => 'Officer',
            'email' => 'bursary@unioffa.edu.ng',
            'phone' => '+234903333444',
            'username' => 'BURSARY001',
            'password' => 'Bursary@Unioffa123',
        ]);
    }

    private function createAccount(string $userTypeName, array $data): void
    {
        $userTypeId = $this->getUserTypeId($userTypeName);

        if (! $userTypeId) {
            $this->command->warn("⚠️ UserType '{$userTypeName}' not found. Please run UserTypeSeeder first.");
            return;
        }

        User::updateOrCreate(
            ['email' => $data['email']],
            [
                'id' => (string) Str::uuid(),
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'username' => $data['username'],
                'registration_no' => null,
                'password' => Hash::make($data['password']),
                'user_type_id' => $userTypeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function getUserTypeId(string $name): ?string
    {
        return UserType::where('name', $name)->value('id');
    }
}
