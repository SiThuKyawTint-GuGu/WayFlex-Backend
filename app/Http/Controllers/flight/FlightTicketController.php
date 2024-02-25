<?php

namespace App\Http\Controllers\Flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlightTicketRequest;
use App\Models\AirlineLeftCol;
use App\Models\AirlineNumber;
use App\Models\AirlineRightCol;
use App\Models\AirlineSeat;
use App\Models\FlightTicket;
use App\Models\FlightTicketPrice;
use Exception;
use Illuminate\Support\Facades\DB;


class FlightTicketController extends Controller
{
    protected $queryWith = [
        'flight_category',
        'system',
        'departure_airport',
        'departure_city.country',
        'arrival_city.country',
        'arrive_airport',
        'trip_status',
        'flight_trip',
        'weight',
        'ticket_status',
        'meal',
    ];

    public function index()
    {
        return response()->json(FlightTicket::with($this->queryWith)->get());
    }

    public function store(StoreFlightTicketRequest $request)
    {
        DB::beginTransaction();

        try {

            //Create Ticket
            $flightTicket = new FlightTicket();
            $flightTicket->name = $request->name;
            $flightTicket->flight_category_id = $request->flight_category_id;
            $flightTicket->system_id = $request->system_id;
            $flightTicket->description = $request->description;
            $flightTicket->departure_airport_id = $request->departure_airport_id;
            $flightTicket->departure_time = $request->departure_time;
            $flightTicket->departure_date = $request->departure_date;
            $flightTicket->departure_city_id = $request->departure_city_id;

            $flightTicket->arrival_time = $request->arrival_time;
            $flightTicket->arrival_date = $request->arrival_date;
            $flightTicket->arrival_city_id = $request->arrival_city_id;

            $flightTicket->rating_id = $request->rating_id;
            $flightTicket->return_date = $request->return_date;
            $flightTicket->image = $request->image;

            $flightTicket->arrive_airport_id = $request->arrive_airport_id;
            $flightTicket->duration = $request->duration;
            $flightTicket->trip_status_id = $request->trip_status_id;
            $flightTicket->flight_trip_id = $request->flight_trip_id;

            $flightTicket->weight_id = $request->weight_id;
            $flightTicket->ticket_status_id = $request->ticket_status_id;
            $flightTicket->meal_id = $request->meal_id;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('FlightTicketImage', 'public');
                $flightTicket->image = $imagePath;
            }

            $flightTicket->save();


            $flightTicketPrice = new FlightTicketPrice();
            $flightTicketPrice->price = $request->ticket_price;
            $flightTicketPrice->flight_trip_id = $request->flight_trip_id;
            $flightTicketPrice->save();


            if ($request->has("total_seat")) {
                $totalSeat = $request->total_seat;
                $leftCols = $request->left_cols;
                $rightCols = $request->right_cols;
                $totalCount = count($leftCols) + count($rightCols);


                // Create AirlineNumber
                $airlineNumber = AirlineNumber::create([
                    'number' => $request->number,
                    'airline_id' => $request->airline_id,
                    'total_seat' => $request->total_seat,
                    'flight_class_id' => $request->flight_class_id,
                    'flight_ticket_id' => $flightTicket->id,
                ]);

                // Create AirlineLeftCols
                foreach ($leftCols as $lcol) {
                    AirlineLeftCol::create([
                        'name' => $lcol,
                        'airline_number_id' => $airlineNumber->id,
                    ]);
                }

                // Create AirlineRightCols
                foreach ($rightCols as $rcol) {
                    AirlineRightCol::create([
                        'name' => $rcol,
                        'airline_number_id' => $airlineNumber->id,
                    ]);
                }

                // Create AirlineSeats
                for ($i = 1; $i <= $totalSeat / $totalCount; $i++) {
                    foreach ($leftCols as $lCol) {
                        AirlineSeat::create([
                            'airline_number_id' => $airlineNumber->id,
                            'seat_number' => $lCol . $i,
                            'seat_status' => $request->seat_status,
                            'flight_ticket_price_id' => $flightTicketPrice->id,
                        ]);
                    }

                    foreach ($rightCols as $rCol) {
                        AirlineSeat::create([
                            'airline_number_id' => $airlineNumber->id,
                            'seat_number' => $rCol . $i,
                            'seat_status' => $request->seat_status,
                            'flight_ticket_price_id' => $flightTicketPrice->id,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Flight Ticket created successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating Flight Ticket', 'error' => $e->getMessage()], 500);
        }
    }

    public function ticketCount()
    {
        $flightTicketList = FlightTicket::with(['system', 'departure_airport', 'arrive_airport', 'flight_trip'])
            ->select("id", "name", "system_id", "description", "departure_airport_id", "arrive_airport_id", "flight_trip_id")
            ->get();

        if (!$flightTicketList->isEmpty()) {
            $seatStatus = [];

            foreach ($flightTicketList as $flightTicket) {
                $airlineNumber = $this->getAirlineNumber($flightTicket);

                if ($airlineNumber) {
                    $airlineActiveSeats = $this->getSeatCount($airlineNumber->id, 'active');
                    $airlineDoneSeats = $this->getSeatCount($airlineNumber->id, 'done');

                    $seatStatus[] = [
                        'flight_ticket' => $flightTicket,
                        'airline_number' => $airlineNumber,
                        'activeSeatsCount' => $airlineActiveSeats,
                        'airlineDoneSeats' => $airlineDoneSeats,
                    ];
                }
            }
        }

        return response()->json($seatStatus ?? []);
    }

    protected function getAirlineNumber(FlightTicket $flightTicket)
    {
        return AirlineNumber::with(['flight_class'])->where('flight_ticket_id', $flightTicket->id)->first();
    }

    protected function getSeatCount($airlineNumberId, $seatStatus)
    {
        return AirlineSeat::where('airline_number_id', $airlineNumberId)
            ->where('seat_status', $seatStatus)
            ->count();
    }
}
