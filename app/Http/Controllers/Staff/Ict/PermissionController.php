<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('staff.ict.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('staff.ict.permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
            'identifier' => 'nullable|string|max:100|unique:permissions,identifier',
        ]);

        // Auto-generate identifier from name if not provided
        if (empty($data['identifier'])) {
            $data['identifier'] = Str::slug($data['name'], '_');
        } else {
            $data['identifier'] = Str::slug($data['identifier'], '_');
        }

        Permission::create($data);

        // Bust the gate cache so the new permission is available immediately
        Cache::forget('route_permissions_map');

        return redirect()->route('ict.permissions.index')
            ->with('success', 'Permission "'.$data['name'].'" created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('staff.ict.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name,'.$permission->id,
            'identifier' => 'nullable|string|max:100|unique:permissions,identifier,'.$permission->id,
        ]);

        if (empty($data['identifier'])) {
            $data['identifier'] = Str::slug($data['name'], '_');
        } else {
            $data['identifier'] = Str::slug($data['identifier'], '_');
        }

        $permission->update($data);

        Cache::forget('route_permissions_map');

        return redirect()->route('ict.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        // Detach from all user types first
        $permission->userTypes()->detach();
        $permission->delete();

        Cache::forget('route_permissions_map');

        return redirect()->route('ict.permissions.index')
            ->with('success', 'Permission deleted.');
    }
}
