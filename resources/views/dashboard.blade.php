@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
        <a href="{{ route('phones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Register a gadget
        </a>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['Total gadgets', $stats['total'], 'cpu', 'primary'],
                ['Active', $stats['active'], 'check-circle', 'success'],
                ['Lost', $stats['lost'], 'question-circle', 'warning'],
                ['Stolen', $stats['stolen'], 'shield-exclamation', 'danger'],
                ['Recovered', $stats['recovered'], 'arrow-counterclockwise', 'info'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $icon, $color])
            <div class="col-6 col-md">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-{{ $color }} fs-3"><i class="bi bi-{{ $icon }}"></i></div>
                        <div class="display-6 fw-bold">{{ $value }}</div>
                        <div class="text-muted small text-uppercase">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-geo-alt"></i> Latest known locations
                </div>
                <div class="card-body p-0">
                    <div id="map" class="overview-map" data-points='@json($points)'></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-clock-history"></i> Recent check-ins
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($recent as $log)
                        <li class="list-group-item">
                            <a href="{{ route('phones.show', $log->phone) }}" class="fw-semibold text-decoration-none">
                                {{ $log->phone->label }}
                            </a>
                            <span class="badge bg-light text-dark border ms-1">{{ $log->source }}</span>
                            <div class="small text-muted">
                                {{ number_format($log->latitude, 5) }}, {{ number_format($log->longitude, 5) }}
                                &middot; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">
                            No location reports yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
