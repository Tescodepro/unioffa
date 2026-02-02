<?php

namespace Database\Seeders;

use App\Models\GradingSystem;
use Illuminate\Database\Seeder;

class GradingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            [
                'grade' => 'A',
                'min_score' => 70,
                'max_score' => 100,
                'point' => 5.00,
                'description' => 'Excellent'
            ],
            [
                'grade' => 'B',
                'min_score' => 60,
                'max_score' => 69,
                'point' => 4.00,
                'description' => 'Very Good'
            ],
            [
                'grade' => 'C',
                'min_score' => 50,
                'max_score' => 59,
                'point' => 3.00,
                'description' => 'Good'
            ],
            [
                'grade' => 'D',
                'min_score' => 45,
                'max_score' => 49,
                'point' => 2.00,
                'description' => 'Fair'
            ],
            [
                'grade' => 'E',
                'min_score' => 40,
                'max_score' => 44,
                'point' => 1.00,
                'description' => 'Pass'
            ],
            [
                'grade' => 'F',
                'min_score' => 0,
                'max_score' => 39,
                'point' => 0.00,
                'description' => 'Fail'
            ],
        ];

        foreach ($grades as $grade) {
            GradingSystem::updateOrCreate(
                ['grade' => $grade['grade']],
                $grade
            );
        }
    }
}
