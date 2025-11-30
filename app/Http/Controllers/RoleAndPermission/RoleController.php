<?php

namespace App\Http\Controllers\RoleAndPermission;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Traits\ActivityLoggable;

class RoleController extends Controller
{
    use ActivityLoggable;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:roles.index')->only('index', 'list');
        $this->middleware('permission:roles.create')->only('create', 'store');
        $this->middleware('permission:roles.edit')->only('edit', 'update');
        $this->middleware('permission:roles.destroy')->only('destroy');
    }

    private function generateRoleLogData(Role $role): array
    {
        return [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'guard_name' => $role->guard_name,
        ];
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::select('id', 'name', 'guard_name' ,'created_at', 'updated_at');
            return datatables()->of($roles)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function index()
    {
        return view('role-and-permission.roles.index');
    }

    public function store(StoreRoleRequest $request)
    {
        // Implementation for storing a new role
        $role = Role::create($request->validated());
        // Log the creation
        $logData = collect($this->generateRoleLogData($role));

        $this->logActivity('Role Created', "Role {$role->name} Dibuat", $logData->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'data' => $role
        ]);
    }

    public function edit($id)
    {
        $role = Role::findById($id);
        return response()->json([
            'status' => 'success',
            'data' => $role
        ]);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findById($id);
        $originalData = $this->generateRoleLogData($role);

        $role->update($request->validated());

        // Log the update
        $updatedData = $this->generateRoleLogData($role);
        $changes = array_diff_assoc($updatedData, $originalData);

        if (!empty($changes)) {
            $this->logActivity('Role Updated', "Role {$role->name} Diperbarui", $changes);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findById($id);
        $logData = collect($this->generateRoleLogData($role));

        $role->delete();

        // Log the deletion
        $this->logActivity('Role Deleted', "Role {$role->name} Dihapus", $logData->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully'
        ]);
    }
}
