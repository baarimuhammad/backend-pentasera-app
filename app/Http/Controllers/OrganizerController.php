<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizer;

class OrganizerController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Organizer::with('events')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organizer_name' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $organizer = Organizer::create($validated);

        return response()->json([
            'message' => 'Organizer berhasil dibuat',
            'data' => $organizer->load('events')
        ],201);
    }
}
