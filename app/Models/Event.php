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

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
