<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert any 'rejected' status to 'pending' before changing the enum
        DB::table('results')
            ->where('status', 'rejected')
            ->update(['status' => 'pending']);

        // Modify the enum column to include new statuses
        Schema::table('results', function (Blueprint $table) {
            // For MySQL, we need to use raw SQL to modify enum
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE results MODIFY COLUMN status ENUM('pending', 'recommended', 'approved', 'published') DEFAULT 'pending'");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert any non-standard statuses back to pending before reverting
        DB::table('results')
            ->whereIn('status', ['recommended', 'published'])
            ->update(['status' => 'pending']);

        // Revert to the original enum
        Schema::table('results', function (Blueprint $table) {
            DB::statement("ALTER TABLE results MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        });
    }
};
