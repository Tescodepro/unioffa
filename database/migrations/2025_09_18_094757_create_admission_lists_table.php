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
        Schema::create('admission_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('approved_department_id')->nullable();
            $table->string('session_admitted'); // e.g. "2025/2026"
            $table->enum('admission_status', ['pending', 'admitted', 'rejected'])->default('pending');
            $table->timestamps();

            // If related tables also use UUIDs
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_department_id')->references('id')->on('departments')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_lists');
    }
};
