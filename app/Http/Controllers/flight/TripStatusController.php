<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripStatusRequest;
use App\Models\TripStatus;

class TripStatusController extends Controller
{
    public function index()
    {
        $orderedTripStatus = TripStatus::orderBy('id', 'desc')
        ->get();

        return response()->json($orderedTripStatus);
    }

    public function store(StoreTripStatusRequest $request)
    {
        $validated = $request->all();
        $tripStatus = TripStatus::create($validated);
        return response()->json(TripStatus::find($tripStatus->id));
    }
}
