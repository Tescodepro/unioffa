<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Campus;

class PaymentSlitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $payment_splits = [
            [
                'name' => 'Application and Acceptance Regular  - Main Campus - Percentage',
                'split_code' => 'SPL_HkiZhN6TEF',
                'payment_type' => ['application', 'acceptance', 'maintenance', 'medical', 'ict', 'sport', 'entrepreneurship', 'departmental', 'faculty'],
                'student_type' => ['REGULAR'],
                'center' => 'Main Campus',
            ],
            [
                'name' => 'Application and Acceptance Topup - Main Campus - Percentage',
                'split_code' => 'SPL_HkiZhN6TEF',
                'payment_type' => ['application', 'acceptance', 'administrative'],
                'student_type' => ['TOPUP'],
                'center' => 'Main Campus',
            ],
            [
                'name' => 'Tuition  - Ogun Campus - Percentage',
                'split_code' => 'SPL_JDtYyXDx2R',
                'payment_type' => ['tuition'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Ogun Campus',
            ],
            [
                'name' => 'Tuition  - Igbeti Campus - Percentage',
                'split_code' => 'SPL_cPdCmjimXg',
                'payment_type' => ['tuition'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Igbeti Campus',
            ],
            [
                'name' => 'administrative  - Ogun Campus - Percentage',
                'split_code' => 'SPL_rvIoWM96bH',
                'payment_type' => ['administrative'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Ogun Campus',
            ],
            [
                'name' => 'administrative  - Igbeti Campus - Percentage',
                'split_code' => 'SPL_DGCkobJPtX',
                'payment_type' => ['administrative'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Igbeti Campus',
            ],
            [
                'name' => 'Application and Acceptance  - Ogun Campus - Percentage',
                'split_code' => 'SPL_2qcMkWu4Fo',
                'payment_type' => ['acceptance', 'application'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Ogun Campus',
            ],
            [
                'name' => 'Application and Acceptance  - Igbeti Campus - Percentage',
                'split_code' => 'SPL_XBvKnYFDZw',
                'payment_type' => ['acceptance', 'application'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Igbeti Campus',
            ],
            [
                'name' => 'id_card  - Ogun Campus - Percentage',
                'split_code' => 'SPL_WcoeU3fmz5',
                'payment_type' => ['id_card'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Ogun Campus',
            ],
            [
                'name' => 'id_card  - Igbeti Campus - Percentage',
                'split_code' => 'SPL_iNCsZ2eb0J',
                'payment_type' => ['id_card'],
                'student_type' => ['TOPUP', 'IDELDE', 'IDELUTME'],
                'center' => 'Igbeti Campus',
            ],

        ];

        foreach ($payment_splits as $data) {
            $data['payment_type'] = json_encode($data['payment_type']);
            $data['student_type'] = json_encode($data['student_type']);

            if ($data['center']) {
                // 🔹 Find campus
                $campus = Campus::where('name', $data['center'])->first();

                if (! $campus) {
                    $this->command->warn(" Campus {$data['center']} not found. Skipping...");

                    continue;
                }
            } else {
                $campus = Campus::first();
            }
            $data['center'] = $campus->slug;
            
            DB::table('payment_splits')->updateOrInsert(
            ['name' => $data['name']], // unique check
            array_merge($data, [
                'id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ])
            );
        }
    }
}
