<?php

namespace App\Http\Controllers\RoleAndPermission;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Traits\ActivityLoggable;

class PermissionController extends Controller
{
    use ActivityLoggable;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:permissions.index')->only('index', 'list');
        $this->middleware('permission:permissions.create')->only('store');
        $this->middleware('permission:permissions.edit')->only('edit', 'update');
        $this->middleware('permission:permissions.destroy')->only('destroy');
    }

    private function generatePermissionLogData(Permission $permission): array
    {
        return [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name,
            'guard_name' => $permission->guard_name,
        ];
    }

    // Datatable list
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $permissions = Permission::select('id', 'name', 'guard_name', 'created_at', 'updated_at');
            return datatables()->of($permissions)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function index()
    {
        return view('role-and-permission.permissions.index');
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        $logData = collect($this->generatePermissionLogData($permission));

        $this->logActivity('Permission Created', "Permission {$permission->name} Dibuat", $logData->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Permission created successfully',
            'data' => $permission
        ]);
    }

    public function edit($id)
    {
        $permission = Permission::findById($id);

        return response()->json([
            'status' => 'success',
            'data' => $permission
        ]);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = Permission::findById($id);

        $originalData = $this->generatePermissionLogData($permission);

        $permission->update($request->validated());

        $updatedData = $this->generatePermissionLogData($permission);

        $changes = array_diff_assoc($updatedData, $originalData);

        if (!empty($changes)) {
            $this->logActivity('Permission Updated', "Permission {$permission->name} Diperbarui", $changes);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Permission updated successfully',
            'data' => $permission
        ]);
    }

    public function destroy($id)
    {
        $permission = Permission::findById($id);

        $logData = collect($this->generatePermissionLogData($permission));

        $permission->delete();

        $this->logActivity('Permission Deleted', "Permission {$permission->name} Dihapus", $logData->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Permission deleted successfully'
        ]);
    }
}
