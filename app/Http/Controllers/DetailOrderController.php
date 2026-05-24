<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetailOrder;
use Illuminate\Support\Facades\DB;

class DetailOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'ticket_id' => 'required|exists:tickets,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        if ($ticket->sisa_kuota < $request->jumlah) {
            return response()->json([
                'message' => 'Kuota tiket tidak mencukupi',
            ], 422);
        }

        $subtotal = $ticket->harga * $request->jumlah;

        $detail = DB::transaction(function () use ($request, $ticket, $subtotal) {
            $detail = DetailOrder::create([
                'order_id' => $request->order_id,
                'ticket_id' => $request->ticket_id,
                'jumlah' => $request->jumlah,
                'subtotal' => $subtotal
            ]);

            $ticket->decrement('sisa_kuota', $request->jumlah);

            return $detail;
        });

        return response()->json([
            'message' => 'Detail order berhasil dibuat',
            'data' => $detail->load(['order', 'ticket.event'])
        ], 201);
    }

    public function index()
    {
        return response()->json([
            'data' => DetailOrder::with(['order.user', 'ticket.event', 'eTicket'])->get(),
        ]);
    }
}
