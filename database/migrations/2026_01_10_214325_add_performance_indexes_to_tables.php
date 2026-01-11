<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * These indexes will dramatically improve query performance for the admin dashboard.
     */
    public function up(): void
    {
        // Index on user_types.name for fast type lookups
        Schema::table('user_types', function (Blueprint $table) {
            $table->index('name', 'user_types_name_index');
        });

        // Index on users.user_type_id for filtering by user type
        Schema::table('users', function (Blueprint $table) {
            $table->index('user_type_id', 'users_user_type_id_index');
            $table->index('campus_id', 'users_campus_id_index');
        });

        // Index on user_applications for fast lookups
        Schema::table('user_applications', function (Blueprint $table) {
            $table->index('user_id', 'user_applications_user_id_index');
            $table->index('application_setting_id', 'user_applications_application_setting_id_index');
            $table->index('academic_session', 'user_applications_academic_session_index');
        });

        // Index on transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'payment_type', 'payment_status'], 'transactions_user_payment_index');
        });

        // Index on admission_lists
        Schema::table('admission_lists', function (Blueprint $table) {
            $table->index('admission_status', 'admission_lists_status_index');
            $table->index('user_id', 'admission_lists_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->dropIndex('user_types_name_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_user_type_id_index');
            $table->dropIndex('users_campus_id_index');
        });

        Schema::table('user_applications', function (Blueprint $table) {
            $table->dropIndex('user_applications_user_id_index');
            $table->dropIndex('user_applications_application_setting_id_index');
            $table->dropIndex('user_applications_academic_session_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_payment_index');
        });

        Schema::table('admission_lists', function (Blueprint $table) {
            $table->dropIndex('admission_lists_status_index');
            $table->dropIndex('admission_lists_user_id_index');
        });
    }
};
