<?php

namespace database\seeders;

use App\Models\Department;
use App\Models\ProgrammeDuration;
use Illuminate\Database\Seeder;

class ProgrammeDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();
        $programmes = ['REGULAR', 'TOPUP', 'IDELUTME', 'IDELDE', 'DIPLOMA'];

        foreach ($departments as $dept) {
            foreach ($programmes as $prog) {
                $duration = 4;
                $maxLevel = 400;

                // Customize based on programme
                if ($prog === 'TOPUP') {
                    $duration = 3;
                } elseif ($prog === 'DIPLOMA') {
                    $duration = 2;
                    $maxLevel = 200;
                }

                // Customize based on department name/code if needed
                // e.g. Nursing or Law might be 5 years
                if (preg_match('/nursing|law|engineering/i', $dept->department_name)) {
                    $duration = 5;
                    $maxLevel = 500;
                }

                ProgrammeDuration::updateOrCreate(
                    ['department_id' => $dept->id, 'programme' => $prog],
                    ['duration' => $duration, 'max_level' => $maxLevel]
                );
            }
        }
    }
}
