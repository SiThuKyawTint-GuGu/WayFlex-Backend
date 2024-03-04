<?php

namespace App\Http\Controllers;

use Storage;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Models\PaymentType;

class PaymentTypeController extends Controller
{
    public function index()
    {
        return response()->json(PaymentType::orderBy("id","desc")->get());
    }

    public function store(StorePaymentTypeRequest $request)
    {
        $validated = $request->validated();
        if ($request->has('image')) {
            try {
                $base64Image = $request->image;
                list($type, $data) = explode(';', $base64Image);
                list(, $data)      = explode(',', $data);
                $decodedImage = base64_decode($data);
                $filename = 'payment_type_' . time() . '.' . explode('/', $type)[1];
                Storage::disk('public')->put('Payment_types/' . $filename, $decodedImage);
                $validated['image'] = $filename;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error storing image', 'error' => $e->getMessage()], 500);
            }
        }
        $paymentType = PaymentType::create($validated);

        return response()->json(PaymentType::find($paymentType->id));
    }
}
