<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ETicket;
use Illuminate\Support\Str;

class ETicketController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => ETicket::with(['detailOrder.ticket.event', 'checkin'])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'detail_order_id' => 'required|exists:detail_orders,id'
        ]);

        $ticket = ETicket::create([
            'detail_order_id' => $request->detail_order_id,
            'kode_qr' => Str::uuid(),
            'status_validasi' => 'valid'
        ]);

        return response()->json([
            'message' => 'E-ticket berhasil dibuat',
            'data' => $ticket->load('detailOrder.ticket.event')
        ], 201);
    }
}
