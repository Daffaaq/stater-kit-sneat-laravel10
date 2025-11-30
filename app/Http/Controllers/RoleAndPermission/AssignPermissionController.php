<?php

namespace App\Http\Controllers\RoleAndPermission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\ActivityLoggable;
use Illuminate\Support\Facades\DB;

class AssignPermissionController extends Controller
{
    use ActivityLoggable;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:assign.index')->only('index', 'list');
        $this->middleware('permission:assign.edit')->only('edit', 'update');
    }

    private function generateAssignLogData(Role $role): array
    {
        return [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ];
    }

    // Datatable list
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('permissions')->select('id', 'name');

            return datatables()->of($roles)
                ->addIndexColumn()
                ->addColumn('permissions', fn($role) => $role->permissions->pluck('name')->implode(', '))
                ->make(true);
        }
    }

    public function index()
    {
        return view('role-and-permission.assign-permission.index', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
        ]);
    }

    public function edit(Role $role)
    {
        $menuGroups = DB::table('menu_groups')->orderBy('order')->get();
        $menuItems = DB::table('menu_items')->orderBy('menu_group_id')->orderBy('order')->get();
        $permissions = DB::table('permissions')->get();

        $groupedMenus = $menuGroups->map(function ($group) use ($menuItems, $permissions) {
            $items = $menuItems->where('menu_group_id', $group->id)->values()->map(function ($item) use ($permissions, $group) {
                // Ambil permission khusus item, kecuali permission group
                $relatedPermissions = $permissions->filter(function ($perm) use ($item, $group) {
                    return $perm->name !== $group->permission_name
                        && str_starts_with($perm->name, explode('.', $item->permission_name)[0]);
                })->values();

                return [
                    'item' => $item,
                    'permissions' => $relatedPermissions
                ];
            });

            // Ambil permission group
            $groupPermission = $permissions->firstWhere('name', $group->permission_name);

            return [
                'group' => $group,
                'group_permission' => $groupPermission,
                'items' => $items
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'role' => $role,
                'role_permissions' => $role->permissions->pluck('name')->toArray(),
                'grouped_menus' => $groupedMenus
            ]
        ]);
    }



    public function update(Request $request, Role $role)
    {
        $permissions = $request->input('permissions', []);

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        $originalPermissions = $role->permissions->pluck('name')->toArray();

        $role->syncPermissions($permissions);

        $updatedPermissions = $role->permissions->pluck('name')->toArray();

        if (!empty(array_diff($updatedPermissions, $originalPermissions)) || count($permissions) !== count($originalPermissions)) {
            $this->logActivity(
                'Update Assigned Permission',
                "Role {$role->name} updated permissions",
                [
                    'before' => $originalPermissions,
                    'after' => $updatedPermissions,
                    'role_id' => $role->id,
                    'role_name' => $role->name
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Permissions updated successfully'
        ]);
    }
}
