<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTransactionPassenger extends Model
{
    use HasFactory;
    protected $fillable = [
        "passenger_qty_id",
        "flight_transaction_id",
    ];

    public function passenger_qty()
    {
        return $this->belongsTo(PassengerQty::class);
    }

    public function flight_transaction()
    {
        return $this->belongsTo(FlightTransaction::class);
    }
}
