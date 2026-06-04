<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\ETicket;
use Illuminate\Support\Str;

class ETicketController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(
            ETicket::with(['detailOrder.ticket.event', 'checkin'])->get(),
            'Daftar e-ticket berhasil diambil'
        );
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

        return $this->success(
            $ticket->load('detailOrder.ticket.event'),
            'E-ticket berhasil dibuat',
            201
        );
    }
}
