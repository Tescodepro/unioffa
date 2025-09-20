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
        Schema::create('application_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('application_code');
            $table->text('description');
            $table->boolean('enabled')->default(true);
            $table->string('academic_session');
            $table->string('admission_duration')->nullable();
            $table->json('modules_enable')->nullable()->comment('JSON object with module names as keys and booleans as values'); // can hold nested JSON
            $table->decimal('application_fee', 10, 2)->default(0.00);
            $table->decimal('acceptance_fee', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_settings');
    }
};
