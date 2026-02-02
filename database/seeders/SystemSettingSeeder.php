<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Group
            [
                'key' => 'school_name',
                'value' => 'University of Offa',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Official Name of the Institution'
            ],
            [
                'key' => 'school_motto',
                'value' => 'Knowledge, Integrity, and Service',
                'group' => 'general',
                'type' => 'string',
                'description' => 'School Motto'
            ],
            [
                'key' => 'school_address',
                'value' => 'PMB 1020, Offa, Kwara State, Nigeria',
                'group' => 'general',
                'type' => 'string',
                'description' => 'Physical Address'
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@unioffa.edu.ng',
                'group' => 'general',
                'type' => 'string',
                'description' => 'General Contact Email'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+234 810 000 0000', // Default placeholder
                'group' => 'general',
                'type' => 'string',
                'description' => 'General Contact Phone'
            ],

            // Assets Group
            [
                'key' => 'logo_path',
                'value' => 'assets/images/logo.png',
                'group' => 'assets',
                'type' => 'file',
                'description' => 'School Logo Path'
            ],
            [
                'key' => 'letterhead_path',
                'value' => 'portal_assets/img/users/letter_head.png',
                'group' => 'assets',
                'type' => 'file',
                'description' => 'Official Letterhead Image'
            ],
            [
                'key' => 'registrar_signature_path',
                'value' => 'portal_assets/img/users/signature.png',
                'group' => 'assets',
                'type' => 'file',
                'description' => 'Registrar Signature Image'
            ],

            // Admission/Registrar Group
            [
                'key' => 'registrar_name',
                'value' => 'Mr. Salaudeen OYEWALE',
                'group' => 'admission',
                'type' => 'string',
                'description' => 'Name of the Registrar'
            ],
            [
                'key' => 'current_session',
                'value' => '2024/2025',
                'group' => 'academics',
                'type' => 'string',
                'description' => 'Current Academic Session'
            ]
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
