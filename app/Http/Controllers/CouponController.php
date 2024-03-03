<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index()
    {
        return response()->json(Coupon::get());
    }

    public function store(StoreCouponRequest $request)
    {
        $validated = $request->all();
        $coupon = Coupon::create($validated);
        return response()->json(Coupon::find($coupon->id));
    }
}
