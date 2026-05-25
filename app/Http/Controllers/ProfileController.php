<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Organizer;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * PATCH /api/profile
     * Update user name, no_hp. If creator → also update organizer fields.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama'          => 'sometimes|string|max:255',
            'no_hp'         => 'sometimes|nullable|string|max:20',
            // Creator-only organizer fields
            'organizer_name' => 'sometimes|string|max:255',
            'deskripsi'      => 'sometimes|nullable|string|max:500',
            'address'        => 'sometimes|nullable|string|max:255',
            'contact_phone'  => 'sometimes|nullable|string|max:20',
        ]);

        $user = auth()->user();

        // Update user fields
        if ($request->has('nama')) {
            $user->nama = $request->nama;
        }
        if ($request->has('no_hp')) {
            $user->no_hp = $request->no_hp;
        }
        $user->save();

        // If creator, update organizer
        if ($user->role === 'creator') {
            $organizer = Organizer::where('user_id', $user->id)->first();

            if ($organizer) {
                $organizerFields = $request->only([
                    'organizer_name', 'deskripsi', 'address', 'contact_phone'
                ]);

                if (!empty($organizerFields)) {
                    $organizer->update($organizerFields);
                }
            }
        }

        return $this->success($user->fresh(), 'Profil berhasil diperbarui');
    }

    /**
     * POST /api/profile/avatar
     * Upload user avatar image.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar_url = $path;
        $user->save();

        return $this->success([
            'avatar_url' => $path,
            'avatar_full_url' => asset('storage/' . $path),
        ], 'Avatar berhasil diupload');
    }
}
