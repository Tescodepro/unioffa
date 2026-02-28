<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_application_type_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('application_setting_id');
            $table->timestamps();

            $table->unique(['user_id', 'application_setting_id'], 'uata_user_apptype_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('application_setting_id')->references('id')->on('application_settings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_application_type_assignments');
    }
};
