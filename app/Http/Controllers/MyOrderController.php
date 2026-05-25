<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ETicket;

class MyOrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/my-orders
     * List orders belonging to the authenticated user, paginated.
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['detailOrders.ticket.event', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->success($orders, 'Daftar order berhasil diambil');
    }

    /**
     * GET /api/my-orders/{id}
     * Show a single order detail — only if it belongs to the authenticated user.
     */
    public function show($id)
    {
        $order = Order::with(['detailOrders.ticket.event', 'payment', 'detailOrders.eTickets'])
            ->findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            return $this->error('Order tidak ditemukan', 404);
        }

        return $this->success($order, 'Detail order berhasil diambil');
    }

    /**
     * GET /api/my-tickets
     * List e-tickets belonging to the authenticated user
     * via: e_tickets → detail_orders → orders (where user_id = auth).
     */
    public function myTickets(Request $request)
    {
        $tickets = ETicket::whereHas('detailOrder.order', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['detailOrder.ticket.event', 'detailOrder.order'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($tickets, 'Daftar tiket berhasil diambil');
    }

    /**
     * GET /api/e-tickets/{id}
     * Show a single e-ticket — only if it belongs to the authenticated user.
     */
    public function showETicket($id)
    {
        $eTicket = ETicket::with(['detailOrder.ticket.event', 'detailOrder.order'])
            ->findOrFail($id);

        if ($eTicket->detailOrder->order->user_id !== auth()->id()) {
            return $this->error('E-ticket tidak ditemukan', 404);
        }

        return $this->success($eTicket, 'Detail e-ticket berhasil diambil');
    }
}
