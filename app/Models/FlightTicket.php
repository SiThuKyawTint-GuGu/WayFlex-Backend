<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightTicket extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "flight_category_id",
        "system_id",
        "description",
        "departure_airport_id",
        "departure_time",
        "departure_date",
        "departure_city_id",
        "arrival_time",
        "arrival_date",
        "arrival_city_id",
        "rating_id",
        "return_date",
        "arrive_airport_id",
        "duration",
        "trip_status_id",
        "flight_trip_id",
        "weight_id",
        "ticket_status_id",
        "meal_id",
        "image"
    ];

    public function flight_category()
    {
        return $this->belongsTo(FlightCategory::class);
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function departure_airport()
    {
        return $this->belongsTo(Airport::class, "departure_airport_id", "id");
    }

    public function departure_city()
    {
        return $this->belongsTo(City::class, "departure_city_id", "id");
    }

    public function arrival_city()
    {
        return $this->belongsTo(City::class, "arrival_city_id", "id");
    }

    public function arrive_airport()
    {
        return $this->belongsTo(Airport::class, "arrive_airport_id", "id");
    }

    public function trip_status()
    {
        return $this->belongsTo(TripStatus::class);
    }

    public function flight_trip()
    {
        return $this->belongsTo(FlightTrip::class);
    }


    public function weight()
    {
        return $this->belongsTo(Weight::class);
    }

    public function ticket_status()
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
