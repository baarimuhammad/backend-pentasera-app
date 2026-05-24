<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Checkin;
use App\Models\ETicket;

class CheckinController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Checkin::with(['eTicket.detailOrder.ticket.event', 'user'])->latest('waktu_checkin')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'e_ticket_id' => 'required|exists:e_tickets,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $ticket = ETicket::findOrFail($request->e_ticket_id);

        if ($ticket->status_validasi !== 'valid') {
            return response()->json([
                'message' => 'Tiket tidak valid atau sudah digunakan'
            ], 400);
        }

        $checkin = Checkin::create([
            'e_ticket_id' => $request->e_ticket_id,
            'user_id' => $request->user_id,
            'waktu_checkin' => now()
        ]);

        $ticket->update([
            'status_validasi' => 'used'
        ]);

        return response()->json([
            'message' => 'Check-in berhasil',
            'data' => $checkin->load(['eTicket.detailOrder.ticket.event', 'user'])
        ], 201);
    }
}
