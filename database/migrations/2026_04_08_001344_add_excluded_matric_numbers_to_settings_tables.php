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
            $table->json('excluded_matric_numbers')->nullable()->after('increment_date');
        });

        Schema::table('course_registration_settings', function (Blueprint $table) {
            $table->json('excluded_matric_numbers')->nullable()->after('late_registration_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('late_payment_settings', function (Blueprint $table) {
            $table->dropColumn('excluded_matric_numbers');
        });

        Schema::table('course_registration_settings', function (Blueprint $table) {
            $table->dropColumn('excluded_matric_numbers');
        });
    }
};
