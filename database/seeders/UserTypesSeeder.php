<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // Import Str

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $types = ['applicant', 'student', 'lecturer', 'hod', 'dean', 'registrar', 'administrator','vice-chancellor', 'ict'];
        $types = ['dean'];

        foreach ($types as $type) {
            UserType::updateOrInsert(
                ['name' => $type],
                [
                    'id' => (string) Str::uuid(), // 🔹 Generate UUID for new records
                    'name' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
