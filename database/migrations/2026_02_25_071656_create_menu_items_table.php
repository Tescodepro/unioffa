<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('section');                          // Group heading, e.g. "Student Management"
            $table->string('label');                            // Display name, e.g. "All Students"
            $table->string('icon')->default('ti ti-circle');   // Tabler icon class
            $table->string('route_name');                       // Laravel named route
            $table->string('permission_identifier')->nullable();// null = visible to all staff
            $table->string('route_pattern')->nullable();        // for activeClass(), e.g. "staff/ict/students*"
            $table->string('user_type_scope')->nullable();      // null = all staff; 'vice-chancellor' = VC only
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
