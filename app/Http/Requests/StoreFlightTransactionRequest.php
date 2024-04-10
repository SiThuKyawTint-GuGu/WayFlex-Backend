<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlightTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_form_id' => 'required|numeric|exists:payment_forms,id',
            'passenger_type_id' => 'required|not_null',
            'seat_type_id' => 'required|not_null',
            'flight_ticket_id' => 'required|numeric|exists:flight_tickets,id',
            'ticket_price' => 'required',
            'fare_tax' => 'required',
            'total_amount' => 'required',
            'transaction_date' => 'required',
            'discount_amount' => 'required',
            'normal_amount' => 'required',
            'level_discount_id' => 'required|numeric|exists:level_discounts,id'
        ];
    }
}
