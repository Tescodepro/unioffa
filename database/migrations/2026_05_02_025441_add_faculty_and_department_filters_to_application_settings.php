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
        Schema::table('application_settings', function (Blueprint $table) {
            $table->json('available_faculties')->nullable()->after('modules_enable');
            $table->json('available_departments')->nullable()->after('available_faculties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_settings', function (Blueprint $table) {
            $table->dropColumn(['available_faculties', 'available_departments']);
        });
    }
};
