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
        "ticket_number",
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
        "discount_amount",
        "normal_amount"
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

    public function coupon()
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

    public function passengers()
    {
        return $this->belongsToMany(PassengerQty::class, 'flight_transaction_passengers', 'flight_transaction_id', 'passenger_qty_id')
        ->withTimestamps();
    }

    public function seats()
    {
        return $this->belongsToMany(AirlineSeat::class, 'flight_transaction_seats', 'flight_transaction_id', 'airline_seat_id')
        ->withTimestamps();
    }

    // public static function getAllWithRelationships(){
    //     return self::with([
    //         'user',
    //         'payment_type',
    //         'flight_ticket',
    //         'coupon',
    //         'payment_form',
    //         'level_discount',
    //         'passengers',
    //         'seats'
    //     ])->get();
    // }

}
