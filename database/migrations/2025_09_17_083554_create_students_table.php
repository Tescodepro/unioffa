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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relations
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('campus_id')->constrained('campuses')->cascadeOnDelete();

            // Academic details
            $table->string('matric_no')->unique()->comment('Matriculation number for student');
            $table->string('programme');
            $table->string('entry_mode'); // e.g. UTME, Direct Entry
            $table->string('stream')->nullable();
            $table->string('jamb_registration_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->foreignUuid('department_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->string('level')->nullable();
            $table->string('admission_session')->nullable();
            $table->string('address')->nullable();
            $table->date('admission_date')->nullable();
            $table->integer('status')->default(1)->comment('student status');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
