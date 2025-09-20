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
        Schema::create('jamb_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('user_application_id');
            $table->string('registration_number')->unique();
            $table->year('exam_year');
            $table->string('jamb_type'); // 'UTME' or 'direct entry'
            $table->string('score')->nullable(); // total JAMB score
            $table->json('subjects')->nullable();
            $table->json('subject_scores')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_application_id')->references('id')->on('user_applications')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamb_details');
    }
};
