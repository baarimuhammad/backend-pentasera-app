<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'event_id',
        'kategori',
        'harga',
        'kuota',
        'sisa_kuota',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class);
    }
}
