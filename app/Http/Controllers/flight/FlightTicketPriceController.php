<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightTicketPriceRequest;
use App\Models\FlightTicketPrice;
use Illuminate\Http\Request;

class FlightTicketPriceController extends Controller
{
    protected $queryWith = ['flight_trip.status'];
    public function index()
    {
        return response()->json(FlightTicketPrice::with($this->queryWith)->get());
    }

    public function store(StoreFlightTicketPriceRequest $request)
    {
        $validated = $request->all();
        $flightTicketPrice = FlightTicketPrice::create($validated);
        return response()->json(FlightTicketPrice::with($this->queryWith)->find($flightTicketPrice->id));
    }
}
