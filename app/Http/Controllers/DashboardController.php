<?php

namespace App\Http\Controllers;

use App\Models\MenuGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        return view('Dashboard.index');
    }

    public function sidebar()
    {
        $menuGroups = MenuGroup::with('menuItems')
            ->orderBy('order', 'asc')
            ->get();

        return view('layouts.sidebar', compact('menuGroups'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6|confirmed', // pakai password_confirmation
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
}
