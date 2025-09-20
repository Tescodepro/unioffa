<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Faculty;
use App\Models\Department;

class PaymentSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settingsData = [
            // Tuition (general)
            [
                'all' => true,
                'faculty_code' => null,
                'department_code' => null,
                'level' => null,
                'sex' => null, // Applies to all
                'matric_number' => null,
                'payment_type' => 'tuition',
                'student_type'=> null,
                'amount' => 220000,
                'description' => 'Tuition fee for 200 level FSC students',
            ],

            // Hostel (male only)
            [
                'faculty_code' => null,
                'department_code' => null,
                'level' => null,
                'sex' => 'male', // Sex-specific payment
                'matric_number' => null,
                'payment_type' => 'hostel',
                'amount' => 50000,
                'description' => 'Hostel fee for male students in Accounting department',
            ],

            // Hostel (female only)
            [
                'faculty_code' => null,
                'department_code' => null,
                'level' => null,
                'sex' => 'female', // Sex-specific payment
                'matric_number' => null,
                'payment_type' => 'hostel',
                'amount' => 45000,
                'description' => 'Hostel fee for female students in Accounting department',
            ],
        ];

        foreach ($settingsData as $data) {
            // Find faculty if code provided
            $facultyId = null;
            if (!empty($data['faculty_code'])) {
                $faculty = Faculty::where('faculty_code', $data['faculty_code'])->first();
                if (!$faculty) {
                    $this->command->warn("Faculty {$data['faculty_code']} not found. Skipping...");
                    continue;
                }
                $facultyId = $faculty->id;
            }

            // Find department if code provided
            $departmentId = null;
            if (!empty($data['department_code'])) {
                $department = Department::where('department_code', $data['department_code'])
                    ->where('faculty_id', $facultyId)
                    ->first();

                if (!$department) {
                    $this->command->warn("Department {$data['department_code']} not found. Skipping...");
                    continue;
                }
                $departmentId = $department->id;
            }

            // Insert payment setting
            DB::table('payment_settings')->insert([
                'id' => Str::uuid(),
                'faculty_id' => $facultyId,
                'department_id' => $departmentId,
                'level' => $data['level'],
                'sex' => $data['sex'],
                'matric_number' => $data['matric_number'],
                'payment_type' => $data['payment_type'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}