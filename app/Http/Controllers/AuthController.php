<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:buyer,creator',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'status' => 'aktif',
        ]);

        return $this->success([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'no_hp' => $user->no_hp,
                'avatar_url' => $user->avatar_url,
                'avatar_full_url' => $user->avatar_url ? asset('storage/' . $user->avatar_url) : null,
            ],
        ], 'Registrasi berhasil! Silakan login.', 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
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
                'no_hp' => $user->no_hp,
                'avatar_url' => $user->avatar_url,
                'avatar_full_url' => $user->avatar_url ? asset('storage/' . $user->avatar_url) : null,
            ]
        ], 'Login berhasil');
    }

    /**
     * Logout — revoke current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil');
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'creator') {
            $user->load('organizer');
        }

        return $this->success([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'no_hp' => $user->no_hp,
                'avatar_url' => $user->avatar_url,
                'avatar_full_url' => $user->avatar_url ? asset('storage/' . $user->avatar_url) : null,
                'email_verified' => $user->hasVerifiedEmail(),
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);
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
            // Tidak bocorkan info apakah email terdaftar
            return $this->success(null, 'Jika email terdaftar, kami akan mengirim link verifikasi.');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email sudah diverifikasi. Silakan login.', 400);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            report($e);

            return $this->error('Email verifikasi gagal dikirim. Periksa konfigurasi SMTP lalu coba lagi.', 503);
        }

        return $this->success(null, 'Email verifikasi berhasil dikirim ulang. Silakan cek inbox Anda.');
    }
}
