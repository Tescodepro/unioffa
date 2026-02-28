<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UserType;
use App\Models\Permission;
use Illuminate\Support\Str;

class UserTypeController extends Controller
{
    public function index()
    {
        $userTypes = UserType::all();
        return view('staff.ict.user-types.index', compact('userTypes'));
    }

    public function create()
    {
        return view('staff.ict.user-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:user_types,name|max:255',
        ]);

        UserType::create([
            'id' => Str::uuid(),
            'name' => strtolower($request->name),
        ]);

        return redirect()->route('ict.user-types.index')->with('success', 'User Type created successfully.');
    }

    public function permissions($id)
    {
        $userType = UserType::findOrFail($id);
        $permissions = Permission::all();

        return view('staff.ict.user-types.permissions', compact('userType', 'permissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $userType = UserType::findOrFail($id);

        // Sync permissions
        $userType->permissions()->sync($request->permissions ?? []);

        return back()->with('success', 'Permissions updated successfully.');
    }
}
