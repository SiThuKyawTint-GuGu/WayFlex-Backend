<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Models\Weight;
use Illuminate\Http\Request;

class WeightController extends Controller
{
    public function index()
    {
        return response()->json(Weight::get());
    }

    public function store(Request $request)
    {
        $validated = $request->all();
        $weight = Weight::create($validated);
        return response()->json(Weight::find($weight->id));
    }
}
