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
        return $this->success(Organizer::all(), 'Daftar organizer berhasil diambil');
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

        return $this->success($organizer, 'Organizer berhasil dibuat', 201);
    }
}
