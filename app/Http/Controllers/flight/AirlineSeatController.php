<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirlineSeatRequest;
use App\Models\AirlineSeat;

class AirlineSeatController extends Controller
{
    protected $queryWith = ["airline_number.airline"];

    public function index()
    {
        return response()->json(AirlineSeat::with($this->queryWith)->get());
    }

    public function store(StoreAirlineSeatRequest $request)
    {
        $validated = $request->all();
        $airlineSeat = AirlineSeat::create($validated);
        return response()->json(AirlineSeat::with($this->queryWith)->find($airlineSeat->id));
    }
}
