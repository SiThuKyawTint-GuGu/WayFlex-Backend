<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerQty extends Model
{
    use HasFactory;
    protected $fillable = [
        'passenger_type_id',
        'qty'
    ];

    public function passenger_type()
    {
        return $this->belongsTo(PassengerType::class);
    }
}
