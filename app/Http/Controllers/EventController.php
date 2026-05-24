<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Event::with(['organizer', 'tickets'])->orderBy('event_datetime')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event'     => 'required|string|max:150',
            'deskripsi'      => 'nullable|string',
            'lokasi'         => 'required|string|max:150',
            'event_datetime' => 'required|date',
        ]);

        // Auto-create organizer dari data user yang login jika belum ada
        $organizer = \App\Models\Organizer::firstOrCreate(
            ['contact_email' => $request->user()->email],
            [
                'organizer_name' => $request->user()->nama,
                'contact_email'  => $request->user()->email,
            ]
        );

        $event = \App\Models\Event::create([
            'organizer_id'   => $organizer->id,
            'nama_event'     => $request->nama_event,
            'deskripsi'      => $request->deskripsi,
            'lokasi'         => $request->lokasi,
            'event_datetime' => $request->event_datetime,
            'event_status'   => 'draft',
        ]);

        return response()->json([
            'message' => 'Event berhasil dibuat',
            'data'    => $event
        ], 201);
    }

    public function show(Event $event)
    {
        return response()->json([
            'data' => $event->load(['organizer', 'tickets']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_event'     => 'required|string|max:150',
            'deskripsi'      => 'nullable|string',
            'lokasi'         => 'required|string|max:150',
            'event_datetime' => 'required|date',
        ]);

        $event = Event::findOrFail($id);

        // Pastikan yang edit adalah organizer yang membuat event
        if ($event->organizer_id != $request->user()->id &&
            $request->user()->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengedit event ini'
            ], 403);
        }

        $event->update([
            'nama_event'     => $request->nama_event,
            'deskripsi'      => $request->deskripsi,
            'lokasi'         => $request->lokasi,
            'event_datetime' => $request->event_datetime,
        ]);

        return response()->json([
            'message' => 'Event berhasil diupdate',
            'data'    => $event
        ]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        // Pastikan yang delete adalah organizer yang membuat event
        if ($event->organizer_id != auth()->user()->id &&
            auth()->user()->role != 'admin') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus event ini'
            ], 403);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event berhasil dihapus'
        ]);
    }
}
