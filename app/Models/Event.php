<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id',
        'nama_event',
        'deskripsi',
        'lokasi',
        'event_datetime',
        'event_status',
        'image_url',
        'kategori_event',
    ];

    protected $casts = [
        'event_datetime' => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function getImageSrcAttribute(): string
    {
        $path = $this->image_url ?: 'assets/hero-banner.jpg';

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    public function getLowestTicketPriceAttribute(): ?float
    {
        if ($this->relationLoaded('tickets')) {
            return $this->tickets->min('harga');
        }

        return $this->tickets()->min('harga');
    }

    public function getFormattedLowestTicketPriceAttribute(): string
    {
        $price = $this->lowest_ticket_price;

        if ($price === null) {
            return 'Belum ada tiket';
        }

        return 'Rp ' . number_format((float) $price, 0, ',', '.');
    }

    public function getTotalCapacityAttribute(): int
    {
        if ($this->relationLoaded('tickets')) {
            return (int) $this->tickets->sum('kuota');
        }

        return (int) $this->tickets()->sum('kuota');
    }

    public function getSoldTicketsAttribute(): int
    {
        if ($this->relationLoaded('tickets')) {
            return (int) $this->tickets->sum(fn (Ticket $ticket) => $ticket->sold_quantity);
        }

        return (int) $this->tickets()->get()->sum(fn (Ticket $ticket) => $ticket->sold_quantity);
    }
}
