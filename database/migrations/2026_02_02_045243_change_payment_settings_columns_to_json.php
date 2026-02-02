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
        // 1. Convert columns to TEXT/JSON to allow array storage
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->text('entry_mode')->nullable()->change();
            $table->text('student_type')->nullable()->change();
        });

        // 2. Data Migration: Convert existing strings to JSON Arrays
        // We use raw PHP to iterate and update to be DB-agnostic (safer than raw SQL JSON functions which vary)
        $settings = \DB::table('payment_settings')->get();
        foreach ($settings as $setting) {
            $updates = [];

            // Convert Entry Mode
            if ($setting->entry_mode && !str_starts_with($setting->entry_mode, '[')) {
                $updates['entry_mode'] = json_encode([$setting->entry_mode]);
            }

            // Convert Student Type
            if ($setting->student_type && !str_starts_with($setting->student_type, '[')) {
                $updates['student_type'] = json_encode([$setting->student_type]);
            }

            if (!empty($updates)) {
                \DB::table('payment_settings')
                    ->where('id', $setting->id)
                    ->update($updates);
            }
        }
    }

    public function down(): void
    {
        // Reverse: Extract first item from JSON array back to string
        $settings = \DB::table('payment_settings')->get();
        foreach ($settings as $setting) {
            $updates = [];

            if ($setting->entry_mode && str_starts_with($setting->entry_mode, '[')) {
                $arr = json_decode($setting->entry_mode, true);
                $updates['entry_mode'] = $arr[0] ?? null;
            }

            if ($setting->student_type && str_starts_with($setting->student_type, '[')) {
                $arr = json_decode($setting->student_type, true);
                $updates['student_type'] = $arr[0] ?? null;
            }

            if (!empty($updates)) {
                \DB::table('payment_settings')
                    ->where('id', $setting->id)
                    ->update($updates);
            }
        }

        Schema::table('payment_settings', function (Blueprint $table) {
            $table->string('entry_mode')->nullable()->change();
            $table->string('student_type')->nullable()->change();
        });
    }
};
