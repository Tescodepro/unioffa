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
        Schema::table('course_registrations', function (Blueprint $table) {
            // Step 1: Add temporary new columns
            $table->string('session_temp')->nullable();
            $table->string('semester_temp')->nullable();
        });

        // Step 2: Copy existing data from foreign keys into new columns
        DB::statement("
            UPDATE course_registrations cr
            JOIN academic_sessions s ON cr.session_id = s.id
            JOIN academic_semesters sem ON cr.semester_id = sem.id
            SET cr.session_temp = s.name, cr.semester_temp = sem.name
        ");

        Schema::table('course_registrations', function (Blueprint $table) {
            // Step 3: Drop old foreign keys and columns
            $table->dropForeign(['session_id']);
            $table->dropForeign(['semester_id']);
            $table->dropColumn(['session_id', 'semester_id']);

            // Step 4: Rename temp columns to final names
            $table->renameColumn('session_temp', 'session');
            $table->renameColumn('semester_temp', 'semester');
        });
    }


    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            // Rollback logic (optional)
            $table->uuid('session_id')->nullable();
            $table->uuid('semester_id')->nullable();
            $table->dropColumn(['session', 'semester']);
        });
    }
};
