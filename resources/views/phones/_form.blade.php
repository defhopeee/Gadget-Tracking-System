@php $phone = $phone ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Label <span class="text-danger">*</span></label>
        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
               value="{{ old('label', $phone->label ?? '') }}" placeholder="e.g. Work iPhone" required>
        @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Device type</label>
        <select name="type" class="form-select">
            @foreach (\App\Models\Phone::TYPES as $key => [$tlabel, $ticon])
                <option value="{{ $key }}" @selected(old('type', $phone->type ?? 'phone') === $key)>
                    {{ $tlabel }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach (['active','inactive','lost','stolen','recovered'] as $s)
                <option value="{{ $s }}" @selected(old('status', $phone->status ?? 'active') === $s)>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Brand</label>
        <input type="text" name="brand" class="form-control"
               value="{{ old('brand', $phone->brand ?? '') }}" placeholder="Apple, Samsung…">
    </div>
    <div class="col-md-6">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control"
               value="{{ old('model', $phone->model ?? '') }}" placeholder="iPhone 15, Galaxy S24…">
    </div>
    <div class="col-md-6">
        <label class="form-label">IMEI</label>
        <input type="text" name="imei" class="form-control @error('imei') is-invalid @enderror"
               value="{{ old('imei', $phone->imei ?? '') }}" placeholder="15-digit serial">
        @error('imei') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone number</label>
        <input type="text" name="phone_number" class="form-control"
               value="{{ old('phone_number', $phone->phone_number ?? '') }}" placeholder="+1 555 0100">
    </div>
    <div class="col-md-12">
        <label class="form-label">Owner name</label>
        <input type="text" name="owner_name" class="form-control"
               value="{{ old('owner_name', $phone->owner_name ?? '') }}" placeholder="Who carries this device">
    </div>
</div>
