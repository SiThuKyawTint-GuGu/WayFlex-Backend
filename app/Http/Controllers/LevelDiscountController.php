<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLevelDiscountRequest;
use App\Models\LevelDiscount;

class LevelDiscountController extends Controller
{
    protected $queryWith = ['level'];

    public function index()
    {
        return response()->json(LevelDiscount::with($this->queryWith)->orderBy('id', 'desc')->get());
    }

    public function store(StoreLevelDiscountRequest $request)
    {
        $validated = $request->all();
        $levelDiscount = LevelDiscount::create($validated);
        return response()->json(LevelDiscount::with($this->queryWith)->find($levelDiscount->id));
    }
}
