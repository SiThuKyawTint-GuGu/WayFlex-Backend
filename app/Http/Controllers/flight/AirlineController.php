<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirlineRequest;
use App\Models\Airline;

class AirlineController extends Controller
{
    public function index()
    {
        return response()->json(Airline::get());
    }

    public function store(StoreAirlineRequest $request)
    {
        $validated = $request->all();
        $airline = Airline::create($validated);
        return response()->json(Airline::find($airline->id));
    }
}
