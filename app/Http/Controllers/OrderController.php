<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'detailOrders.ticket.event', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest('tanggal_order')
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
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

        return response()->json([
            'message' => 'Order berhasil dibuat',
            'data' => $order->load(['user', 'detailOrders.ticket.event', 'payment'])
        ], 201);
    }

    public function show(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id && $request->user()->role !== 'admin', 403);

        return response()->json([
            'data' => $order->load(['user', 'detailOrders.ticket.event', 'payment']),
        ]);
    }
}
