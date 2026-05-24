<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    protected $fillable = [
        'organizer_name',
        'deskripsi',
        'logo_url',
        'address',
        'contact_email',
        'contact_phone',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
