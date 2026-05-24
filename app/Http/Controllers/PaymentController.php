<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Payment::with('order.user')->latest('waktu_bayar')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'metode' => 'required|in:qris,ewallet,virtual_account,bank_transfer',
            'jumlah_bayar' => 'required|numeric|min:0'
        ]);

        $payment = Payment::create([
            'order_id' => $validated['order_id'],
            'metode' => $validated['metode'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'status_pembayaran' => 'paid',
            'waktu_bayar' => now()
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $order->update([
            'status_order' => 'paid'
        ]);

        return response()->json([
            'message' => 'Payment berhasil dibuat',
            'data' => $payment->load('order.user')
        ], 201);
    }
}
