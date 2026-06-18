<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /** Store a location entered through the UI, a browser, or live tracking. */
    public function store(Request $request, Phone $phone)
    {
        $validator = Validator::make($request->all(), [
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy'  => ['nullable', 'numeric', 'min:0'],
            'battery'   => ['nullable', 'integer', 'between:0,100'],
            'label'     => ['nullable', 'string', 'max:120'],
            'source'    => ['nullable', 'in:manual,browser'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput()->with('open_modal', 'addLocationModal');
        }

        $data = $validator->validated();
        $data['source'] = $data['source'] ?? 'manual';

        $location = $phone->locations()->create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'       => true,
                'location' => [
                    'lat'  => (float) $location->latitude,
                    'lng'  => (float) $location->longitude,
                    'time' => $location->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('status', 'Location recorded.');
    }
}
