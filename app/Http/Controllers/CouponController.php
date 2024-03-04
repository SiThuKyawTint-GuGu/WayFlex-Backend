<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Models\Coupon;

class CouponController extends Controller
{
    protected $queryWith = ["system"];

    public function index()
    {
        return response()->json(Coupon::with($this->queryWith)->orderBy("id","desc")->get());
    }

    public function store(StoreCouponRequest $request)
    {
        $validated = $request->all();
        $coupon = Coupon::create($validated);
        return response()->json(Coupon::with($this->queryWith)->find($coupon->id));
    }
}
