<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightTripRequest;
use App\Models\FlightTrip;
use Illuminate\Http\Request;

class FlightTripController extends Controller
{
    protected $queryWith = ['status'];

    public function index(Request $request)
    {
        $query = FlightTrip::with($this->queryWith);

        if ($request->has('flight_trip_id')) {
            $query->where('id', $request->flight_trip_id);
        }
        $query->orderBy('id', 'desc');
        $flightTrips = $query->get();

        return response()->json($flightTrips);
    }

    public function store(StoreFlightTripRequest $request)
    {
        $validated = $request->all();
        $flightTrip = FlightTrip::create($validated);
        return response()->json(FlightTrip::with($this->queryWith)->find($flightTrip->id));
    }
}
