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
        'max_ticket_per_transaction',
        'one_email_one_transaction',
        'single_identity_per_ticket',
    ];

    protected $casts = [
        'event_datetime'             => 'datetime',
        'one_email_one_transaction'  => 'boolean',
        'single_identity_per_ticket' => 'boolean',
    ];

    protected $appends = ['image_src'];

    /**
     * Accessor: full URL for event image with fallback.
     */
    public function getImageSrcAttribute(): string
    {
        $path = $this->image_url ?: 'assets/hero-banner.jpg';

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Check if stored via Storage (e.g., event-banners/...)
        if ($this->image_url && !str_starts_with($this->image_url, 'assets/')) {
            return asset('storage/' . $this->image_url);
        }

        return asset($path);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Accessor: lowest ticket price value.
     */
    public function getLowestTicketPriceAttribute(): ?float
    {
        if ($this->relationLoaded('tickets')) {
            return $this->tickets->min('harga');
        }

        return $this->tickets()->min('harga');
    }

    /**
     * Accessor: formatted lowest ticket price (e.g. "Rp 50.000" or "Gratis").
     */
    public function getFormattedLowestTicketPriceAttribute(): string
    {
        $price = $this->lowest_ticket_price;

        if ($price === null) {
            return 'Belum ada tiket';
        }

        if ($price == 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format((float) $price, 0, ',', '.');
    }

    /**
     * Accessor: total capacity across all tickets.
     */
    public function getTotalCapacityAttribute(): int
    {
        if ($this->relationLoaded('tickets')) {
            return (int) $this->tickets->sum('kuota');
        }

        return (int) $this->tickets()->sum('kuota');
    }

    /**
     * Accessor: total sold tickets.
     */
    public function getSoldTicketsAttribute(): int
    {
        if ($this->relationLoaded('tickets')) {
            return (int) $this->tickets->sum(fn (Ticket $ticket) => $ticket->sold_quantity);
        }

        return (int) $this->tickets()->get()->sum(fn (Ticket $ticket) => $ticket->sold_quantity);
    }
}
