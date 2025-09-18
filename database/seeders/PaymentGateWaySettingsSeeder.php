<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGateWaySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'paystack',
                'display_name' => 'Paystack',
                'is_active' => 1,
                'settings' => json_encode([
                    'public_key' => 'your-paystack-public-key',
                    'secret_key' => 'your-paystack-secret-key',
                    'merchant_email' => 'merchant@example.com',
                ]),
            ],
            [
                'name' => 'monnify',
                'display_name' => 'Monnify',
                'is_active' => 0,
                'settings' => json_encode([
                    'api_key' => 'your-monnify-api-key',
                    'secret_key' => 'your-monnify-secret-key',
                    'contract_code' => 'your-contract-code',
                ]),
            ],
            [
                'name' => 'oneapp',
                'display_name' => 'OneApp',
                'is_active' => 0,
                'settings' => json_encode([
                    'client_id' => 'your-oneapp-client-id',
                    'client_secret' => 'your-oneapp-client-secret',
                ]),
            ],
        ];

        foreach ($gateways as $gateway) {
            DB::table('payment_gate_way_settings')->updateOrInsert(
                ['name' => $gateway['name']], // check unique column
                [
                    'display_name' => $gateway['display_name'],
                    'is_active'    => $gateway['is_active'],
                    'settings'     => $gateway['settings'],
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ]
            );
        }
    }
}
