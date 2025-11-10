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
        Schema::create('results', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Course and student relationship
            $table->foreignUuid('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('course_code');
            $table->string('course_title');
            $table->unsignedInteger('course_unit');

            // Academic info
            $table->string('session'); // e.g. 2024/2025
            $table->string('semester'); // e.g. First or Second

            // Scores
            $table->decimal('ca', 5, 2)->nullable(); // Continuous Assessment
            $table->decimal('exam', 5, 2)->nullable(); // Examination score
            $table->decimal('total', 5, 2)->nullable(); // Computed total

            // Grade and remarks
            $table->string('grade', 2)->nullable(); // e.g. A, B, C
            $table->string('remark')->nullable(); // e.g. Pass, Fail

            // Workflow and audit
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
