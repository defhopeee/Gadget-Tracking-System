@php $phone = $phone ?? null; @endphp
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Label <span class="text-danger">*</span></label>
        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
               value="{{ old('label', $phone->label ?? '') }}" placeholder="e.g. My Phone" required>
        @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Device type</label>
        <select name="type" class="form-select">
            @foreach (\App\Models\Phone::TYPES as $key => [$tlabel, $ticon])
                <option value="{{ $key }}" @selected(old('type', $phone->type ?? 'phone') === $key)>{{ $tlabel }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach (['active','inactive','lost','stolen','recovered'] as $s)
                <option value="{{ $s }}" @selected(old('status', $phone->status ?? 'active') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Owner name</label>
        <input type="text" name="owner_name" class="form-control"
               value="{{ old('owner_name', $phone->owner_name ?? '') }}" placeholder="Who carries it">
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone number</label>
        <input type="text" name="phone_number" class="form-control"
               value="{{ old('phone_number', $phone->phone_number ?? '') }}" placeholder="+254 7xx xxx xxx">
    </div>
    <div class="col-12">
        <label class="form-label">IMEI</label>
        <input type="text" name="imei" class="form-control @error('imei') is-invalid @enderror"
               value="{{ old('imei', $phone->imei ?? '') }}" placeholder="Dial *#06# on the phone">
        @error('imei') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
