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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID as primary key
            $table->foreignUuid('faculty_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('level')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->string('matric_number')->nullable()->unique();
            $table->string('payment_type'); // payment category
            $table->decimal('amount', 10, 2);
            $table->string('session'); // e.g., 2023/2024
            $table->string('student_type')->nullable(); // e.g., First, Second, Full Year
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
