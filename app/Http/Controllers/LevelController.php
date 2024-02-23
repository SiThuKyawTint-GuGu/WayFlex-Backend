<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLevelRequest;
use App\Models\Level;

class LevelController extends Controller
{
    public function index(){
        return response()->json(Level::get());
    }

    public function store(StoreLevelRequest $request){
        $validated = $request->all();
        $level = Level::create($validated);
        return response()->json(Level::find($level->id));
    }
}
