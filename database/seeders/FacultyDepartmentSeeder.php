<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FacultyDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $facultiesData = [
            'FSC' => [
                'name' => 'Faculty of Science and Computing',
                'description' => 'Faculty of Science and Computing description',
                'departments' => [
                    'Computer Science' => 'CSC',
                    'Cybersecurity' => 'CYS',
                    'Software Engineering' => 'SWE',
                    'Microbiology' => 'MCB',
                    'Physics with Electronics' => 'PYE',
                    'Biology' => 'BIO',
                ],
            ],
            'FMSS' => [
                'name' => 'Faculty of Management and Social Sciences',
                'description' => 'Faculty of Management and Social Sciences description',
                'departments' => [
                    'Accounting' => 'ACC',
                    'Business Administration' => 'BUS',
                    'Economics' => 'ECO',
                    'Mass Communication' => 'MAC',
                    'Political Science' => 'POL',
                ],
            ],
            'FE' => [
                'name' => 'Faculty of Education',
                'description' => 'Faculty of Education description',
                'departments' => [
                    'Economics Education' => 'ECE',
                    'Social Studies Education' => 'SSE',
                    'Vocational and Technical Education' => 'VTE',
                ],
            ],
        ];

        // Fetch existing faculties to reduce queries
        $existingFaculties = DB::table('faculties')->pluck('id', 'faculty_code')->toArray();

        foreach ($facultiesData as $code => $faculty) {
            $facultyId = $existingFaculties[$code] ?? Str::uuid();

            DB::table('faculties')->updateOrInsert(
                ['faculty_code' => $code],
                [
                    'id' => $facultyId,
                    'faculty_name' => $faculty['name'],
                    'description' => $faculty['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Fetch existing departments for this faculty
            $existingDepartments = DB::table('departments')
                ->where('faculty_id', $facultyId)
                ->pluck('id', 'department_code')
                ->toArray();

            foreach ($faculty['departments'] as $deptName => $deptCode) {
                $deptId = $existingDepartments[$deptCode] ?? Str::uuid();

                DB::table('departments')->updateOrInsert(
                    ['department_code' => $deptCode, 'faculty_id' => $facultyId],
                    [
                        'id' => $deptId,
                        'department_name' => $deptName,
                        'department_description' => $deptName . ' description',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
