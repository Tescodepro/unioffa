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
        Schema::create('entry_modes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('e.g., Direct Entry, UTME');
            $table->string('code')->unique()->comment('e.g., DE, UTME');
            $table->string('student_type')->comment('e.g., REGULAR, TOPUP');
            $table->string('matric_prefix')->nullable()->comment('e.g., DE, T, or empty string');
            $table->string('default_level')->default('100');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_modes');
    }
};
