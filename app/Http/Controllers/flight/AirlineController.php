<?php

namespace App\Http\Controllers\flight;

use Storage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAirlineRequest;
use App\Models\Airline;

class AirlineController extends Controller
{
    public function index()
    {
        return response()->json(Airline::orderBy('id','desc')->get());
    }

    public function store(StoreAirlineRequest $request)
    {
        $validated = $request->validated();
        if ($request->has('image')) {
            try {
                $base64Image = $request->image;
                list($type, $data) = explode(';', $base64Image);
                list(, $data)      = explode(',', $data);
                $decodedImage = base64_decode($data);
                $filename = 'airline_image_' . time() . '.' . explode('/', $type)[1];
                Storage::disk('public')->put('AirlineImages/' . $filename, $decodedImage);
                $validated['image'] = $filename;
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error storing image', 'error' => $e->getMessage()], 500);
            }
        }

        $airline = Airline::create($validated);

        return response()->json(Airline::find($airline->id));
    }
}
