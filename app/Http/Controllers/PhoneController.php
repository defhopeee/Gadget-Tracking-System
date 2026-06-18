<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PhoneController extends Controller
{
    public function index()
    {
        $phones = Phone::with('latestLocation')->latest()->get();

        return view('phones.index', compact('phones'));
    }

    public function create()
    {
        return view('phones.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('open_modal', 'addGadgetModal');
        }

        $data = $validator->validated();
        $data['user_id'] = $this->defaultUserId();

        $phone = Phone::create($data);

        return redirect()->route('phones.show', $phone)->with('status', 'Gadget added.');
    }

    public function show(Phone $phone)
    {
        $phone->load(['locations' => fn ($q) => $q->limit(100)]);

        $points = $phone->locations
            ->map(fn ($l) => [
                'lat'    => (float) $l->latitude,
                'lng'    => (float) $l->longitude,
                'label'  => $l->label,
                'time'   => $l->created_at->toDayDateTimeString(),
                'source' => $l->source,
            ])
            ->values();

        return view('phones.show', compact('phone', 'points'));
    }

    public function edit(Phone $phone)
    {
        return view('phones.edit', compact('phone'));
    }

    public function update(Request $request, Phone $phone)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('open_modal', 'editGadgetModal');
        }

        $phone->update($validator->validated());

        return redirect()->route('phones.show', $phone)->with('status', 'Gadget updated.');
    }

    public function destroy(Phone $phone)
    {
        $phone->delete();

        return redirect()->route('phones.index')->with('status', 'Gadget and its location history removed.');
    }

    public function regenerateKey(Phone $phone)
    {
        $phone->update(['api_key' => Phone::generateApiKey()]);

        return back()->with('status', 'A new API key has been generated.');
    }

    /** Owner for newly registered gadgets while authentication is disabled. */
    private function defaultUserId(): int
    {
        $user = User::query()->oldest('id')->first();

        if (! $user) {
            $user = User::create([
                'name'     => 'Owner',
                'email'    => 'owner@example.com',
                'password' => Str::random(32),
            ]);
        }

        return $user->id;
    }

    private function rules(): array
    {
        return [
            'label'        => ['required', 'string', 'max:100'],
            'type'         => ['required', 'in:' . implode(',', array_keys(Phone::TYPES))],
            'imei'         => ['nullable', 'string', 'max:32'],
            'phone_number' => ['nullable', 'string', 'max:32'],
            'owner_name'   => ['nullable', 'string', 'max:100'],
            'status'       => ['required', 'in:active,inactive,lost,stolen,recovered'],
        ];
    }
}
