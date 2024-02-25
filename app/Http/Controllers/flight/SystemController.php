<?php

namespace App\Http\Controllers\flight;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSystemRequest;
use App\Models\System;

class SystemController extends Controller
{
    public function index()
    {
        return response()->json(System::get());
    }

    public function store(StoreSystemRequest $request)
    {
        $validated = $request->all();
        $system = System::create($validated);
        return response()->json(System::find($system->id));
    }
}
