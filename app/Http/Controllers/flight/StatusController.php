<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStatusRequest;
use App\Models\Status;

class StatusController extends Controller
{
    public function index()
    {
        return response()->json(Status::get());
    }

    public function store(StoreStatusRequest $request)
    {
        $validated = $request->all();
        $status = Status::create($validated);
        return response()->json(Status::find($status->id));
    }
}
