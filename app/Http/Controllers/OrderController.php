<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        return $this->success(Order::all(), 'Daftar order berhasil diambil');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_harga' => 'required|numeric|min:0'
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'tanggal_order' => now(),
            'total_harga' => $request->total_harga,
            'status_order' => 'pending'
        ]);

        return $this->success($order, 'Order berhasil dibuat', 201);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return $this->success($order, 'Detail order berhasil diambil');
    }
}