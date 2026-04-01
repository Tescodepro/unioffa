<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->string('matric_no')->nullable()->after('student_id');
        });

        // Backfill existing records with matric_no from students table
        if (DB::getDriverName() === 'mysql') {
            DB::statement('
                UPDATE course_registrations cr
                JOIN students s ON cr.student_id = s.user_id
                SET cr.matric_no = s.matric_no
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn('matric_no');
        });
    }
};
