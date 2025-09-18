<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserType;
use Illuminate\Support\Str; // Import Str                                                                                                   


class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $types = ['applicant', 'student', 'lecturer', 'hod', 'registrar', 'administrator'];

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
