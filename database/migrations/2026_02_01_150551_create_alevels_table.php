<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alevels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users');
            $table->foreignUuid('user_application_id')->nullable()->constrained('user_applications');
            $table->string('exam_type')->nullable();
            $table->string('exam_year')->nullable();
            $table->string('center_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->json('subjects')->nullable();
            $table->json('grades')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alevels');
    }
};
