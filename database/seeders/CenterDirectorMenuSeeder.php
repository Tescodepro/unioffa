<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CenterDirectorMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Insert the Admissions menu item for center-director users.
        // Uses firstOrCreate logic via updateOrInsert to be safe on re-runs.
        DB::table('menu_items')->updateOrInsert(
            [
                'route_name' => 'center-director.admission.applicants',
                'user_type_scope' => 'center-director',
            ],
            [
                'id' => (string) Str::uuid(),
                'label' => 'Applicants',
                'route_name' => 'center-director.admission.applicants',
                'route_pattern' => 'center-director/admission/applicants*',
                'icon' => 'ti ti-users',
                'section' => 'Admissions',
                'user_type_scope' => 'center-director',
                'permission_identifier' => null,
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
