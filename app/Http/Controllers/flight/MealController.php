<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealRequest;
use App\Models\Meal;

class MealController extends Controller
{
    public function index()
    {
        return response()->json(Meal::get());
    }

    public function store(StoreMealRequest $request)
    {
        $validated = $request->all();
        $meal = Meal::create($validated);
        return response()->json(Meal::find($meal->id));
    }
}
