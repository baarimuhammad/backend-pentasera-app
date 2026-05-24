<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Ticket::with('event.organizer')->orderBy('event_id')->orderBy('harga')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'kategori' => 'required|string|max:50',
            'harga' => 'required|numeric',
            'kuota' => 'required|integer|min:1'
        ]);

        $ticket = Ticket::create([
            'event_id' => $validated['event_id'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'kuota' => $validated['kuota'],
            'sisa_kuota' => $validated['kuota']
        ]);

        return response()->json([
            'message' => 'Ticket berhasil dibuat',
            'data' => $ticket->load('event.organizer')
        ], 201);
    }

    public function show(Ticket $ticket)
    {
        return response()->json([
            'data' => $ticket->load('event.organizer'),
        ]);
    }
}
