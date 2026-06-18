@extends('layouts.app')
@section('title', 'Phones')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Registered phones</h1>
        <a href="{{ route('phones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add phone
        </a>
    </div>

    @if ($phones->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="display-1 text-muted"><i class="bi bi-phone"></i></div>
                <p class="lead text-muted">You haven't registered any phones yet.</p>
                <a href="{{ route('phones.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Register your first phone
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Label</th>
                            <th>Device</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Last seen</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($phones as $phone)
                            <tr>
                                <td>
                                    <a href="{{ route('phones.show', $phone) }}" class="fw-semibold text-decoration-none">
                                        {{ $phone->label }}
                                    </a>
                                </td>
                                <td class="text-muted">{{ trim($phone->brand . ' ' . $phone->model) ?: '—' }}</td>
                                <td class="text-muted">{{ $phone->owner_name ?: '—' }}</td>
                                <td><span class="badge bg-{{ $phone->statusColor() }}">{{ ucfirst($phone->status) }}</span></td>
                                <td class="text-muted small">
                                    @if ($phone->latestLocation)
                                        {{ $phone->latestLocation->created_at->diffForHumans() }}
                                    @else
                                        <span class="text-muted">never</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('phones.show', $phone) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-geo-alt"></i> Track
                                    </a>
                                    <a href="{{ route('phones.edit', $phone) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
