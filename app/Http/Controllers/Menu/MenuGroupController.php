<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuGroupRequest;
use App\Http\Requests\UpdateMenuGroupRequest;
use Illuminate\Http\Request;
use App\Models\MenuGroup;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class MenuGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:menu-groups.index')->only('index', 'list');
        $this->middleware('permission:menu-groups.create')->only('create', 'store');
        $this->middleware('permission:menu-groups.edit')->only('edit', 'update');
        $this->middleware('permission:menu-groups.destroy')->only('destroy');
    }

    /**
     * Return data for DataTables
     */
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $menuGroups = MenuGroup::select('id', 'name', 'permission_name', 'icon', 'route', 'order')
                ->orderBy('order', 'asc');
            return DataTables::of($menuGroups)
                ->addIndexColumn()
                ->make(true);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menu-groups.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuGroupRequest $request)
    {
        $data = $request->validated();

        $maxOrder = MenuGroup::max('order') ?? 0;

        // Jika order user > maxOrder + 1, set jadi maxOrder + 1
        if (!isset($data['order']) || $data['order'] > $maxOrder + 1) {
            $data['order'] = $maxOrder + 1;
        }

        DB::transaction(function () use ($data, &$menuGroup) {
            // Geser menu group lain yang order >= input user
            MenuGroup::where('order', '>=', $data['order'])->increment('order');

            // Simpan menu group baru
            $menuGroup = MenuGroup::create($data);

            // Buat permission jika belum ada
            $permission = Permission::firstOrCreate([
                'name' => $menuGroup->permission_name,
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
            'message' => 'Menu Group created successfully',
            'data' => $menuGroup
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $menuGroup = MenuGroup::find($id);

        if (!$menuGroup) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu Group not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $menuGroup
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuGroupRequest $request, $id)
    {
        $menuGroup = MenuGroup::find($id);

        if (!$menuGroup) {
            return response()->json([
                'status' => 'error',
                'message' => 'Menu Group not found'
            ], 404);
        }

        $oldPermissionName = $menuGroup->permission_name;
        $data = $request->validated();
        $maxOrder = MenuGroup::max('order') ?? 0;

        if (!isset($data['order']) || $data['order'] > $maxOrder) {
            $data['order'] = $maxOrder;
        }

        DB::transaction(function () use ($menuGroup, $data, $oldPermissionName) {
            // Shift orders jika berubah
            if (isset($data['order']) && $data['order'] != $menuGroup->order) {
                if ($data['order'] > $menuGroup->order) {
                    MenuGroup::where('order', '>', $menuGroup->order)
                        ->where('order', '<=', $data['order'])->decrement('order');
                } else {
                    MenuGroup::where('order', '>=', $data['order'])
                        ->where('order', '<', $menuGroup->order)->increment('order');
                }
            }

            $menuGroup->update($data);

            // Update nama permission jika berubah
            if ($oldPermissionName !== $data['permission_name']) {
                $permission = Permission::where('name', $oldPermissionName)->first();
                if ($permission) {
                    $permission->update(['name' => $data['permission_name']]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Menu Group updated successfully',
            'data' => $menuGroup
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuGroup $menuGroup)
    {
        DB::transaction(function () use ($menuGroup) {
            $deletedOrder = $menuGroup->order;
            $permissionName = $menuGroup->permission_name;

            // Hapus menu group
            $menuGroup->delete();

            // Geser order menu group lain yang lebih besar dari yang dihapus
            MenuGroup::where('order', '>', $deletedOrder)->decrement('order');

            // Cabut permission dari super-admin
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
            'message' => 'Menu Group deleted successfully'
        ]);
    }
}
