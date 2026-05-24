<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Handle email verification from browser (user clicks link in email).
     * This is a WEB route — returns HTML view, not JSON.
     */
    public function verifyFromEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Check if hash matches
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->view('emails.verification-failed', [
                'message' => 'Link verifikasi tidak valid.',
            ], 403);
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return response()->view('emails.verification-success');
        }

        // Mark email as verified
        $user->markEmailAsVerified();

        return response()->view('emails.verification-success');
    }

    /**
     * Handle email verification via API (optional, for programmatic access).
     * Returns JSON response.
     */
    public function verifyApi(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User tidak ditemukan.',
            ], 404);
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'message' => 'Link verifikasi tidak valid.',
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email sudah diverifikasi sebelumnya.',
                'already_verified' => true,
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email berhasil diverifikasi. Silakan login.',
            'verified' => true,
        ]);
    }
}
