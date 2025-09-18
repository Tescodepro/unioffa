<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('course_title');
            $table->string('course_code')->unique();
            $table->unsignedTinyInteger('course_unit');
            $table->string('course_status')->nullable();
            $table->foreignUuid('department_id')->constrained('departments')->cascadeOnDelete();
            $table->unsignedSmallInteger('level');
            $table->enum('semester', ['1st', '2nd','3rd','4th','5th','6th']);
            $table->boolean('active_for_register')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('courses');
        Schema::enableForeignKeyConstraints();

    }
};
