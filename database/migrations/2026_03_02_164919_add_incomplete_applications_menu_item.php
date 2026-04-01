<?php

use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        MenuItem::create([
            'section' => 'Student Management',
            'label' => 'Incomplete Applications',
            'icon' => 'ti ti-file-x',
            'route_name' => 'ict.applications.incomplete',
            'route_pattern' => 'staff/ict/applications/incomplete*',
            'permission_identifier' => 'manage_students',
            'sort_order' => 45,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MenuItem::where('route_name', 'ict.applications.incomplete')->delete();
    }
};
