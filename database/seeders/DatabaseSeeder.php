<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Faker\Provider\ar_EG\Payment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserTypesSeeder::class);
        $this->call(ApplicationSettingsSeeder::class);
        $this->call(FacultyDepartmentSeeder::class);
        $this->call(PaymentGateWaySettingsSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CampusSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(SemesterSeeder::class);
        $this->call(SessionSeeder::class);
        $this->call(PaymentSettingsSeeder::class);


    }
}
