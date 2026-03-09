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
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->text('stream')->nullable()->change();
            $table->text('campus_id')->nullable()->change();
        });

        Schema::table('academic_semesters', function (Blueprint $table) {
            $table->text('stream')->nullable()->change();
            $table->text('campus_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_sessions', function (Blueprint $table) {
            //
        });
    }
};
