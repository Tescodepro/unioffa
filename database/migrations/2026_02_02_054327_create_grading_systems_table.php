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
        Schema::create('grading_systems', function (Blueprint $table) {
            $table->id();
            $table->string('grade'); // A, B, C, D, E, F
            $table->integer('min_score'); // e.g. 70
            $table->integer('max_score'); // e.g. 100
            $table->decimal('point', 3, 2); // e.g. 5.00
            $table->string('description')->nullable(); // Excellent, Very Good
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_systems');
    }
};
