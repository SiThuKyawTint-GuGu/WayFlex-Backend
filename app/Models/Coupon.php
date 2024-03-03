<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        "system_id",
        "coupon_number",
        "amount",
        "expire_date",
        "status"
    ];

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
