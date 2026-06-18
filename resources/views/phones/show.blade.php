@extends('layouts.app')
@section('title', $phone->label)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('phones.index') }}">Gadgets</a></li>
            <li class="breadcrumb-item active">{{ $phone->label }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi {{ $phone->typeIcon() }} text-muted"></i>
                {{ $phone->label }}
                <span class="badge bg-{{ $phone->statusColor() }} align-middle">{{ ucfirst($phone->status) }}</span>
            </h1>
            <div class="text-muted">
                {{ $phone->typeLabel() }}
                @if ($phone->owner_name) &middot; {{ $phone->owner_name }} @endif
            </div>
        </div>
        <div>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editGadgetModal">
                <i class="bi bi-pencil"></i> Edit
            </button>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteGadgetModal">
                <i class="bi bi-trash"></i> Delete
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">
                    <i class="bi bi-map"></i> Location trail
                    <span class="text-muted small">({{ $phone->locations->count() }} points)</span>
                </div>
                <div class="card-body p-0">
                    <div id="map" class="phone-map" data-points='@json($points)' data-path="1"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-clock-history"></i> Location history</div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>When</th><th>Latitude</th><th>Longitude</th><th>Source</th><th>Battery</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($phone->locations as $log)
                                <tr>
                                    <td title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</td>
                                    <td>{{ number_format($log->latitude, 6) }}</td>
                                    <td>{{ number_format($log->longitude, 6) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $log->source }}</span></td>
                                    <td>{{ $log->battery !== null ? $log->battery . '%' : 'Not set' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No locations recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-geo-alt"></i> Location</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                            <i class="bi bi-plus-lg"></i> Add location
                        </button>
                        <button id="live-toggle" class="btn btn-outline-success" data-url="{{ route('locations.store', $phone) }}">
                            <i class="bi bi-broadcast"></i> Start live tracking
                        </button>
                        <div id="live-status" class="small text-muted text-center"></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-info-circle"></i> Details</div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">IMEI</span><span>{{ $phone->imei ?: 'Not set' }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Number</span><span>{{ $phone->phone_number ?: 'Not set' }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Registered</span><span>{{ $phone->created_at->toFormattedDateString() }}</span></li>
                </ul>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-key"></i> Device API key</div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        Point a check-in script on the device at the reporting endpoint using this key.
                    </p>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control font-monospace" id="api-key" value="{{ $phone->api_key }}" readonly>
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="navigator.clipboard.writeText(document.getElementById('api-key').value)">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <pre class="api-snippet"><code>curl -X POST {{ url('/api/reportlocation') }} \
  -H "X-Api-Key: {{ $phone->api_key }}" \
  -d "latitude=51.5074" \
  -d "longitude=-0.1278" \
  -d "battery=82"</code></pre>
                    <form method="POST" action="{{ route('phones.regeneratekey', $phone) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-warning w-100" type="submit">
                            <i class="bi bi-arrow-repeat"></i> Regenerate key
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit gadget modal --}}
    <div class="modal fade" id="editGadgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('phones.update', $phone) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit gadget</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">@include('phones._form', ['phone' => $phone])</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete gadget modal --}}
    <div class="modal fade" id="deleteGadgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle"></i> Delete gadget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Delete <strong>{{ $phone->label }}</strong> and its entire location history?
                    This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('phones.destroy', $phone) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger"><i class="bi bi-trash"></i> Delete gadget</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Add location modal --}}
    <div class="modal fade" id="addLocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('locations.store', $phone) }}">
                    @csrf
                    <input type="hidden" name="source" id="loc-source" value="manual">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-geo-alt"></i> Add location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Latitude</label>
                                <input type="number" step="any" name="latitude" id="loc-lat"
                                       class="form-control @error('latitude') is-invalid @enderror"
                                       value="{{ old('latitude') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Longitude</label>
                                <input type="number" step="any" name="longitude" id="loc-lng"
                                       class="form-control @error('longitude') is-invalid @enderror"
                                       value="{{ old('longitude') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Battery (%)</label>
                                <input type="number" min="0" max="100" name="battery" class="form-control" value="{{ old('battery') }}">
                            </div>
                        </div>
                        <button type="button" id="use-my-location" class="btn btn-outline-primary btn-sm w-100 mt-3">
                            <i class="bi bi-crosshair"></i> Use this browser's location
                        </button>
                        <div class="form-text">
                            Tip: on the phone, open Google Maps, long-press your spot, and copy the two numbers.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Save location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
