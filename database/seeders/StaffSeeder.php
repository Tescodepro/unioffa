<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Staff;
use App\Models\Faculty;
use App\Models\Department;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🔹 Example: Dean
        $deanUser = User::where('email', 'dean@unioffa.edu.ng')->first();
        $faculty = Faculty::where('faculty_code', 'FSC')->first(); // replace with correct faculty

        if ($deanUser && !$deanUser->staff) {
            Staff::create([
                'id' => Str::uuid(),
                'user_id' => $deanUser->id,
                'faculty_id' => $faculty->id,
                'department_id' => null, // Dean may not belong to a specific department
                'staff_no' => 'DEAN001',
                'status' => 'active',
                'date_of_employment' => now(),
            ]);
        }

        // 🔹 Example: HOD
        $hodUser = User::where('email', 'hod@unioffa.edu.ng')->first();
        $department = Department::where('department_code', 'CSC')->first(); // replace with correct department

        if ($hodUser && !$hodUser->staff) {
            Staff::create([
                'id' => Str::uuid(),
                'user_id' => $hodUser->id,
                'faculty_id' => $faculty->id,
                'department_id' => $department->id,
                'staff_no' => 'HOD001',
                'status' => 'active',
                'date_of_employment' => now(),
            ]);
        }

        // 🔹 Example: Lecturer
        $lecturerUser = User::where('email', 'lecturer@unioffa.edu.ng')->first();

        if ($lecturerUser && !$lecturerUser->staff) {
            Staff::create([
                'id' => Str::uuid(),
                'user_id' => $lecturerUser->id,
                'faculty_id' => $faculty->id,
                'department_id' => $department->id,
                'staff_no' => 'LEC001',
                'status' => 'active',
                'date_of_employment' => now(),
            ]);
        }

        $this->command->info('Staff records created for Dean, HOD, and Lecturer.');
    }
}
