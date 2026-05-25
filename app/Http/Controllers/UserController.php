<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(User::all(), 'Daftar user berhasil diambil');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:buyer,creator,admin',
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);

        return $this->success($user, 'Role berhasil diupdate');
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

        return $this->success($user, 'User berhasil dibuat', 201);
    }
}
