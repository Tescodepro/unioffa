<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Maps every user type name to its post-login dashboard route.
     * To add a new role, just add an entry here and re-run this seeder.
     * The dashboard_route can also be updated directly in the DB without a re-deploy.
     */
    public function run(): void
    {
        $types = [
            'administrator' => 'admin.dashboard',
            'vice-chancellor' => 'vc.dashboard',
            'registrar' => 'registrar.dashboard',
            'bursary' => 'burser.dashboard',
            'ict' => 'ict.dashboard',
            'dean' => 'lecturer.dean.dashboard',
            'lecturer' => 'lecturer.dashboard',
            'hod' => 'lecturer.dashboard',
            'center-director' => 'center-director.dashboard',
        ];

        foreach ($types as $name => $dashboardRoute) {
            $existing = UserType::where('name', $name)->first();

            if ($existing) {
                // Just update the dashboard_route, never touch the id or re-insert
                $existing->update(['dashboard_route' => $dashboardRoute]);
            } else {
                UserType::create([
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                    'dashboard_route' => $dashboardRoute,
                ]);
            }
        }
    }
}
