<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(
            Payment::with('order.user')->latest('waktu_bayar')->get(),
            'Daftar pembayaran berhasil diambil'
        );
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

        // Also update the order status to paid
        $order = Order::findOrFail($validated['order_id']);
        $order->update([
            'status_order' => 'paid'
        ]);

        return $this->success(
            $payment->load('order.user'),
            'Payment berhasil dibuat',
            201
        );
    }
}
