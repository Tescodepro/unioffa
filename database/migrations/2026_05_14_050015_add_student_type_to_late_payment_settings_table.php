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
            $table->longText('student_type')->nullable()->after('entry_mode');
            $table->longText('level')->nullable()->after('student_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('late_payment_settings', function (Blueprint $table) {
            $table->dropColumn(['student_type', 'level']);
        });
    }
};
