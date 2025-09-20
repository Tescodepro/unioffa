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
                'name' => 'Main Campus',
                'address' => 'Beside Len Poly',
                'phone_number' => '+2349036154339',
                'email' => 'info1@unioffa.edu.ng',
                'direction' => 'Offa Kwara State',
            ],
            [
                'name' => 'Ilorin Campus',
                'address' => 'Muhydeen College Of Education, Ilorin',
                'phone_number' => '+2348068242496',
                'email' => 'info3@university.edu',
                'direction' => 'Behind the science park',
            ],
            [
                'name' => 'Ogun Campus',
                'address' => 'DSP Adegbenro ICT Polythenic Itori',
                'phone_number' => '+2348066280969',
                'email' => 'info2@university.edu',
                'direction' => 'Next to University Teaching Hospital',
            ],
            [
                'name' => 'Igbeti Campus',
                'address' => 'Igbeti Oyo State',
                'phone_number' => '+2348027216771',
                'email' => 'info4@university.edu',
                'direction' => 'Next to University Teaching Hospital',
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
