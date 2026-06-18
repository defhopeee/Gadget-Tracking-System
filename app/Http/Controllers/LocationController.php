<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /** Store a location entered through the web UI (manual or browser geolocation). */
    public function store(Request $request, Phone $phone)
    {
        abort_unless($phone->user_id === Auth::id(), 403);

        $data = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy'  => ['nullable', 'numeric', 'min:0'],
            'battery'   => ['nullable', 'integer', 'between:0,100'],
            'label'     => ['nullable', 'string', 'max:120'],
            'source'    => ['nullable', 'in:manual,browser'],
        ]);

        $data['source'] = $data['source'] ?? 'manual';
        $phone->locations()->create($data);

        return back()->with('status', 'Location recorded.');
    }

    public function destroy(Phone $phone, $location)
    {
        abort_unless($phone->user_id === Auth::id(), 403);

        $phone->locations()->whereKey($location)->delete();

        return back()->with('status', 'Location entry deleted.');
    }
}
