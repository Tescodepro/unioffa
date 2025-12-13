<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OgunStatePaymentSplitsSeeder extends Seeder
{
    public function run()
    {
        // Default student types
        $defaultStudentTypes = ["TOPUP", "IDELDE", "IDELUTME", "REGULAR"];

        $splits = [
            [
                'name' => 'ID Card',
                'split_code' => 'SPL_WcoeU3fmz5',
                'payment_type' => ['id_card'],
            ],
            [
                'name' => 'Administrative',
                'split_code' => 'SPL_rvIoWM96bH',
                'payment_type' => ['administrative'],
            ],
            [
                'name' => 'Tuition',
                'split_code' => 'SPL_JDtYyXDx2R',
                'payment_type' => ['tuition'],
            ],
            [
                'name' => 'Application Fee',
                'split_code' => 'SPL_2qcMkWu4Fo',
                'payment_type' => ['application_fee'],
            ],
            [
                'name' => 'Development Levy',
                'split_code' => 'SPL_SzCM9E6WFd',
                'payment_type' => ['development_levy'],
            ],
            [
                'name' => 'NHIS',
                'split_code' => 'SPL_vKfzCMgETv',
                'payment_type' => ['nhis'],
            ],
            [
                'name' => 'Examination Fees',
                'split_code' => 'SPL_UyEKLM5DeI',
                'payment_type' => ['examination_fees'],
            ],
            [
                'name' => 'Acceptance Fees',
                'split_code' => 'SPL_eGBpFgAeW0',
                'payment_type' => ['acceptance_fees'],
            ],
            [
                'name' => 'Caution Fees',
                'split_code' => 'SPL_ZW0F2x0NLg',
                'payment_type' => ['caution_fees'],
            ],
            [
                'name' => 'Matriculation',
                'split_code' => 'SPL_0P8VLSRKWc',
                'payment_type' => ['matriculation'],
            ],
            [
                'name' => 'Convocation',
                'split_code' => 'SPL_lmICH96FOp',
                'payment_type' => ['convocation'],
            ],
            [
                'name' => 'Alumni',
                'split_code' => 'SPL_ouS8urtj7h',
                'payment_type' => ['alumni'],
            ],
            [
                'name' => 'Technical',
                'split_code' => 'SPL_yrB2gwL9PI',
                'payment_type' => ['technical'],
            ],
        ];

        foreach ($splits as $split) {
            // Check if this split_code already exists to prevent duplicates
            $exists = DB::table('payment_splits')
                ->where('split_code', $split['split_code'])
                ->exists();

            if (!$exists) {
                DB::table('payment_splits')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $split['name'],
                    'split_code' => $split['split_code'],
                    'payment_type' => json_encode($split['payment_type']),
                    'student_type' => json_encode($defaultStudentTypes),
                    'center' => 'ogun-campus', // Normalized center name
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}