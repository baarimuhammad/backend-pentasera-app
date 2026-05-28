<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Checkin;
use App\Models\ETicket;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Support\Facades\DB;

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

    /**
     * POST /api/checkin/scan
     * Scan QR code for check-in.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'kode_qr' => 'required|string',
        ]);

        $eTicket = ETicket::where('kode_qr', $request->kode_qr)
            ->with(['detailOrder.ticket.event', 'detailOrder.order.user'])
            ->first();

        if (!$eTicket) {
            return $this->error('E-ticket dengan kode QR tersebut tidak ditemukan', 404);
        }

        // Already used
        if ($eTicket->status_validasi === 'used') {
            $previousCheckin = Checkin::where('e_ticket_id', $eTicket->id)->first();
            return $this->error('Tiket sudah digunakan (check-in sebelumnya: ' . ($previousCheckin->waktu_checkin ?? '-') . ')', 422);
        }

        // Process check-in
        $checkin = DB::transaction(function () use ($eTicket, $request) {
            $eTicket->update(['status_validasi' => 'used']);

            return Checkin::create([
                'e_ticket_id' => $eTicket->id,
                'user_id' => $request->user()->id,
                'waktu_checkin' => now(),
            ]);
        });

        $detailOrder = $eTicket->detailOrder;
        $ticket = $detailOrder->ticket ?? null;
        $event = $ticket->event ?? null;
        $order = $detailOrder->order ?? null;

        return $this->success([
            'checkin' => $checkin,
            'event' => $event ? [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'lokasi' => $event->lokasi,
                'event_datetime' => $event->event_datetime,
            ] : null,
            'ticket' => $ticket ? [
                'kategori' => $ticket->kategori,
                'harga' => $ticket->harga,
            ] : null,
            'buyer_name' => $order && $order->user ? $order->user->nama : '-',
            'waktu_checkin' => $checkin->waktu_checkin,
        ], 'Check-in berhasil', 201);
    }

    /**
     * GET /api/events/{id}/checkins
     * List all check-ins for an event (ownership check).
     */
    public function eventCheckins(Request $request, $id)
    {
        $event = Event::with('organizer')->findOrFail($id);

        // Ownership check
        if ($request->user()->role !== 'admin' && $event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses ke data check-in event ini', 403);
        }

        $ticketIds = $event->tickets()->pluck('id');

        $checkins = Checkin::whereHas('eTicket.detailOrder', function ($q) use ($ticketIds) {
                $q->whereIn('ticket_id', $ticketIds);
            })
            ->with(['eTicket.detailOrder.ticket', 'eTicket.detailOrder.order.user'])
            ->orderBy('waktu_checkin', 'desc')
            ->paginate(20);

        return $this->success($checkins, 'Daftar check-in event berhasil diambil');
    }
}
