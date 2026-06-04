<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $orders = Order::with(['user', 'detailOrders.ticket.event', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest('tanggal_order')
            ->get();

        return $this->success($orders, 'Daftar order berhasil diambil');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_harga' => 'required|numeric|min:0'
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'tanggal_order' => now(),
            'total_harga' => $validated['total_harga'],
            'status_order' => 'pending'
        ]);

        return $this->success(
            $order->load(['user', 'detailOrders.ticket.event', 'payment']),
            'Order berhasil dibuat',
            201
        );
    }

    public function show(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id && $request->user()->role !== 'admin', 403);

        return $this->success(
            $order->load(['user', 'detailOrders.ticket.event', 'payment']),
            'Detail order berhasil diambil'
        );
    }
}
