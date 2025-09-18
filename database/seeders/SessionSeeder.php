<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = [
            [
                'name' => '2023/2024',
                'status' => '0',
                'status_upload_result' => '0',
            ],
            [
                'name' => '2024/2025',
                'status' => '0',
                'status_upload_result' => '0',
            ],
            [
                'name' => '2025/2026',
                'status' => '1',
                'status_upload_result' => '0',
            ],
        ];

        foreach ($sessions as $session) {
            DB::table('academic_sessions')->updateOrInsert(
                ['name' => $session['name']],
                [
                    'id' => Str::uuid(),
                    'name' => $session['name'],
                    'status' => $session['status'],
                    'status_upload_result' => $session['status_upload_result'],
                    'lecturar_ids' => '[]',
                    'students_ids' => '[]',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
