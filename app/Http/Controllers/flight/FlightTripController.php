<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightTripRequest;
use App\Models\FlightTrip;

class FlightTripController extends Controller
{
    protected $queryWith = ['status'];
    public function index()
    {
        return response()->json(FlightTrip::with($this->queryWith)->get());
    }

    public function store(StoreFlightTripRequest $request)
    {
        $validated = $request->all();
        $flightTrip = FlightTrip::create($validated);
        return response()->json(FlightTrip::with($this->queryWith)->find($flightTrip->id));
    }
}
