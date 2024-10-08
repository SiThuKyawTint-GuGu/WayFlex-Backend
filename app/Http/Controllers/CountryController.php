<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCountryRequest;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $orderedCountry = Country::orderBy('id', 'desc')
            ->get();

        return response()->json($orderedCountry);
    }

    public function store(StoreCountryRequest $request)
    {
        $validated = $request->all();
        $country = Country::create($validated);
        return response()->json(Country::find($country->id));
    }
}
