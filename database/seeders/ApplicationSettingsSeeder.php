<?php

namespace Database\Seeders;

use App\Models\ApplicationSetting;
use Illuminate\Database\Seeder;

class ApplicationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'name' => 'IDELDE Admission',
                'application_code' => 'IDELDE',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'alevel'],
                ]),
                'application_fee' => 10000,
                'acceptance_fee' => 25000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '2',
                'description' => 'For IDEL DE candidates. Requires a completed profile, Olevel and Alevel results, and chosen course of study.',
            ],
            [
                'name' => 'IDELUTME Admission',
                'application_code' => 'IDELUTME',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel'],
                ]),
                'application_fee' => 10000,
                'acceptance_fee' => 25000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '4',
                'description' => 'For IDEL UTME candidates. Requires a completed profile, Olevel and Alevel results, and chosen course of study.',
            ],
            [
                'name' => 'UTME Admission',
                'application_code' => 'UTME',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'jamb_detail' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'utme_printout'],
                ]),
                'application_fee' => 10000,
                'acceptance_fee' => 50000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '4',
                'description' => 'For UTME candidates applying with Olevel results only. Requires profile, Olevel results, and course selection.',
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
                'application_fee' => 10000,
                'acceptance_fee' => 25000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '2',
                'description' => 'Top-up admission for candidates with prior qualifications. Requires profile, Olevel results, previous certificate, and course selection.',
            ],
            [
                'name' => 'Transfer Admission',
                'application_code' => 'TRANSFER',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'transfer_letter', 'transcript'],
                ]),
                'application_fee' => 10000,
                'acceptance_fee' => 50000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '3',
                'description' => 'For students transferring from other institutions. Requires profile, Olevel results, transfer letter, and course selection.',
            ],
            [
                'name' => 'DE Admission',
                'application_code' => 'DE',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel', 'alevel_or_result_certificate '],
                ]),
                'application_fee' => 10000,
                'acceptance_fee' => 50000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '3',
                'description' => 'Direct Entry admission. Requires profile, Olevel, Alevel, Result certificate, and course selection.',
            ],
            [
                'name' => 'UNIOFFA DIPLOMA Admission',
                'application_code' => 'DE',
                'modules_enable' => json_encode([
                    'profile' => true,
                    'olevel' => true,
                    'alevel' => false,
                    'course_of_study' => true,
                    'documents' => ['olevel'],
                ]),
                'application_fee' => 10000,
                'acceptance_fee' => 50000,
                'academic_session' => '2025/2026',
                'enabled' => true,
                'admission_duration' => '4',
                'description' => 'Direct Entry admission for diploma holders. Requires profile, Olevel, Alevel, diploma certificate, and course selection.',
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
