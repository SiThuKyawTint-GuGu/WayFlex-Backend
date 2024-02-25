<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineRightCol extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "airline_number_id"
    ];

    public function airline_number()
    {
        return $this->belongsTo(AirlineNumber::class);
    }
}
