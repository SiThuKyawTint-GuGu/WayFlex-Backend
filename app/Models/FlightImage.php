<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightImage extends Model
{
    use HasFactory;
    protected $fillable = [
        "flight_ticket_id",
        "image"
    ];

    public function flight_ticket(){
        return $this->belongsTo(FlightTicket::class);
    }
}
