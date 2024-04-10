<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePaymentFormRequest;
use App\Models\PaymentForm;


class PaymentFormController extends Controller
{
      protected $queryWith = ['payment_type','user'];

      public function index(Request $request)
    {
        return response()->json(PaymentForm::where('user_id',$request->user()->id)->with($this->queryWith)->get());
    }


     public function store(StorePaymentFormRequest $request)
    {
        $validatedData = $request->validated();
        $paymentForm = new PaymentForm();
        $userId = $request->user()->id;
        $validatedData['user_id'] = $userId;
        $paymentForm->fill($validatedData);
        $paymentForm->save();
        return response()->json(['message' => 'Payment form submitted successfully'], 200);
    }

    public function destroy($id)
    {
        $paymentForm = PaymentForm::find($id);
        if ($paymentForm) {
            $paymentForm->delete();
            return response()->json(['message' => 'Successfully Deleted.', 'id' => $paymentForm->id]);
        } else {
            return response()->json(['message' => "Not found id: " . $id], 404);
        }
    }
}
