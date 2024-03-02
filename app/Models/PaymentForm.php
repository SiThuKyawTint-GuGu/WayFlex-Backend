<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_type_id',
        'card_holder_name',
        'card_number',
        'expiry_date',
        'cvv'
    ];

    public function payment_type(){
        return $this->belongsTo(PaymentType::class);
    }
}
