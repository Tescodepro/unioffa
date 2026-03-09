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
            $table->string('stream')->nullable()->after('status_upload_result');
            $table->uuid('campus_id')->nullable()->after('stream');
            $table->text('students_ids')->nullable()->change();
            $table->text('lecturar_ids')->nullable()->change();
        });

        Schema::table('academic_semesters', function (Blueprint $table) {
            $table->string('stream')->nullable()->after('status_upload_result');
            $table->uuid('campus_id')->nullable()->after('stream');
            $table->text('students_ids')->nullable()->change();
            $table->text('lecturar_ids')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->dropColumn(['stream', 'campus_id']);
            $table->string('students_ids', 255)->nullable()->change();
            $table->string('lecturar_ids', 255)->nullable()->change();
        });

        Schema::table('academic_semesters', function (Blueprint $table) {
            $table->dropColumn(['stream', 'campus_id']);
            $table->string('students_ids', 255)->nullable()->change();
            $table->string('lecturar_ids', 255)->nullable()->change();
        });
    }
};
