<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $fillable = [
        'e_ticket_id',
        'user_id',
        'waktu_checkin'
    ];

    public function eTicket()
    {
        return $this->belongsTo(ETicket::class, 'e_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}