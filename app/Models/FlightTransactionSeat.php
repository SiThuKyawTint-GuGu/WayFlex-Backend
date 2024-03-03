<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTransactionSeat extends Model
{
    use HasFactory;
    protected $fillable = [
        "airline_seat_id",
        "flight_transaction_id",
    ];

    public function airline_seat()
    {
        return $this->belongsTo(AirlineSeat::class);
    }

    public function flight_transaction()
    {
        return $this->belongsTo(FlightTransaction::class);
    }

}
