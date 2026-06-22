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
        Schema::create('summer_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->string('academic_session');
            $table->string('status')->default('pending_payment'); // pending_payment, pending_vc_approval, approved, rejected, registered
            $table->json('courses')->nullable();
            $table->decimal('summary_fee', 10, 2)->default(30000.00);
            $table->decimal('course_fee_total', 10, 2)->default(0.00);
            $table->decimal('total_fee', 10, 2)->default(0.00);
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->text('reason_for_increase')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summer_registrations');
    }
};
