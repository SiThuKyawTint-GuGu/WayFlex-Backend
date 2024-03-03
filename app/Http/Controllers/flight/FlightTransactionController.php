<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Models\AirlineNumber;
use App\Models\AirlineSeat;
use App\Models\FlightTransaction;
use App\Models\LevelDiscount;
use App\Models\PassengerQty;
use App\Models\PaymentForm;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlightTransactionController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $paymentForm = new PaymentForm();
            $paymentForm->payment_type_id = $request->payment_type_id;
            $paymentForm->card_holder_name = $request->card_holder_name;
            $paymentForm->card_number = $request->card_number;
            $paymentForm->expiry_date = $request->expiry_date;
            $paymentForm->cvv = $request->cvv;
            $paymentForm->save();

            $passengers = $request->passenger_type_id;
            $totalPassenger = 0;

            foreach ($passengers as $passenger) {
                $totalPassenger += $passenger['qty'];
            }

            $airlineNumbers  = AirlineNumber::where('flight_ticket_id', $request->flight_ticket_id)
                ->select('id')
                ->first();

            $seatNumbers = AirlineSeat::where('airline_number_id', $airlineNumbers->id)
                ->where('seat_status', 'active')
                ->get();


            $availableSeatsCount = $seatNumbers->count();

            if ($availableSeatsCount < $totalPassenger) {
                $availableTickets = $availableSeatsCount === 1 ? 'ticket' : 'tickets';
                $errorMessage = "Not enough seats available. Only $availableSeatsCount $availableTickets left in the database.";
                throw new Exception($errorMessage);
            }

            $levelDiscount = LevelDiscount::where('level_id', $request->user()->level_id)->first();

            $flightTransaction = new FlightTransaction();
            $flightTransaction->user_id = $request->user()->id;
            $flightTransaction->payment_type_id = $request->payment_type_id;
            $flightTransaction->flight_ticket_id = $request->flight_ticket_id;
            $flightTransaction->ticket_price = $request->ticket_price;
            $flightTransaction->fare_tax = $request->fare_tax;
            $flightTransaction->total_amount = $request->total_amount;
            $flightTransaction->transaction_date = $request->transaction_date;
            $flightTransaction->coupon_id = $request->coupon_id;
            $flightTransaction->payment_form_id = $paymentForm->id;
            $flightTransaction->passenger_count = $totalPassenger;
            $flightTransaction->seat_count = $totalPassenger;
            $flightTransaction->level_discount_id = $levelDiscount->id;
            $flightTransaction->discount_amount = $request->discount_amount;
            $flightTransaction->normal_amount = $request->normal_amount;
            $flightTransaction->save();

            foreach ($passengers as $passenger) {
                $passengerQty =  PassengerQty::create([
                    'passenger_type_id' => $passenger['passenger_type_id'],
                    'qty' => $passenger['qty'],
                ]);

                $flightTransaction->passengers()->attach($passengerQty);
            }

            $selectSeats = $seatNumbers->take($totalPassenger);


            foreach ($selectSeats as $selectSeat) {
                AirlineSeat::where('id', $selectSeat->id)->update([
                    'seat_status' => 'done'
                ]);
                $flightTransaction->seats()->attach($selectSeat->id);
            }


            DB::commit();
            return response()->json(['message' => 'Flight Transaction created successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating Flight Transaction', 'error' => $e->getMessage()], 500);
        }
    }
}
