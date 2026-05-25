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
        return $this->success(Payment::all(), 'Daftar pembayaran berhasil diambil');
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'metode' => 'required|string|max:50',
            'jumlah_bayar' => 'required|numeric|min:0'
        ]);

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'metode' => $request->metode,
            'jumlah_bayar' => $request->jumlah_bayar,
            'status_pembayaran' => 'pending',
            'waktu_bayar' => now()
        ]);

        return $this->success($payment, 'Payment berhasil dibuat', 201);
    }
}