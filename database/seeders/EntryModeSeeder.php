<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntryMode;

class EntryModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modes = [
            ['name' => 'UTME', 'code' => 'UTME', 'student_type' => 'REGULAR', 'matric_prefix' => '', 'default_level' => '100'],
            ['name' => 'Direct Entry', 'code' => 'DE', 'student_type' => 'REGULAR', 'matric_prefix' => 'DE', 'default_level' => '200'],
            ['name' => 'Top Up', 'code' => 'TOPUP', 'student_type' => 'TOPUP', 'matric_prefix' => 'T', 'default_level' => '200'],
            ['name' => 'Transfer', 'code' => 'TRANSFER', 'student_type' => 'REGULAR', 'matric_prefix' => 'TR', 'default_level' => '200'],
            ['name' => 'IDEL UTME', 'code' => 'IDELUTME', 'student_type' => 'IDELUTME', 'matric_prefix' => 'D', 'default_level' => '100'],
            ['name' => 'IDEL DE', 'code' => 'IDELDE', 'student_type' => 'IDELDE', 'matric_prefix' => 'D', 'default_level' => '200'],
            ['name' => 'Diploma', 'code' => 'DIPLOMA', 'student_type' => 'DIPLOMA', 'matric_prefix' => 'DP', 'default_level' => '100'],
        ];

        foreach ($modes as $mode) {
            EntryMode::updateOrCreate(
                ['code' => $mode['code']],
                [
                    'name' => $mode['name'],
                    'student_type' => $mode['student_type'],
                    'matric_prefix' => $mode['matric_prefix'],
                    'default_level' => $mode['default_level'],
                ]
            );
        }
    }
}
