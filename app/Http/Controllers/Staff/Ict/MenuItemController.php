<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Permission;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::orderBy('section')->orderBy('sort_order')->get();
        return view('staff.ict.menu-items.index', compact('menuItems'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('staff.ict.menu-items.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'icon' => 'required|string|max:100',
            'route_name' => 'required|string|max:200|unique:menu_items,route_name',
            'route_pattern' => 'nullable|string|max:200',
            'permission_identifier' => 'nullable|string|max:100',
            'user_type_scope' => 'nullable|string|max:100',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        MenuItem::create($data);

        return redirect()->route('ict.menu-items.index')
            ->with('success', 'Menu item "' . $data['label'] . '" created.');
    }

    public function edit(MenuItem $menuItem)
    {
        $permissions = Permission::orderBy('name')->get();
        return view('staff.ict.menu-items.edit', compact('menuItem', 'permissions'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'section' => 'required|string|max:100',
            'label' => 'required|string|max:100',
            'icon' => 'required|string|max:100',
            'route_name' => 'required|string|max:200|unique:menu_items,route_name,' . $menuItem->id,
            'route_pattern' => 'nullable|string|max:200',
            'permission_identifier' => 'nullable|string|max:100',
            'user_type_scope' => 'nullable|string|max:100',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $menuItem->update($data);

        return redirect()->route('ict.menu-items.index')
            ->with('success', 'Menu item updated.');
    }

    public function toggle(MenuItem $menuItem)
    {
        $menuItem->update(['is_active' => !$menuItem->is_active]);
        $status = $menuItem->is_active ? 'enabled' : 'disabled';
        return redirect()->route('ict.menu-items.index')
            ->with('success', '"' . $menuItem->label . '" ' . $status . '.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return redirect()->route('ict.menu-items.index')
            ->with('success', '"' . $menuItem->label . '" deleted.');
    }
}
