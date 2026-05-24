<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal_order',
        'total_harga',
        'status_order',
    ];

    protected $casts = [
        'tanggal_order' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
