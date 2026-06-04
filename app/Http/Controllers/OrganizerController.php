<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Organizer;

class OrganizerController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(
            Organizer::with('events')->get(),
            'Daftar organizer berhasil diambil'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'organizer_name' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $organizer = Organizer::create([
            'organizer_name' => $request->organizer_name,
            'deskripsi' => $request->deskripsi,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'user_id' => auth()->id(),
        ]);

        return $this->success($organizer->load('events'), 'Organizer berhasil dibuat', 201);
    }

    /**
     * GET /api/organizers/{id}
     * Show organizer with its published events.
     */
    public function show($id)
    {
        $organizer = Organizer::with(['events' => function ($query) {
            $query->where('event_status', 'published')->orderBy('event_datetime', 'desc');
        }])->findOrFail($id);

        return $this->success($organizer, 'Detail organizer berhasil diambil');
    }

    /**
     * PATCH /api/organizers/{id}
     * Update organizer details (ownership check).
     */
    public function update(Request $request, $id)
    {
        $organizer = Organizer::findOrFail($id);

        if ($request->user()->role !== 'admin' && $organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses untuk mengubah organizer ini', 403);
        }

        $request->validate([
            'organizer_name' => 'sometimes|string|max:100',
            'deskripsi'      => 'nullable|string',
            'contact_email'  => 'nullable|email|max:100',
            'contact_phone'  => 'nullable|string|max:20',
            'address'        => 'nullable|string',
        ]);

        $organizer->update($request->only([
            'organizer_name',
            'deskripsi',
            'contact_email',
            'contact_phone',
            'address',
        ]));

        return $this->success($organizer, 'Organizer berhasil diperbarui');
    }
}
