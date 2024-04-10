<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLevelDiscountRequest;
use App\Models\LevelDiscount;
use Illuminate\Http\Request;

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

     public function getUserLevelDiscount(Request $request){
        $levelDiscount = LevelDiscount::with($this->queryWith)->where('level_id',$request->user()->level_id)->first();
        return response()->json($levelDiscount);
    }
}
