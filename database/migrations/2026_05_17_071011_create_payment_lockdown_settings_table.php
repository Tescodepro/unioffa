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
        Schema::create('payment_lockdown_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('payment_type')->nullable(); // null means locks all payments
            $table->dateTime('deadline');
            $table->json('campus_ids')->nullable();
            $table->json('faculty_ids')->nullable();
            $table->json('department_ids')->nullable();
            $table->json('levels')->nullable();
            $table->json('admission_sessions')->nullable();
            $table->json('genders')->nullable();
            $table->json('entry_modes')->nullable();
            $table->json('programmes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_lockdown_settings');
    }
};
