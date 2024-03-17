<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightClassRequest;
use App\Models\FlightClass;

class FlightClassController extends Controller
{
    public function index()
    {
        return response()->json(FlightClass::get());
    }

    public function store(StoreFlightClassRequest $request)
    {
        $validated = $request->all();
        $flightClass = FlightClass::create($validated);
        return response()->json(FlightClass::find($flightClass->id));
    }

}
