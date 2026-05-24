<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'metode',
        'jumlah_bayar',
        'status_pembayaran',
        'waktu_bayar'
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'waktu_bayar' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
