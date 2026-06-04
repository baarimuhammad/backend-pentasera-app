<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    protected $fillable = [
        'order_id',
        'ticket_id',
        'jumlah',
        'subtotal'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function eTickets()
    {
        return $this->hasMany(ETicket::class);
    }
}
