<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightCategoryRequest;
use App\Models\FlightCategory;

class FlightCategoryController extends Controller
{
    public function index()
    {
        $orderedFlightCategory = FlightCategory::orderBy('id', 'desc')
        ->get();

        return response()->json($orderedFlightCategory);
    }

    public function store(StoreFlightCategoryRequest $request)
    {
        $validated = $request->all();
        $flightCategory = FlightCategory::create($validated);
        return response()->json(FlightCategory::find($flightCategory->id));
    }

}
