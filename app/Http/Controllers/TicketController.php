<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Ticket::query();
        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        return $this->success($query->get(), 'Daftar tiket berhasil diambil');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'kategori' => 'required|string|max:50',
            'harga' => 'required|numeric',
            'kuota' => 'required|integer|min:1'
        ]);

        $ticket = Ticket::create([
            'event_id' => $request->event_id,
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'kuota' => $request->kuota,
            'sisa_kuota' => $request->kuota
        ]);

        return $this->success($ticket, 'Ticket berhasil dibuat', 201);
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return $this->success($ticket, 'Detail tiket berhasil diambil');
    }

    /**
     * PATCH /api/tickets/{id}
     * Update a ticket (ownership check via event→organizer→user_id).
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::with('event.organizer')->findOrFail($id);

        // Ownership check
        if ($request->user()->role !== 'admin'
            && $ticket->event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses untuk mengubah tiket ini', 403);
        }

        $request->validate([
            'kategori'  => 'sometimes|string|max:50',
            'harga'     => 'sometimes|numeric',
            'kuota'     => 'sometimes|integer|min:1',
        ]);

        $data = $request->only(['kategori', 'harga', 'kuota']);

        // If kuota is being updated, adjust sisa_kuota proportionally
        if (isset($data['kuota'])) {
            $sold = $ticket->kuota - $ticket->sisa_kuota;
            $data['sisa_kuota'] = max(0, $data['kuota'] - $sold);
        }

        $ticket->update($data);

        return $this->success($ticket->fresh(), 'Tiket berhasil diperbarui');
    }

    /**
     * DELETE /api/tickets/{id}
     * Delete a ticket (only if no orders reference it).
     */
    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::with('event.organizer')->findOrFail($id);

        // Ownership check
        if ($request->user()->role !== 'admin'
            && $ticket->event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses untuk menghapus tiket ini', 403);
        }

        // Check no detail_orders reference this ticket
        if ($ticket->detailOrders()->exists()) {
            return $this->error('Tiket tidak bisa dihapus karena sudah memiliki pesanan', 422);
        }

        $ticket->delete();

        return $this->success(null, 'Tiket berhasil dihapus');
    }
}
