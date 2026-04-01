<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_types', function (Blueprint $table) {
            // The named route to redirect to after login, e.g. 'ict.dashboard'
            $table->string('dashboard_route')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('user_types', function (Blueprint $table) {
            $table->dropColumn('dashboard_route');
        });
    }
};
