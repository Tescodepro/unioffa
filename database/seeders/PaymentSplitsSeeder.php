<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentSplitsSeeder extends Seeder
{
    public function run()
    {
        // Default student types based on your sample
        $defaultStudentTypes = ["TOPUP", "IDELDE", "IDELUTME", "REGULAR"];

        $splits = [
            [
                'name' => 'ID Card',
                'split_code' => 'SPL_iNCsZ2eb0J',
                'payment_type' => ['id_card'],
            ],
            [
                'name' => 'Administrative',
                'split_code' => 'SPL_DGCkobJPtX',
                'payment_type' => ['administrative'],
            ],
            [
                'name' => 'Tuition',
                'split_code' => 'SPL_cPdCmjimXg',
                'payment_type' => ['tuition'],
            ],
            [
                'name' => 'Application Fee',
                'split_code' => 'SPL_FF2Zr9tfd3',
                'payment_type' => ['application'],
            ],
            [
                'name' => 'Development Levy',
                'split_code' => 'SPL_1cbIugRAa6',
                'payment_type' => ['development_levy'],
            ],
            [
                'name' => 'NHIS',
                'split_code' => 'SPL_Fi8eANOK7n',
                'payment_type' => ['nhis'],
            ],
            [
                'name' => 'Examination Fees',
                'split_code' => 'SPL_YlPul1yHkn',
                'payment_type' => ['examination'],
            ],
            [
                'name' => 'Acceptance Fees',
                'split_code' => 'SPL_uFHCsZeS0h',
                'payment_type' => ['acceptance'],
            ],
            [
                'name' => 'Caution Fees',
                'split_code' => 'SPL_Vu4fPMyS3Y',
                'payment_type' => ['caution'],
            ],
            [
                'name' => 'Matriculation',
                'split_code' => 'SPL_sm6NVnQLqy',
                'payment_type' => ['matriculation'],
            ],
            [
                'name' => 'Convocation',
                'split_code' => 'SPL_3GoqJJgXdu',
                'payment_type' => ['convocation'],
            ],
            [
                'name' => 'Alumni',
                'split_code' => 'SPL_7ttzAmO6LZ',
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
                    'id' => (string) Str::uuid(), // Generate UUID
                    'name' => $split['name'].' igbeti-campus',
                    'split_code' => $split['split_code'],
                    'payment_type' => json_encode($split['payment_type']), // Convert array to JSON
                    'student_type' => json_encode($defaultStudentTypes),   // Convert array to JSON
                    'center' => 'igbeti-campus', // Set based on your input list
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}