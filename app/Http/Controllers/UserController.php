<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::with('orders')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:buyer,creator,admin',
        ]);

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => 'Role berhasil diupdate',
            'data' => $user
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:buyer,creator,admin',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => 'aktif',
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data' => $user,
        ], 201);
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:buyer,creator,admin',
        ]);

        $user = \App\Models\User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return response()->json([
            'message' => 'Role berhasil diupdate',
            'data' => $user
        ]);
    }
}
