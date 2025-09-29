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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // 🔹 Split name into first, middle, last
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();

            // 🔹 Contact info
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->unique();
            $table->string('otp')->nullable();
            $table->string('otp_expires_at')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('campus_id')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();

            // 🔹 Authentication
            $table->string('username')->unique()->commont('The matric number for student');
            $table->string('registration_no')->nullable()->comment('The registration number for applicant no for applicanq and reg no for staff');
            $table->string('password');
            $table->rememberToken();

            // 🔹 User type (FK to user_types table)
            $table->uuid('user_type_id');
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
