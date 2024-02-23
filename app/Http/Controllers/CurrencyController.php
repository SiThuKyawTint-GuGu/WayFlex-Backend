<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        return response()->json(Currency::get());
    }

    public function store(StoreCurrencyRequest $request)
    {
        $validated = $request->all();
        $currency = Currency::create($validated);
        return response()->json(Currency::find($currency->id));
    }
}
