<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Checkin;
use App\Models\ETicket;

class CheckinController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(Checkin::all(), 'Daftar check-in berhasil diambil');
    }

    public function store(Request $request)
    {
        $request->validate([
            'e_ticket_id' => 'required|exists:e_tickets,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $ticket = ETicket::findOrFail($request->e_ticket_id);

        if ($ticket->status_validasi !== 'valid') {
            return $this->error('Tiket tidak valid atau sudah digunakan', 400);
        }

        $checkin = Checkin::create([
            'e_ticket_id' => $request->e_ticket_id,
            'user_id' => $request->user_id,
            'waktu_checkin' => now()
        ]);

        $ticket->update([
            'status_validasi' => 'used'
        ]);

        return $this->success($checkin, 'Check-in berhasil', 201);
    }
}
