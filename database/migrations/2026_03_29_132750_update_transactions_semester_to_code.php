<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update 'First Semester' to '1st'
        DB::table('transactions')
            ->where('semester', 'First Semester')
            ->update(['semester' => '1st']);

        // Update 'Second Semester' to '2nd'
        DB::table('transactions')
            ->where('semester', 'Second Semester')
            ->update(['semester' => '2nd']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert '1st' to 'First Semester'
        DB::table('transactions')
            ->where('semester', '1st')
            ->update(['semester' => 'First Semester']);

        // Revert '2nd' to 'Second Semester'
        DB::table('transactions')
            ->where('semester', '2nd')
            ->update(['semester' => 'Second Semester']);
    }
};
