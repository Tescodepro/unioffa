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
        Schema::table('payment_settings', function (Blueprint $table) {
            // Drop foreign keys and unique constraints
            $table->dropForeign(['faculty_id']);
            $table->dropForeign(['department_id']);
            $table->dropUnique(['matric_number']);

            // Rename existing columns to old_*
            $table->renameColumn('faculty_id', 'old_faculty_id');
            $table->renameColumn('department_id', 'old_department_id');
            $table->renameColumn('sex', 'old_sex');
            $table->renameColumn('matric_number', 'old_matric_number');
            $table->renameColumn('semester', 'old_semester');
        });

        Schema::table('payment_settings', function (Blueprint $table) {
            // Add new JSON columns
            $table->json('faculty_ids')->nullable()->after('old_faculty_id');
            $table->json('department_ids')->nullable()->after('old_department_id');
            $table->json('sexes')->nullable()->after('old_sex');
            $table->json('matric_numbers')->nullable()->after('old_matric_number');
            $table->json('semesters')->nullable()->after('old_semester');
        });

        // Migrate data
        \DB::table('payment_settings')->get()->each(function ($item) {
            \DB::table('payment_settings')->where('id', $item->id)->update([
                'faculty_ids' => $item->old_faculty_id ? json_encode([$item->old_faculty_id]) : json_encode([]),
                'department_ids' => $item->old_department_id ? json_encode([$item->old_department_id]) : json_encode([]),
                'sexes' => $item->old_sex ? json_encode([$item->old_sex]) : json_encode([]),
                'matric_numbers' => $item->old_matric_number ? json_encode([$item->old_matric_number]) : json_encode([]),
                'semesters' => $item->old_semester ? json_encode([$item->old_semester]) : json_encode([]),
            ]);
        });

        Schema::table('payment_settings', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['old_faculty_id', 'old_department_id', 'old_sex', 'old_matric_number', 'old_semester']);
        });
    }

    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->char('faculty_id', 36)->nullable();
            $table->char('department_id', 36)->nullable();
            $table->string('sex')->nullable();
            $table->string('matric_number')->nullable();
            $table->string('semester')->nullable();
        });

        // Migrate data back
        \DB::table('payment_settings')->get()->each(function ($item) {
            $faculty_ids = json_decode($item->faculty_ids, true) ?: [];
            $department_ids = json_decode($item->department_ids, true) ?: [];
            $sexes = json_decode($item->sexes, true) ?: [];
            $matric_numbers = json_decode($item->matric_numbers, true) ?: [];
            $semesters = json_decode($item->semesters, true) ?: [];

            \DB::table('payment_settings')->where('id', $item->id)->update([
                'faculty_id' => ! empty($faculty_ids) ? $faculty_ids[0] : null,
                'department_id' => ! empty($department_ids) ? $department_ids[0] : null,
                'sex' => ! empty($sexes) ? $sexes[0] : null,
                'matric_number' => ! empty($matric_numbers) ? $matric_numbers[0] : null,
                'semester' => ! empty($semesters) ? $semesters[0] : null,
            ]);
        });

        Schema::table('payment_settings', function (Blueprint $table) {
            $table->unique('matric_number');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->dropColumn(['faculty_ids', 'department_ids', 'sexes', 'matric_numbers', 'semesters']);
        });
    }
};
