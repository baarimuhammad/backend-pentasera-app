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
            'role'          => 'sometimes|string|in:buyer,creator',
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
        if ($request->has('role')) {
            $user->role = $request->role;
        }
        $user->save();

        // If creator, update or create organizer
        if ($user->role === 'creator') {
            $organizer = Organizer::where('user_id', $user->id)->first();

            if (!$organizer) {
                $organizer = Organizer::create([
                    'user_id' => $user->id,
                    'organizer_name' => 'Organizer ' . ($user->nama ?: $user->email),
                ]);
            }

            $organizerFields = $request->only([
                'organizer_name', 'deskripsi', 'address', 'contact_phone'
            ]);

            // Filter out null or empty elements to avoid overwriting existing data if not provided
            $organizerFields = array_filter($organizerFields, function ($value) {
                return !is_null($value);
            });

            if (!empty($organizerFields)) {
                $organizer->update($organizerFields);
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

        // Upload to Cloudinary via Laravel Storage disk
        $storedPath = $request->file('avatar')->store('pentasera/avatars', 'cloudinary');
        $uploadedFileUrl = Storage::disk('cloudinary')->url($storedPath);

        $user->avatar_url = $uploadedFileUrl;
        $user->save();

        return $this->success([
            'avatar_url' => $uploadedFileUrl,
            'avatar_full_url' => $uploadedFileUrl,
        ], 'Avatar berhasil diupload');
    }
}
