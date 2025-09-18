<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\UserType;
use App\Models\Department;
use App\Models\Campus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Get student user type
        $studentType = UserType::where('name', 'student')->first();
        if (!$studentType) {
            $this->command->error(" User type 'student' not found. Please seed user_types first.");
            return;
        }

        // 🔹 Pick a campus
        $campus = Campus::first();
        if (!$campus) {
            $this->command->error(" No campus found. Please seed campuses first.");
            return;
        }

        // 🔹 Helper: generate matric numbers
        $generateMatric = function ($facultyCode, $deptCode, $serial) {
            $year = now()->format('y'); // e.g. 24 for 2024
            return sprintf("%s/%s/%s/%03d", $year, $facultyCode, $deptCode, $serial);
        };

        // 🔹 Define students
        $students = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Michael',
                'email' => 'john.doe@example.com',
                'phone' => '08011111111',
                'department_code' => 'CSC',
                'programme' => 'TOPUP',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => '2024/2025',
                'serial' => 1,
                'sex' => 'male'
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'middle_name' => null,
                'email' => 'jane.smith@example.com',
                'phone' => '08022222222',
                'department_code' => 'ACC',
                'programme' => 'DE',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => '2023/2024',
                'serial' => 2,
                'sex' => 'female'
            ],
        ];

        foreach ($students as $studentData) {
            // 🔹 Find department
            $department = Department::where('department_code', $studentData['department_code'])->first();

            if (!$department) {
                $this->command->warn(" Department {$studentData['department_code']} not found. Skipping...");
                continue;
            }

            // 🔹 Generate matric number
            $matricNo = $generateMatric($department->faculty->faculty_code, $department->department_code, $studentData['serial']);

            // 🔹 Create user
            $user = User::create([
                'id' => Str::uuid(),
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'middle_name' => $studentData['middle_name'],
                'email' => $studentData['email'],
                'phone' => $studentData['phone'],
                'username' => $matricNo,
                'registration_no' => null,
                'password' => Hash::make('password123'),
                'user_type_id' => $studentType->id,
                
            ]);

            // 🔹 Create student record
            Student::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'campus_id' => $campus->id,
                'department_id' => $department->id,
                'matric_no' => $matricNo,
                'programme' => $studentData['programme'],
                'entry_mode' => $studentData['entry_mode'],
                'level' => $studentData['level'],
                'admission_session' => $studentData['admission_session'],
                'admission_date' => now(),
                'status' => 1,
                'sex' => $studentData['sex'],
            ]);
        }

        $this->command->info("✅ " . count($students) . " students seeded successfully.");
    }
}
