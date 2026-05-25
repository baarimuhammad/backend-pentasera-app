<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Verify email from web (signed URL) — returns a Blade view.
     */
    public function verifyFromEmail(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (! $user || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return view('emails.verification-failed', [
                'message' => 'Link verifikasi tidak valid.',
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return view('emails.verification-success', [
                'message' => 'Email sudah diverifikasi sebelumnya.',
            ]);
        }

        $user->markEmailAsVerified();

        return view('emails.verification-success', [
            'message' => 'Email berhasil diverifikasi!',
        ]);
    }

    /**
     * Verify email from API (signed URL) — returns JSON.
     */
    public function verifyFromApi(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (! $user || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return $this->error('Link verifikasi tidak valid.', 400);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success(null, 'Email sudah diverifikasi sebelumnya.');
        }

        $user->markEmailAsVerified();

        return $this->success(null, 'Email berhasil diverifikasi!');
    }
}
