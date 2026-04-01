<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('late_payment_settings', function (Blueprint $table) {
            $table->decimal('increment_amount', 12, 2)->nullable()->after('late_fee_amount');
            $table->dateTime('increment_date')->nullable()->after('increment_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('late_payment_settings', function (Blueprint $table) {
            $table->dropColumn(['increment_amount', 'increment_date']);
        });
    }
};
