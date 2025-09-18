<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $campuses = [
            [
                'name'         => 'Main Campus',
                'address'      => '123 University Ave, City Center',
                'phone_number' => '+2348012345678',
                'email'        => 'maincampus@university.edu',
                'direction'    => 'Located near the central library',
            ],
            [
                'name'         => 'Science Campus',
                'address'      => '45 Research Blvd, Innovation District',
                'phone_number' => '+2348098765432',
                'email'        => 'sciencecampus@university.edu',
                'direction'    => 'Behind the science park',
            ],
            [
                'name'         => 'Medical Campus',
                'address'      => '89 Health Road, West End',
                'phone_number' => '+2348076543210',
                'email'        => 'medicalcampus@university.edu',
                'direction'    => 'Next to University Teaching Hospital',
            ],
        ];

        foreach ($campuses as $data) {
            Campus::updateOrCreate(
                ['email' => $data['email']], // unique check
                $data
            );
        }
    }
}
