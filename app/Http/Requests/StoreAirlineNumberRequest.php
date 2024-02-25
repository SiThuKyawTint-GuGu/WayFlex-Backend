<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAirlineNumberRequest extends FormRequest
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
            'number' => 'required',
            'airline_id' => 'required|numeric|exists:airlines,id',
            'total_seat' => 'required',
            'flight_class_id' => 'required|numeric|exists:flight_classes,id',
        ];
    }
}
