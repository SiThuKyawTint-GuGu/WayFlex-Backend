<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "payment_type_id",
        "flight_ticket_id",
        "ticket_price",
        "fare_tax",
        "total_amount",
        "transaction_date",
        "coupon_id",
        "payment_form_id",
        "passenger_count",
        "seat_count",
        "level_discount_id",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function flight_ticket()
    {
        return $this->belongsTo(FlightTicket::class);
    }

    public function coupon_id()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payment_form()
    {
        return $this->belongsTo(PaymentForm::class);
    }

    public function level_discount()
    {
        return $this->belongsTo(LevelDiscount::class);
    }
}
