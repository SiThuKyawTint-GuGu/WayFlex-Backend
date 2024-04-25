<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\StoreCouponAddRequest;
use App\Models\CouponList;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CouponAddController extends Controller
{
    public function addCoupon(StoreCouponAddRequest $request)
    {

        DB::beginTransaction();
        try {
            $couponList = new CouponList();
            $couponList->user_id = $request->user()->id;
            $couponList->coupon_id = $request->coupon_id;
            $couponList->status = 'active';
            $couponList->save();

            User::where('id', $request->user()->id)->update([
                'coupon_count' => $request->user()->coupon_count - 1,
            ]);
            DB::commit();
            return response()->json($couponList);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Error Adding Coupon', 'error' => $e->getMessage()], 500);
        }
    }
}
