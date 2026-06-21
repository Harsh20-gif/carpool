{{--
  resources/views/vehicles/create.blade.php
  Add / edit vehicle form.
  Uses old() helper on all inputs for re-population after validation failure.
--}}
@extends('layouts.app')

@section('title', 'Add a vehicle — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <a href="{{ url('/vehicles') }}" style="color:var(--color-text-secondary);text-decoration:none;font-size:.875rem;display:inline-flex;align-items:center;gap:var(--space-1);margin-bottom:var(--space-2)">
    <i class="fa-solid fa-chevron-left"></i>
    Back to My Vehicles
  </a>
  <h1 class="headline">Add a vehicle</h1>
  <p class="caption">We verify your documents within 24 hours. You can offer rides straight away — verified status is shown to passengers.</p>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="sp-card">
      <div class="sp-card-header">
        <h2 class="section-label mb-0">Vehicle information</h2>
      </div>
      <div class="sp-card-body">

        {{-- TODO: POST /vehicles => VehicleController::store() --}}
        <form method="POST" action="{{ url('/vehicles') }}" enctype="multipart/form-data"
              class="sp-validated-form" novalidate id="add-vehicle-form">
          @csrf

          {{-- Plate number --}}
          <div class="mb-3">
            <label for="plate" class="form-label">Number plate <span class="text-danger">*</span></label>
            <input type="text" class="form-control {{ $errors->has('plate') ? 'is-invalid' : '' }}"
                   id="plate" name="plate"
                   placeholder="e.g. DL 01 AB 1234"
                   value="{{ old('plate') }}"
                   required
                   style="font-family:monospace;text-transform:uppercase;letter-spacing:.05em">
            <div class="invalid-feedback">
              {{ $errors->first('plate') ?: 'Please enter the vehicle number plate.' }}
            </div>
          </div>

          <div class="row g-3 mb-3">
            {{-- Make --}}
            <div class="col-sm-4">
              <label for="make" class="form-label">Make <span class="text-danger">*</span></label>
              <input type="text" class="form-control {{ $errors->has('make') ? 'is-invalid' : '' }}"
                     id="make" name="make"
                     placeholder="e.g. Maruti"
                     value="{{ old('make') }}" required>
              <div class="invalid-feedback">{{ $errors->first('make') ?: 'Required.' }}</div>
            </div>

            {{-- Model --}}
            <div class="col-sm-4">
              <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
              <input type="text" class="form-control {{ $errors->has('model') ? 'is-invalid' : '' }}"
                     id="model" name="model"
                     placeholder="e.g. Swift Dzire"
                     value="{{ old('model') }}" required>
              <div class="invalid-feedback">{{ $errors->first('model') ?: 'Required.' }}</div>
            </div>

            {{-- Year --}}
            <div class="col-sm-4">
              <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
              <input type="number" class="form-control {{ $errors->has('year') ? 'is-invalid' : '' }}"
                     id="year" name="year"
                     placeholder="{{ date('Y') }}"
                     min="2000" max="{{ date('Y') }}"
                     value="{{ old('year') }}" required>
              <div class="invalid-feedback">{{ $errors->first('year') ?: 'Enter a valid year.' }}</div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            {{-- Type --}}
            <div class="col-sm-4">
              <label for="type" class="form-label">Vehicle type <span class="text-danger">*</span></label>
              <select class="form-select {{ $errors->has('type') ? 'is-invalid' : '' }}"
                      id="type" name="type" required>
                <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select type</option>
                @foreach(['Hatchback','Sedan','SUV','MPV','Van','Pickup','Other'] as $t)
                  <option value="{{ strtolower($t) }}" {{ old('type') === strtolower($t) ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback">{{ $errors->first('type') ?: 'Required.' }}</div>
            </div>

            {{-- Seat capacity --}}
            <div class="col-sm-4">
              <label for="seats" class="form-label">Seat capacity (incl. driver) <span class="text-danger">*</span></label>
              <select class="form-select {{ $errors->has('seats') ? 'is-invalid' : '' }}"
                      id="seats" name="seats" required>
                <option value="" disabled {{ old('seats') ? '' : 'selected' }}>Seats</option>
                @for($i = 2; $i <= 9; $i++)
                  <option value="{{ $i }}" {{ old('seats', 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
              </select>
              <div class="invalid-feedback">{{ $errors->first('seats') ?: 'Required.' }}</div>
            </div>

            {{-- Fuel type --}}
            <div class="col-sm-4">
              <label for="fuel_type" class="form-label">Fuel type</label>
              <select class="form-select" id="fuel_type" name="fuel_type">
                @foreach(['Petrol','Diesel','CNG','Electric','Hybrid','LPG'] as $f)
                  <option value="{{ strtolower($f) }}" {{ old('fuel_type') === strtolower($f) ? 'selected' : '' }}>{{ $f }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-sm-6">
              <label for="color" class="form-label">Colour</label>
              <input type="text" class="form-control" id="color" name="color"
                     placeholder="e.g. Silver"
                     value="{{ old('color') }}">
            </div>
          </div>

          {{-- Feature checkboxes --}}
          <div class="mb-4">
            <label class="form-label">Features</label>
            <div class="row g-2">
              @foreach([
                ['name' => 'features[]', 'value' => 'ac',        'label' => 'Air conditioning'],
                ['name' => 'features[]', 'value' => 'music',     'label' => 'Music system'],
                ['name' => 'features[]', 'value' => 'usb',       'label' => 'USB charging'],
                ['name' => 'features[]', 'value' => 'luggage',   'label' => 'Boot space (luggage)'],
                ['name' => 'features[]', 'value' => 'child_seat','label' => 'Child seat available'],
                ['name' => 'features[]', 'value' => 'pets',      'label' => 'Pet friendly'],
              ] as $feat)
              <div class="col-6 col-sm-4">
                <label class="feature-checkbox">
                  <input type="checkbox"
                         name="{{ $feat['name'] }}"
                         value="{{ $feat['value'] }}"
                         {{ in_array($feat['value'], old('features', [])) ? 'checked' : '' }}>
                  <span>{{ $feat['label'] }}</span>
                </label>
              </div>
              @endforeach
            </div>
          </div>

          <hr class="divider">

          {{-- Document upload --}}
          <div class="mb-4">
            <label class="form-label">Verification documents <span class="text-danger">*</span></label>
            <p class="caption mb-3">Upload a clear photo of your RC book and insurance certificate. PDFs accepted. Max 10 MB each.</p>
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="rc_document" class="form-label">RC Book / Registration certificate</label>
                <input type="file" class="form-control" id="rc_document" name="rc_document"
                       accept=".pdf,.jpg,.jpeg,.png" required>
                <div class="invalid-feedback">Please upload your RC document.</div>
              </div>
              <div class="col-sm-6">
                <label for="insurance_document" class="form-label">Insurance certificate</label>
                <input type="file" class="form-control" id="insurance_document" name="insurance_document"
                       accept=".pdf,.jpg,.jpeg,.png">
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <p class="caption mb-0" style="max-width:400px">
              Documents are reviewed by our admin team. You'll get an email once verified. Your vehicle will be listed as "Verification pending" until then.
            </p>
            <div class="d-flex gap-2">
              <a href="{{ url('/vehicles') }}" class="btn-outline-sp">Cancel</a>
              <button type="submit" class="btn-accent-sp" id="btn-submit-vehicle">
                Add vehicle
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- Sidebar tip --}}
  <div class="col-lg-4">
    <div class="sp-card">
      <div class="sp-card-body">
        <div class="label-text mb-3">Document requirements</div>
        <ul style="padding-left:1.25rem;margin:0;display:flex;flex-direction:column;gap:.75rem;font-size:.875rem">
          <li>RC Book must show your name as registered owner or co-owner</li>
          <li>Insurance must be valid and not expired</li>
          <li>Images must be legible — blurry photos will be rejected</li>
          <li>Verification usually completes within 24 business hours</li>
        </ul>
        <hr class="divider">
        <div class="caption">Questions? Contact <a href="mailto:fleet@company.com" style="color:var(--color-primary)">fleet@company.com</a></div>
      </div>
    </div>
  </div>
</div>
@endsection
