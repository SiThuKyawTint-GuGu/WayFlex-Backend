<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlightTicketRequest extends FormRequest
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
            'flight_category_id' => 'required|numeric|exists:flight_categories,id',
            'system_id' => 'required|numeric|exists:systems,id',
            'description' => 'required',
            'departure_airport_id' => 'required|numeric|exists:airports,id',
            'departure_time' => 'required',
            'departure_date' => 'required',
            'departure_city_id' => 'required|numeric|exists:cities,id',
            'arrival_time' => 'required',
            'arrival_date' => 'required',
            'arrival_city_id' => 'required|numeric|exists:cities,id',
            'rating_id' => 'required|numeric|exists:ratings,id',
            // 'return_date' => 'required',
            'ticket_price' => 'required',
            'arrive_airport_id' => 'required|numeric|exists:airports,id',
            'duration' => 'required',
            'trip_status_id' => 'required|numeric|exists:trip_statuses,id',
            'flight_trip_id' => 'required|numeric|exists:flight_trips,id',
            'weight_id' => 'required|numeric|exists:weights,id',
            'ticket_status_id' => 'required|numeric|exists:ticket_statuses,id',
            'meal_id' => 'required|numeric|exists:meals,id',
        ];
    }
}
