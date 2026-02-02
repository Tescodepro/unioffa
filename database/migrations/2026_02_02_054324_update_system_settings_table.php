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
        // Check if table exists, if so, we modify it. If not, we create it.
        // Assuming it might exist but be empty as per Model check.

        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group')->default('general'); // general, mail, assets, admission
                $table->string('type')->default('string'); // string, boolean, file, number, rich_text
                $table->string('description')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('system_settings', function (Blueprint $table) {
                // Add columns if they don't exist
                if (!Schema::hasColumn('system_settings', 'key')) {
                    $table->string('key')->unique();
                }
                if (!Schema::hasColumn('system_settings', 'value')) {
                    $table->text('value')->nullable();
                }
                if (!Schema::hasColumn('system_settings', 'group')) {
                    $table->string('group')->default('general');
                }
                if (!Schema::hasColumn('system_settings', 'type')) {
                    $table->string('type')->default('string');
                }
                if (!Schema::hasColumn('system_settings', 'description')) {
                    $table->string('description')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
