<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_type_id',
        'user_id',
        'name',
        'otp_code',
        'phone_number',
        'card_number',
        'expiry_date',
        'cvv'
    ];

    public function payment_type(){
        return $this->belongsTo(PaymentType::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
