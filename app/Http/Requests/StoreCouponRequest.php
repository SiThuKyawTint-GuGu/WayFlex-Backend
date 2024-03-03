<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'system_id' => 'required|numeric|exists:systems,id',
            'coupon_number' => 'required',
            'amount' => 'required',
            'expire_date' => 'required',
            'status' => 'required',
        ];
    }
}
