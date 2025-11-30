<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Events\UserActivityEvent;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.index')->only('index', 'list');
        $this->middleware('permission:users.create')->only('create', 'store');
        $this->middleware('permission:users.edit')->only('edit', 'update');
        $this->middleware('permission:users.destroy')->only('destroy');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select('id', 'name', 'email');
            return DataTables::of($users)
                ->addIndexColumn()
                ->make(true);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsersRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsersRequest $request, $id)
    {
        $user = User::find($id);
        //mengupdate data user ke database
        $data = $request->validated();

        // Hanya update password jika ada
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']); // hash aman
        } else {
            unset($data['password']); // jangan ubah password
        }

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Cek jika user yang login mencoba menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        try {
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User Deleted Successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete the user. Please try again later.'
            ], 500);
        }
    }
}
