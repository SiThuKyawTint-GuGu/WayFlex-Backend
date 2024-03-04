<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponAddRequest;
use App\Models\CouponList;

class CouponAddController extends Controller
{
  public function addCoupon(StoreCouponAddRequest $request){
    $couponList = new CouponList();
    $couponList->user_id = $request->user()->id;
    $couponList->coupon_id = $request ->coupon_id;
    $couponList->status ='active';
    $couponList->save();

    return response()->json($couponList);
  }
}
