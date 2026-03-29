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
        Schema::create('late_payment_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_type');
            $table->uuid('campus_id');
            $table->json('entry_mode')->nullable();
            $table->string('semester')->nullable();
            $table->string('session')->nullable();
            $table->dateTime('closing_date');
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('campus_id')->references('id')->on('campuses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('late_payment_settings');
    }
};
