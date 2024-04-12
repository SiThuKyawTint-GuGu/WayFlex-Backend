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
use App\Models\FlightTrip;


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
    public function getUserFlightTransaction(Request $request)
    {
        $userFlightTran = FlightTransaction::where('user_id', $request->user()->id)
            ->get();

        $groupedTransactions = $userFlightTran->groupBy('transaction_date')->map(function($transaction){
            $totalAmount = $transaction->sum('total_amount');
            return [
                'transaction_date' => $transaction->first()->transaction_date,
                'total_amount' => $totalAmount,
                'transactions' => $transaction,
            ];
        });

        return response()->json($groupedTransactions);
    }

    public function store(StoreFlightTransactionRequest $request)
    {
        DB::beginTransaction();

        try {
            $paymentForm = PaymentForm::where('id', $request->payment_form_id)->first();

            // Validate and process coupons
            $coupon = $this->processCoupons($request);

            // Update user counts
            $this->updateUserCounts($request);

            // Create flight transaction
            $flightTransaction = $this->createFlightTransaction($request, $paymentForm, $coupon);

            // Commit the transaction
            DB::commit();

            return response()->json($flightTransaction, 200);
        } catch (Exception $e) {
            // Rollback in case of an exception
            DB::rollBack();

            return response()->json(['message' => 'Error creating Flight Transaction', 'error' => $e->getMessage()], 500);
        }
    }


    public function searchFlightTicket(Request $request)
    {
        try {
            $query = FlightTicket::with(['flight_category', 'system', 'departure_airport', 'departure_city.country', 'arrival_city.country', 'arrive_airport', 'trip_status', 'flight_trip', 'weight', 'ticket_status', 'meal'])
                ->where('departure_airport_id', $request->departure_airport_id);

            if ($request->has('arrive_airport_id')) {
                $query->where('arrive_airport_id', $request->arrive_airport_id);
            }

            $ticket = $query->first();

            if (!$ticket) {
                return response()->json(['message' => 'No available ticket found for the given criteria.'], 404);
            }

            if ($request->has('departure_date') && $request->has('return_date')) {
                if ($ticket->departure_date !== $request->departure_date || $ticket->return_date !== $request->return_date) {
                    return response()->json(['message' => 'Ticket is not available for the specified departure and return dates.'], 404);
                }
            } elseif ($request->has('departure_date') && !$request->has('return_date')) {
                if ($ticket->departure_date !== $request->departure_date) {
                    return response()->json(['message' => 'Ticket is not available for the specified departure date.'], 404);
                }
            }

            if ($request->has('flight_trip_id')) {
                if ($ticket->flight_trip_id != $request->flight_trip_id) {
                    $flightTrip = FlightTrip::find($request->flight_trip_id);
                    if (!$flightTrip) {
                        return response()->json(['message' => 'Flight trip not found.'], 404);
                    }
                    return response()->json(['message' => 'The ticket does not belong to ' . $flightTrip->name], 404);
                }
            }

            if ($request->has('travelers')) {
                $qty = 0;
                $travelersData = $request->travelers;
                foreach ($travelersData as $item) {
                    $qty += $item['qty'];
                }
                $airlineNumbers = AirlineNumber::where('flight_ticket_id', $ticket->id)->first();

                $seatNumbers = AirlineSeat::where('airline_number_id', $airlineNumbers->id)
                    ->where('seat_status', 'active')
                    ->get();

                $availableSeatsCount = $seatNumbers->count();
                if ($availableSeatsCount < $qty) {
                    $errorMessage = "Not enough seats available. Only $availableSeatsCount seats left in the database.";
                    return response()->json(['message' => $errorMessage], 404);
                }
            }

            if ($request->has('flight_class_id')) {
                $airlineNumber = AirlineNumber::where('flight_ticket_id', $ticket->id)->first();
                if ($airlineNumber->flight_class_id != $request->flight_class_id) {
                    $flightClass = AirlineNumber::with(['flight_class'])->find($airlineNumber->id);
                    if (!$flightClass) {
                        return response()->json(['message' => 'Flight Class not found.'], 404);
                    }
                    return response()->json(['message' => 'The ticket is only for ' . $flightClass->flight_class->name . ' Class'], 404);
                }
            }

            if ($ticket) {
                $AirlineNumber = AirlineNumber::with(['airline'])->where('flight_ticket_id', $ticket->id)->first();
                $ticket->flight_number = $AirlineNumber;
                $price = AirlineSeat::with(['flight_ticket_price'])->where('id', $AirlineNumber->id)->first();
                $ticket->ticket_price = $price;
            }

            return response()->json($ticket, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    public function searchFlightSeat(Request $request)
    {
        $ticketID = $request->ticket_id;
        if ($ticketID) {
            $AirlineNumber = AirlineNumber::where('flight_ticket_id', $ticketID)->first();
            if ($AirlineNumber) {
                $seats = AirlineSeat::where('airline_number_id', $AirlineNumber->id)->get();
                return response()->json($seats, 200);
            } else {
                return response()->json(['error' => 'Flight number not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Ticket ID is required'], 400);
        }
    }

    public function checkCoupon(Request $request)
    {
        $coupon = null;

        // Validate and process coupons
        if ($request->coupon_number !== null) {
            $flightTicket = FlightTicket::findOrFail($request->flight_ticket_id);

            $coupon = Coupon::where('coupon_number', $request->coupon_number)->first();

            if (!$coupon) {
                return response()->json(['error' => 'Invalid coupon number.'], 400);
            }

            $userCoupon = CouponList::where('user_id', $request->user()->id)
                ->where('coupon_id', $coupon->id)->first();

            if (!$userCoupon) {
                return response()->json(['error' => 'This coupon is not available in your account.'], 400);
            }

            if ($userCoupon->status != 'active') {
                return response()->json(['error' => 'This coupon has already been used.'], 400);
            }

            if ($coupon->system_id !== $flightTicket->system_id) {
                $systemType = System::findOrFail($coupon->system_id);
                $errorMessage = 'This coupon is not for this ticket. Only for ' . $systemType->name . ' ticket';
                return response()->json(['error' => $errorMessage], 400);
            }

            if (now()->greaterThan($coupon->expire_date)) {
                $errorMessage = 'The coupon has expired. The expiration date was ' . $coupon->expire_date;
                return response()->json(['error' => $errorMessage], 400);
            }
        } else {
            return response()->json(['error' => 'Coupon is required!'], 400);
        }

        return response()->json($coupon, 200);
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

        //Create Ticket Number
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 9;
        $ticketNumber = '#';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $ticketNumber .= $characters[$randomIndex];
        }
        $flightTransaction = new FlightTransaction([
            'ticket_number' => $ticketNumber,
            'user_id' => $request->user()->id,
            'payment_type_id' => $paymentForm->payment_type_id,
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

        $soldOutSeat = [];
        $seats = $request->seat_type_id;
        foreach ($seats as $seat) {
            $seatStatus = AirlineSeat::where('id', $seat)->first();
            if ($seatStatus->seat_status !== 'active') {
                $soldOutSeat[] = $seatStatus->seat_number;
            } else {
                AirlineSeat::where('id', $seat)->update(['seat_status' => 'done']);
                $flightTransaction->seats()->attach($seat);
            }
        }

        if (!empty($soldOutSeat)) {
            $errorMessage = "Seat Numbers " . implode(',', $soldOutSeat) . " are sold out!";
            throw new Exception($errorMessage);
        }

        $flightTransaction->load([
            'user',
            'payment_type',
            'flight_ticket' => function ($query) {
                $query->with(
                    'departure_airport',
                    'departure_city.country',
                    'arrival_city.country',
                    'arrive_airport',
                    'trip_status',
                    'flight_trip',
                );
            },
            'coupon',
            'payment_form',
            'level_discount',
            'passengers',
            'seats' => function ($query) {
                $query->with('airline_number.airline', 'airline_number.flight_class');
            }
        ]);

        return $flightTransaction;
    }
}
