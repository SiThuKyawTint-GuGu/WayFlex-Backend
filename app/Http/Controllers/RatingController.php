<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Rating;

class RatingController extends Controller
{
    public function index()
    {
        return response()->json(Rating::get());
    }

    public function store(StoreRatingRequest $request)
    {
        $validated = $request->all();
        $rating = Rating::create($validated);
        return response()->json(Rating::find($rating->id));
    }
}
