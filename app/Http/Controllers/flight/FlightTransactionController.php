<?php

namespace App\Http\Controllers\flight;

use Exception;
use App\Models\User;
use App\Models\Coupon;
use App\Models\System;
use App\Models\CouponList;
use App\Models\AirlineSeat;
use App\Models\PaymentForm;
use App\Models\FlightTicket;
use App\Models\PassengerQty;
use Illuminate\Http\Request;
use App\Models\AirlineNumber;
use App\Models\LevelDiscount;
use App\Models\FlightTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightTransactionRequest;
use App\Models\CouponCountTime;

class FlightTransactionController extends Controller
{

    /**
     * @var string[]|\Closure[]
     */
    protected $queryWith;

    public function __construct()
    {
        $this->queryWith = [
            "user",
            "payment_type",
            "flight_ticket" => function ($query) {
                $query->with(['departure_airport', 'arrive_airport']);
            },
            "payment_form",
            "level_discount",
            'coupon'
        ];
    }


    public function index()
    {
        $flightTransactions = FlightTransaction::with($this->queryWith)
            ->select([
                'id', 'user_id', 'coupon_id', 'flight_ticket_id',
                'total_amount', 'discount_amount', 'normal_amount',
                'passenger_count', 'seat_count'
            ])
            ->with(['passengers', 'seats'])
            ->get();

        $formattedData = $flightTransactions->map(function ($flightTransaction) {
            $passengers = $flightTransaction->passengers->map(function ($passenger) {
                return [
                    'id' => $passenger->id,
                    'passenger_type' => optional($passenger->passenger_type)->name,
                    'qty' => $passenger->qty,
                ];
            });

            $seats = $flightTransaction->seats->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'airline_number' => optional($seat->airline_number)->number,
                    'seat_number' => $seat->seat_number,
                ];
            });

            return [
                'id' => $flightTransaction->id,
                'username' => optional($flightTransaction->user)->name,
                'coupon_name' => optional($flightTransaction->coupon)->coupon_number,
                'departure_airport' => optional($flightTransaction->flight_ticket->departure_airport)->name,
                'passenger_list' => $passengers,
                'seat_list' => $seats,
                'passenger_count' => $flightTransaction->passenger_count,
                'seat_count' => $flightTransaction->seat_count,
                'arrive_airport' => optional($flightTransaction->flight_ticket->arrive_airport)->name,
                'normal_amount' => $flightTransaction->normal_amount,
                'discount_amount' => $flightTransaction->discount_amount,
                'total_amount' => $flightTransaction->total_amount,
            ];
        });

        return response()->json($formattedData);
    }

    public function store(StoreFlightTransactionRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create payment form
            $paymentForm = new PaymentForm([
                'payment_type_id' => $request->payment_type_id,
                'card_holder_name' => $request->card_holder_name,
                'card_number' => $request->card_number,
                'expiry_date' => $request->expiry_date,
                'cvv' => $request->cvv,
            ]);
            $paymentForm->save();

            // Validate and process coupons
            $coupon = $this->processCoupons($request);

            // Update user counts
            $this->updateUserCounts($request);

            // Create flight transaction
            $this->createFlightTransaction($request, $paymentForm, $coupon);

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Flight Transaction created successfully'], 200);
        } catch (Exception $e) {
            // Rollback in case of an exception
            DB::rollBack();

            return response()->json(['message' => 'Error creating Flight Transaction', 'error' => $e->getMessage()], 500);
        }
    }

    private function processCoupons(Request $request)
    {
        $coupon = null;

        // Validate and process coupons
        if ($request->coupon_number !== null) {
            $flightTicket = FlightTicket::findOrFail($request->flight_ticket_id);

            $coupon = Coupon::where('coupon_number', $request->coupon_number)->first();

            if (!$coupon) {
                throw new Exception('Invalid coupon number.');
            }

            $userCoupon = CouponList::where('user_id', $request->user()->id)
                ->where('coupon_id', $coupon->id)->first();

            if (!$userCoupon) {
                throw new Exception('This coupon is not available in your account.');
            }

            if ($userCoupon->status != 'active') {
                throw new Exception('This coupon has already been used.');
            }

            if ($coupon->system_id !== $flightTicket->system_id) {
                $systemType = System::findOrFail($coupon->system_id);
                $errorMessage = 'This coupon is not for this ticket. Only for ' . $systemType->name . ' ticket';
                throw new Exception($errorMessage);
            }

            if (now()->greaterThan($coupon->expire_date)) {
                $errorMessage = 'The coupon has expired. The expiration date was ' . $coupon->expire_date;
                throw new Exception($errorMessage);
            }

            // Update coupon status
            $userCoupon->update(['status' => 'done']);
        }

        return $coupon;
    }

    private function updateUserCounts(Request $request)
    {
        $couponCountTime = CouponCountTime::first();
        $user = User::findOrFail($request->user()->id);
        $newUserCount = $user->count + 1;

        if ($newUserCount % $couponCountTime->count === 0) {
            $user->update(['count' => $newUserCount, 'coupon_count' => $user->coupon_count + 1]);
        } else {
            $user->update(['count' => $newUserCount]);
        }
    }

    private function createFlightTransaction(Request $request, $paymentForm, $coupon)
    {
        $passengers = $request->passenger_type_id;
        $totalPassenger = 0;

        foreach ($passengers as $passenger) {
            $totalPassenger += $passenger['qty'];
        }

        $airlineNumbers = AirlineNumber::where('flight_ticket_id', $request->flight_ticket_id)->first();

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

        // Create flight transaction
        $flightTransaction = new FlightTransaction([
            'user_id' => $request->user()->id,
            'payment_type_id' => $request->payment_type_id,
            'flight_ticket_id' => $request->flight_ticket_id,
            'ticket_price' => $request->ticket_price,
            'fare_tax' => $request->fare_tax,
            'total_amount' => $request->total_amount,
            'transaction_date' => $request->transaction_date,
            'coupon_id' => $coupon ? $coupon->id : null,
            'payment_form_id' => $paymentForm->id,
            'passenger_count' => $totalPassenger,
            'seat_count' => $totalPassenger,
            'level_discount_id' => $levelDiscount->id,
            'discount_amount' => $request->discount_amount,
            'normal_amount' => $request->normal_amount,
        ]);
        $flightTransaction->save();

        // Attach passengers to the flight transaction
        foreach ($passengers as $passenger) {
            $passengerQty = PassengerQty::create([
                'passenger_type_id' => $passenger['passenger_type_id'],
                'qty' => $passenger['qty'],
            ]);
            $flightTransaction->passengers()->attach($passengerQty);
        }

        // Select seats for the flight transaction
        $selectSeats = $seatNumbers->take($totalPassenger);

        // Update seat status and attach seats to the flight transaction
        foreach ($selectSeats as $selectSeat) {
            AirlineSeat::where('id', $selectSeat->id)->update(['seat_status' => 'done']);
            $flightTransaction->seats()->attach($selectSeat->id);
        }

        return $flightTransaction;
    }

}
