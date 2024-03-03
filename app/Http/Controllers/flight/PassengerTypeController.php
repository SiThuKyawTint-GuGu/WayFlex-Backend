<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePassengerTypeRequest;
use App\Models\PassengerType;


class PassengerTypeController extends Controller
{
    public function index()
    {
        return response()->json(PassengerType::get());
    }

    public function store(StorePassengerTypeRequest $request)
    {
        $validated = $request->all();
        $passengerType = PassengerType::create($validated);
        return response()->json(PassengerType::find($passengerType->id));
    }
}
