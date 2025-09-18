<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign keys
            $table->foreignUuid('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('course_id')->constrained('courses')->onDelete('cascade');

            // Snapshot of course details (in case they change later)
            $table->string('course_code');
            $table->string('course_title');
            $table->unsignedInteger('course_unit');

            // Academic session and semester
            $table->foreignUuid('session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignUuid('semester_id')->constrained('academic_semesters')->onDelete('cascade');

            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
