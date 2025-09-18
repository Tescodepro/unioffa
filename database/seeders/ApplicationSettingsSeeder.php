<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationSetting;

class ApplicationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'name' => 'IDELUTME Admission',
                'application_code' => 'IDELUTME',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => true,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'alevel'],
                ]),
                'application_fee' => 5000,
                'acceptance_fee' => 10000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'description' => 'For IDEL UTME candidates. Requires a completed profile, Olevel and Alevel results, and chosen course of study.'
            ],
            [
                'name' => 'UTME Admission',
                'application_code' => 'UTME',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel'],
                ]),
                'application_fee' => 4000,
                'acceptance_fee' => 9000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'description' => 'For UTME candidates applying with Olevel results only. Requires profile, Olevel results, and course selection.'
            ],
            [
                'name' => 'Top-up Admission',
                'application_code' => 'TOPUP',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'previous_certificate'],
                ]),
                'application_fee' => 6000,
                'acceptance_fee' => 12000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'description' => 'Top-up admission for candidates with prior qualifications. Requires profile, Olevel results, previous certificate, and course selection.'
            ],
            [
                'name' => 'Transfer Admission',
                'application_code' => 'TRANSFER',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'transfer_letter'],
                ]),
                'application_fee' => 5500,
                'acceptance_fee' => 11000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'description' => 'For students transferring from other institutions. Requires profile, Olevel results, transfer letter, and course selection.'
            ],
            [
                'name' => 'DE Admission',
                'application_code' => 'DE',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => true,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'alevel', 'diploma_certificate'],
                ]),
                'application_fee' => 7000,
                'acceptance_fee' => 14000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'description' => 'Direct Entry admission for diploma holders. Requires profile, Olevel, Alevel, diploma certificate, and course selection.'
            ],
        ];

        foreach ($settings as $setting) {
            // avoid duplicates
            ApplicationSetting::updateOrCreate(
                ['application_code' => $setting['application_code']],
                $setting
            );
        }
    }
}

