<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:buyer,creator',
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role,
            'status'   => 'aktif',
        ]);

        // Kirim email verifikasi
        try {
            $user->notify(new VerifyEmailNotification);
            $emailSent = true;
        } catch (\Exception $e) {
            $emailSent = false;
        }

        return response()->json([
            'success' => true,
            'requires_verification' => true,
            'email_delivery_failed' => !$emailSent,
            'message' => 'Registrasi berhasil! Silakan cek email untuk verifikasi, lalu login.',
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if ($user->status === 'nonaktif') {
            return $this->error('Akun kamu dinonaktifkan. Hubungi admin.', 403);
        }

        // Hapus token lama, buat yang baru
        $user->tokens()->delete();
        $token = $user->createToken('pantasera-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'nama'  => $user->nama,
                'email' => $user->email,
                'role'  => $user->role,
            ]
        ], 'Login berhasil');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'creator') {
            $user->load('organizer');
        }
        return $this->success(['user' => $user]);
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->error('User dengan email tersebut tidak ditemukan.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email sudah diverifikasi.', 400);
        }

        $user->notify(new VerifyEmailNotification);

        return $this->success(null, 'Email verifikasi berhasil dikirim ulang.');
    }
}