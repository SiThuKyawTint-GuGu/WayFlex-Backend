<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTicketPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        "price",
        "flight_trip_id"
    ];

    public function flight_trip()
    {
        return $this->belongsTo(FlightTrip::class);
    }
}
