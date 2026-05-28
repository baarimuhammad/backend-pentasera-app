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

    protected $appends = ['image_src'];

    /**
     * Accessor: full URL for event image with fallback.
     */
    public function getImageSrcAttribute(): string
    {
        return $this->image_url
            ? asset('storage/' . $this->image_url)
            : asset('assets/hero-banner.jpg');
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
     * Accessor: formatted lowest ticket price (e.g. "Rp 50.000" or "Gratis").
     */
    public function getFormattedLowestTicketPriceAttribute(): string
    {
        if (!$this->relationLoaded('tickets') || $this->tickets->isEmpty()) {
            return 'Rp 0';
        }

        $lowest = $this->tickets->min('harga');

        if ($lowest == 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($lowest, 0, ',', '.');
    }
}
