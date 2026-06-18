<?php

namespace App\Http\Controllers;

use App\Models\LocationLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $phones = $user->phones()->with('latestLocation')->latest()->get();

        $stats = [
            'total'     => $phones->count(),
            'active'    => $phones->where('status', 'active')->count(),
            'lost'      => $phones->where('status', 'lost')->count(),
            'stolen'    => $phones->where('status', 'stolen')->count(),
            'recovered' => $phones->where('status', 'recovered')->count(),
        ];

        // Points for the overview map (latest known position of each phone).
        $points = $phones->filter(fn ($p) => $p->latestLocation)
            ->map(fn ($p) => [
                'lat'    => (float) $p->latestLocation->latitude,
                'lng'    => (float) $p->latestLocation->longitude,
                'label'  => $p->label,
                'status' => $p->status,
                'url'    => route('phones.show', $p),
                'time'   => $p->latestLocation->created_at->diffForHumans(),
            ])->values();

        $recent = LocationLog::whereIn('phone_id', $phones->pluck('id'))
            ->with('phone')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact('phones', 'stats', 'points', 'recent'));
    }
}
