<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealRequest;
use App\Models\Meal;

class MealController extends Controller
{
    protected $queryWith = ['status'];

    public function index()
    {
        return response()->json(Meal::with($this->queryWith)->orderBy('id', 'desc')->get());
    }

    public function store(StoreMealRequest $request)
    {
        $validated = $request->all();
        if(!isset($validated['name'])){
            $validated['name'] = "not selected";
        }
        $meal = Meal::create($validated);
        return response()->json(Meal::with($this->queryWith)->find($meal->id));
    }
}
