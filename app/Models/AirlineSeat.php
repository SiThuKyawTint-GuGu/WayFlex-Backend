<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineSeat extends Model
{
    use HasFactory;
    protected $fillable = [
        "airline_number_id",
        "seat_number",
        "seat_status",
        "flight_ticket_price_id"
    ];

    public function airline_number()
    {
        return $this->belongsTo(AirlineNumber::class);
    }

    public function flight_ticket_price()
    {
        return $this->belongsTo(FlightTicketPrice::class);
    }
}
