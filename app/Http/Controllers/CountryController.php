<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCountryRequest;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        return response()->json(Country::get());
    }

    public function store(StoreCountryRequest $request)
    {
        $validated = $request->all();
        $country = Country::create($validated);
        return response()->json(Country::find($country->id));
    }
}
