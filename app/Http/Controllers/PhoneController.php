<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneController extends Controller
{
    /** Ensure the phone belongs to the current user. */
    private function authorizePhone(Phone $phone): void
    {
        abort_unless($phone->user_id === Auth::id(), 403);
    }

    public function index()
    {
        $phones = Auth::user()->phones()->with('latestLocation')->latest()->get();

        return view('phones.index', compact('phones'));
    }

    public function create()
    {
        return view('phones.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePhone($request);
        $data['user_id'] = Auth::id();

        $phone = Phone::create($data);

        return redirect()
            ->route('phones.show', $phone)
            ->with('status', 'Phone registered. Its tracking API key is shown below.');
    }

    public function show(Phone $phone)
    {
        $this->authorizePhone($phone);

        $phone->load(['locations' => fn ($q) => $q->limit(100)]);

        $points = $phone->locations
            ->map(fn ($l) => [
                'lat'   => (float) $l->latitude,
                'lng'   => (float) $l->longitude,
                'label' => $l->label,
                'time'  => $l->created_at->toDayDateTimeString(),
                'source'=> $l->source,
            ])
            ->values();

        return view('phones.show', compact('phone', 'points'));
    }

    public function edit(Phone $phone)
    {
        $this->authorizePhone($phone);

        return view('phones.edit', compact('phone'));
    }

    public function update(Request $request, Phone $phone)
    {
        $this->authorizePhone($phone);

        $phone->update($this->validatePhone($request));

        return redirect()
            ->route('phones.show', $phone)
            ->with('status', 'Phone details updated.');
    }

    public function destroy(Phone $phone)
    {
        $this->authorizePhone($phone);

        $phone->delete();

        return redirect()
            ->route('phones.index')
            ->with('status', 'Phone and its location history removed.');
    }

    public function regenerateKey(Phone $phone)
    {
        $this->authorizePhone($phone);

        $phone->update(['api_key' => Phone::generateApiKey()]);

        return back()->with('status', 'A new API key has been generated.');
    }

    private function validatePhone(Request $request): array
    {
        return $request->validate([
            'label'        => ['required', 'string', 'max:100'],
            'type'         => ['required', 'in:' . implode(',', array_keys(\App\Models\Phone::TYPES))],
            'brand'        => ['nullable', 'string', 'max:60'],
            'model'        => ['nullable', 'string', 'max:60'],
            'imei'         => ['nullable', 'string', 'max:32'],
            'phone_number' => ['nullable', 'string', 'max:32'],
            'owner_name'   => ['nullable', 'string', 'max:100'],
            'status'       => ['required', 'in:active,inactive,lost,stolen,recovered'],
        ]);
    }
}
