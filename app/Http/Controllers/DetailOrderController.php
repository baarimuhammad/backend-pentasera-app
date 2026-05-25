<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetailOrder;

class DetailOrderController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'ticket_id' => 'required|exists:tickets,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        $subtotal = $ticket->harga * $request->jumlah;

        $detail = DetailOrder::create([
            'order_id' => $request->order_id,
            'ticket_id' => $request->ticket_id,
            'jumlah' => $request->jumlah,
            'subtotal' => $subtotal
        ]);

        return $this->success($detail, 'Detail order berhasil dibuat', 201);
    }

    public function index()
    {
        return $this->success(DetailOrder::all(), 'Daftar detail order berhasil diambil');
    }
}