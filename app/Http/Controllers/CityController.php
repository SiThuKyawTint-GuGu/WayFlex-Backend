<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Models\City;

class CityController extends Controller
{
    protected $queryWith = ['country'];

    public function index()
    {
        $orderedCity = City::with($this->queryWith)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($orderedCity);
    }

    public function store(StoreCityRequest $request)
    {
        $validated = $request->all();
        $city = City::create($validated);
        return response()->json(City::with($this->queryWith)->find($city->id));
    }
}
