<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_permissions', function (Blueprint $table) {
            $table->string('route_name')->primary();        // Laravel named route (unique)
            $table->string('permission_identifier');        // Permission identifier from permissions table
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_permissions');
    }
};
