<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirportRequest;
use App\Models\Airport;

class AirportController extends Controller
{
    protected $queryWith = ["city.country"];

    public function index()
    {
        return response()->json(Airport::with($this->queryWith)->get());
    }

    public function store(StoreAirportRequest $request)
    {
        $validated = $request->all();
        $airport = Airport::create($validated);
        return response()->json(Airport::with($this->queryWith)->find($airport->id));
    }
}
