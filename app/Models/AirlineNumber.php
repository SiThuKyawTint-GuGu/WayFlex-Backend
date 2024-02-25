<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineNumber extends Model
{
    use HasFactory;
    protected $fillable = [
        "number",
        "airline_id",
        "total_seat",
        "flight_class_id",
        "flight_ticket_id"
    ];

    public function airline(){
        return $this->belongsTo(Airline::class);
    }

    public function flight_class()
    {
        return $this->belongsTo(FlightClass::class);
    }

    public function flight_ticket()
    {
        return $this->belongsTo(FlightTicket::class);
    }
}
