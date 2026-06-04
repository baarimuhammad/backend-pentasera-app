<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ETicket extends Model
{
    protected $table = 'e_tickets';

    protected $fillable = [
        'detail_order_id',
        'kode_qr',
        'status_validasi'
    ];

    public function detailOrder()
    {
        return $this->belongsTo(DetailOrder::class);
    }

    public function checkin()
    {
        return $this->hasOne(Checkin::class);
    }
}
