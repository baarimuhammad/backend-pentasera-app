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

    protected $casts = [
        'harga' => 'decimal:2',
        'kuota' => 'integer',
        'sisa_kuota' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class);
    }

    public function getSoldQuantityAttribute(): int
    {
        return max(0, (int) $this->kuota - (int) $this->sisa_kuota);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->harga, 0, ',', '.');
    }
}
