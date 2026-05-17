<?php

use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        MenuItem::updateOrCreate(
            ['route_name' => 'bursary.payment-lockdown-settings.index'],
            [
                'section' => 'Finance',
                'label' => 'Payment Lockdown',
                'icon' => 'ti ti-lock',
                'route_pattern' => 'staff/burser/payment-lockdown-settings*',
                'permission_identifier' => 'manage_payment_settings',
                'sort_order' => 62,
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MenuItem::where('route_name', 'bursary.payment-lockdown-settings.index')->delete();
    }
};
