<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Models\MenuGroup;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class MenuItemController extends Controller
{
    public function list(Request $request, $id)
    {
        if ($request->ajax()) {
            $menuItems = MenuItem::select('id', 'menu_group_id', 'name', 'route', 'order', 'permission_name')
                ->orderBy('order', 'asc')->where('menu_group_id', $id);
            return DataTables::of($menuItems)
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function index($id)
    {
        $menuGroup = MenuGroup::findOrFail($id);

        if ($menuGroup->route) {
            // Simpan flash message
            return redirect()->route('menu-groups.index')
                ->with('error', 'Menu group already has route');
        }

        return view('menu-items.index', compact('menuGroup'));
    }

    public function store(StoreMenuItemRequest $request, $menuGroupId)
    {
        $menuGroup = MenuGroup::findOrFail($menuGroupId);
        $data = $request->validated();
        $data['menu_group_id'] = $menuGroup->id;

        // Tentukan order
        $maxOrder = MenuItem::where('menu_group_id', $menuGroup->id)->max('order') ?? 0;
        if (!isset($data['order']) || $data['order'] > $maxOrder + 1) {
            $data['order'] = $maxOrder + 1;
        }

        // Transaction untuk geser order dan simpan
        DB::transaction(function () use ($data, &$menuItem, $menuGroup) {
            // Geser item yang order >= input user
            MenuItem::where('menu_group_id', $menuGroup->id)
                ->where('order', '>=', $data['order'])
                ->increment('order');

            // Simpan menu item baru
            $menuItem = MenuItem::create($data);

            // Buat permission jika belum ada
            $permission = Permission::firstOrCreate([
                'name' => $menuItem->permission_name,
                'guard_name' => 'web',
            ]);

            // Berikan permission ke super-admin
            $role = Role::findByName('super-admin', 'web');
            if ($role) {
                $role->givePermissionTo($permission);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Menu Item created successfully',
            'data' => $menuItem
        ]);
    }

    public function edit($menuGroupId, $itemId)
    {
        $menuItem = MenuItem::where('menu_group_id', $menuGroupId)
            ->findOrFail($itemId);

        return response()->json([
            'status' => 'success',
            'data' => $menuItem
        ]);
    }

    public function update(StoreMenuItemRequest $request, $menuGroupId, $itemId)
    {
        $menuGroup = MenuGroup::findOrFail($menuGroupId);
        $menuItem = MenuItem::where('menu_group_id', $menuGroup->id)
            ->findOrFail($itemId);

        $data = $request->validated();
        $oldPermissionName = $menuItem->permission_name;
        // Tentukan order
        $maxOrder = MenuItem::where('menu_group_id', $menuGroup->id)->max('order') ?? 0;
        if (!isset($data['order']) || $data['order'] > $maxOrder) {
            $data['order'] = $maxOrder;
        }

        DB::transaction(function () use ($data, $menuItem, $menuGroup, $oldPermissionName) {
            if ($menuItem->order != $data['order']) {
                // Geser order menu item lain
                if ($data['order'] > $menuItem->order) {
                    MenuItem::where('menu_group_id', $menuGroup->id)
                        ->whereBetween('order', [$menuItem->order + 1, $data['order']])
                        ->decrement('order');
                } else {
                    MenuItem::where('menu_group_id', $menuGroup->id)
                        ->whereBetween('order', [$data['order'], $menuItem->order - 1])
                        ->increment('order');
                }
            }

            // Update menu item
            $menuItem->update($data);

            // Update permission jika ada permission_name
            if ($data['permission_name']) {
                $permission = Permission::where('name', $oldPermissionName)->first();
                if ($permission) {
                    $permission->update(['name' => $data['permission_name']]);
                }

                $role = Role::findByName('super-admin', 'web');
                if ($role) {
                    $role->givePermissionTo($permission);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Menu Item updated successfully',
            'data' => $menuItem
        ]);
    }

    public function destroy($menuGroupId, $itemId)
    {
        $menuGroup = MenuGroup::findOrFail($menuGroupId);
        $menuItem = MenuItem::where('menu_group_id', $menuGroup->id)
            ->findOrFail($itemId);
        $permissionName = $menuItem->permission_name;
        DB::transaction(function () use ($menuItem, $permissionName) {
            // Geser order item lain
            MenuItem::where('menu_group_id', $menuItem->menu_group_id)
                ->where('order', '>', $menuItem->order)
                ->decrement('order');

            $menuItem->delete();

            $role = Role::findByName('super-admin', 'web');
            if ($role && $permissionName) {
                if (Permission::where('name', $permissionName)->exists()) {
                    $role->revokePermissionTo($permissionName);

                    // Hapus permission dari tabel permission
                    Permission::where('name', $permissionName)->delete();
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Menu Item deleted successfully',
        ]);
    }
}
