<?php

namespace App\Http\Controllers\RoleAndPermission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Traits\ActivityLoggable;


class AssignUserToRoleController extends Controller
{
    use ActivityLoggable;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:assign.index')->only('index', 'list');
        $this->middleware('permission:assign.create')->only('create', 'store');
        $this->middleware('permission:assign.edit')->only('edit', 'update');
    }

    private function generateAssignLogData(User $user): array
    {
        return [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'roles' => $user->roles->pluck('name')->toArray(),
        ];
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->select('id', 'name', 'email');

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->implode(', ');
                })
                ->make(true);
        }
    }

    public function index()
    {
        return view('role-and-permission.assign-user.index', [
            'users' => User::all(),
            'roles' => Role::all(),
        ]);
    }

    public function create()
    {
        return view('role-and-permission.assign-user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ]);

        $user = User::findOrFail($request->user_id);

        // Assign multiple roles
        $user->syncRoles($request->roles);

        $this->logActivity(
            'Assign Role',
            "User {$user->name} assigned roles: " . implode(', ', $request->roles),
            $this->generateAssignLogData($user)
        );

        // log
        $this->logActivity("Assign Role", "User {$user->name} assigned roles: " . implode(', ', $request->roles), [
            'user_id' => $user->id,
            'roles' => $request->roles
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Roles assigned successfully'
        ]);
    }

    public function edit(User $user)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'roles' => Role::all(),
                'user_roles' => $user->roles->pluck('name'),
            ]
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Jika tidak ada roles, jadikan array kosong
        $roles = $request->input('roles', []);

        // Validasi roles (boleh kosong)
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name'
        ]);

        $originalRoles = $user->roles->pluck('name')->toArray();

        // Update roles (jika array kosong, otomatis lepas semua)
        $user->syncRoles($roles);

        $updatedRoles = $user->roles->pluck('name')->toArray();
        $changes = array_diff($updatedRoles, $originalRoles);

        if (!empty($changes) || count($roles) !== count($originalRoles)) {
            $this->logActivity(
                'Update Assigned Role',
                "User {$user->name} updated roles",
                [
                    'before' => $originalRoles,
                    'after' => $updatedRoles,
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Roles updated successfully'
        ]);
    }
}
