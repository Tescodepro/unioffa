<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $semesters = [
            [
                'name' => 'First Semester',
                'code' => '1st',
                'status' => '1',
                'status_upload_result' => '1',
            ],
            [
                'name' => 'Second Semester',
                'code' => '2nd',
                'status' => '0',
                'status_upload_result' => '0',
            ],
            [
                'name' => 'Summer Semester',
                'code' => '3nd',
                'status' => '0',
                'status_upload_result' => '0',
            ],
        ];

        foreach ($semesters as $semester) {
            DB::table('academic_semesters')->updateOrInsert(
                ['code' => $semester['code']],
                [
                    'id' => Str::uuid(),
                    'name' => $semester['name'],
                    'code' => $semester['code'],
                    'status' => $semester['status'],
                    'status_upload_result' => $semester['status_upload_result'],
                    'lecturar_ids' => '[]',
                    'students_ids' => '[]',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
