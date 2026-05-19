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
        Schema::create('scholarship_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('academic_session');
            $table->string('application_type')->default('all');
            $table->integer('min_jamb_score')->default(200);
            $table->json('form_fields')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_settings');
    }
};
