<?php

namespace App\Http\Controllers;

use App\Models\FlightTransaction;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function getUserTicketCalendar(Request $request)
    {
        $flightTicketTran = FlightTransaction::with(['flight_ticket', 'flight_ticket.departure_city', 'flight_ticket.arrival_city', 'flight_ticket.system', 'flight_ticket.flight_trip'])
            ->where('user_id', $request->user()->id)
            ->get();

        $formattedData = $flightTicketTran->groupBy('transaction_date')->map(function ($transactions) {
            return $transactions->map(function ($transaction) {
                return [
                    'departure_time' => $transaction->flight_ticket->departure_time,
                    'arrival_time' => $transaction->flight_ticket->arrival_time,
                    'departure_city' => $transaction->flight_ticket->departure_city->name,
                    'arrival_city' => $transaction->flight_ticket->arrival_city->name,
                    'system' => $transaction->flight_ticket->system->name,
                    'trip' => $transaction->flight_ticket->flight_trip->name,
                    'departure_date' => $transaction->flight_ticket->departure_date,
                    'return_date' => $transaction->flight_ticket->return_date,
                ];
            });
        });

        $result = [];

        foreach ($formattedData as $date => $data) {
            $result[$date] = $data->toArray();
        }

        return response()->json($result);
    }
}
