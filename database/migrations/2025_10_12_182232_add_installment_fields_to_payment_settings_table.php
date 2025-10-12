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
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->boolean('installmental_allow_status')->default(0)->after('description')->comment('0 = not allowed, 1 = allowed');
            $table->unsignedTinyInteger('number_of_instalment')->default(1)->after('installmental_allow_status')->comment('Number of allowed installment payments (1–9)');
            $table->json('list_instalment_percentage')->nullable()->after('number_of_instalment')->comment('List of installment percentages e.g. [60,40] or [33.3,33.3,33.4]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn([
                'installmental_allow_status',
                'number_of_instalment',
                'list_instalment_percentage',
            ]);
        });
    }
};
