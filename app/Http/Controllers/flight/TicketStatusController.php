<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketStatusRequest;
use App\Models\TicketStatus;

class TicketStatusController extends Controller
{
    public function index()
    {
        return response()->json(TicketStatus::get());
    }

    public function store(StoreTicketStatusRequest $request)
    {
        $validated = $request->all();
        $ticketStatus = TicketStatus::create($validated);
        return response()->json(TicketStatus::find($ticketStatus->id));
    }
}
