<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirlineNumberRequest;
use App\Models\AirlineNumber;

class AirlineNumberController extends Controller
{
    protected $queryWith = ["airline", "left_col", "right_col", "flight_class"];

    public function index()
    {
        return response()->json(AirlineNumber::with($this->queryWith)->get());
    }

    public function store(StoreAirlineNumberRequest $request)
    {
        $validated = $request->all();
        $airlineNumber = AirlineNumber::create($validated);
        return response()->json(AirlineNumber::with($this->queryWith)->find($airlineNumber->id));
    }
}
