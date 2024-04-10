<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentFormRequest extends FormRequest
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
            'name' => 'required',
            'payment_type_id' => 'required|exists:payment_types,id',
            'phone_number' => 'nullable',
            'otp_code' => 'nullable',
            'card_number' => 'nullable',
            'expiry_date' => 'nullable',
            'cvv' => 'nullable',
        ];
    }
}

