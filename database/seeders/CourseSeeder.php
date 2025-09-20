<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            //  Computer Science
            ['CSC101', 'Introduction to Computer Science', 3, 'C', 'CSC', 100, '1st'],
            ['CSC102', 'Programming Fundamentals', 3, 'C', 'CSC', 100, '2nd'],
            ['CSC201', 'Data Structures', 3, 'C', 'CSC', 200, '1st'],
            ['CSC202', 'Algorithms and Complexity', 3, 'C', 'CSC', 200, '2nd'],
            ['CSC301', 'Database Systems', 3, 'C', 'CSC', 300, '1st'],
            ['CSC302', 'Operating Systems', 3, 'C', 'CSC', 300, '2nd'],
            ['CSC401', 'Artificial Intelligence', 3, 'E', 'CSC', 400, '1st'],
            ['CSC402', 'Computer Networks', 3, 'C', 'CSC', 400, '2nd'],

            //  Accounting
            ['ACC101', 'Principles of Accounting I', 3, 'C', 'ACC', 100, '1st'],
            ['ACC102', 'Principles of Accounting II', 3, 'C', 'ACC', 100, '2nd'],
            ['ACC201', 'Cost Accounting', 3, 'C', 'ACC', 200, '1st'],
            ['ACC202', 'Financial Accounting', 3, 'C', 'ACC', 200, '2nd'],
            ['ACC301', 'Auditing I', 3, 'C', 'ACC', 300, '1st'],
            ['ACC302', 'Taxation', 3, 'C', 'ACC', 300, '2nd'],

            //  Economics
            ['ECO101', 'Principles of Economics I', 3, 'C', 'ECO', 100, '1st'],
            ['ECO102', 'Principles of Economics II', 3, 'C', 'ECO', 100, '2nd'],
            ['ECO201', 'Microeconomics', 3, 'C', 'ECO', 200, '1st'],
            ['ECO202', 'Macroeconomics', 3, 'C', 'ECO', 200, '2nd'],
            ['ECO301', 'Development Economics', 3, 'E', 'ECO', 300, '1st'],
            ['ECO302', 'International Trade', 3, 'C', 'ECO', 300, '2nd'],
        ];

        foreach ($courses as $course) {
            [$code, $title, $unit, $status, $deptCode, $level, $semester] = $course;

            $department = Department::where('department_code', $deptCode)->first();

            if (! $department) {
                $this->command->warn("⚠️ Department {$deptCode} not found. Skipping course {$code}.");

                continue;
            }

            DB::table('courses')->updateOrInsert(
                ['course_code' => $code],
                [
                    'id' => Str::uuid(),
                    'course_title' => $title,
                    'course_code' => $code,
                    'course_unit' => $unit,
                    'course_status' => $status,
                    'department_id' => $department->id,
                    'level' => $level,
                    'semester' => $semester,
                    'active_for_register' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ '.count($courses).' courses seeded successfully.');
    }
}
