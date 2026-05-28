<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    use ApiResponseTrait;

    /**
     * PATCH /api/settings/password
     * Update user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Password saat ini salah', 422);
        }

        $user->update([
            'password' => $request->password,
        ]);

        return $this->success(null, 'Password berhasil diperbarui');
    }

    /**
     * DELETE /api/settings/account
     * Deactivate user account.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('Password salah. Akun tidak dapat ditutup.', 422);
        }

        // Deactivate account
        $user->update(['status' => 'nonaktif']);

        // Revoke all tokens
        $user->tokens()->delete();

        return $this->success(null, 'Akun berhasil dinonaktifkan');
    }
}
