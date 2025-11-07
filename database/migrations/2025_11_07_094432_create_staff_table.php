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
        Schema::create('staff', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Use UUID as primary key
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('faculty_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('department_id')->nullable()->constrained()->onDelete('set null');

            $table->string('staff_no')->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('designation')->nullable(); // Dean, HOD, Lecturer, etc.
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('date_of_employment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
