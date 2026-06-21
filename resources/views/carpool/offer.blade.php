{{--
  resources/views/carpool/offer.blade.php
  Standalone offer creation page (deep-link from profile, vehicles page, etc.)
  Reuses layout, mirrors the "Offer a ride" panel from search.blade.php
--}}
@extends('layouts.app')

@section('title', 'Offer a ride — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <a href="{{ url('/carpool/search') }}" style="color:var(--color-text-secondary);text-decoration:none;font-size:.875rem;display:inline-flex;align-items:center;gap:var(--space-1);margin-bottom:var(--space-2)">
    <i class="fa-solid fa-arrow-left"></i> Back to Carpool
  </a>
  <h1 class="headline">Post a ride offer</h1>
  <p class="caption">Offer seats in your car to colleagues travelling the same way.</p>
</div>

<div class="sp-card mb-5">
  <div class="sp-card-body">
    {{-- TODO: POST /carpool/offers => CarpoolOfferController::store() --}}
    <form method="POST" action="{{ url('/carpool/search') }}" id="offer-ride-form" novalidate>
      @csrf

      <div class="mb-4">
        <label class="form-label">Days you're driving <span class="text-danger">*</span></label>
        <div class="day-chips">
          @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
            <button type="button" class="day-chip" data-day="{{ $day }}">{{ $day }}</button>
          @endforeach
        </div>
        <div class="caption mt-1">Select the days you'll be driving this week.</div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-sm-4">
          <label for="departure-time" class="form-label">Departure time <span class="text-danger">*</span></label>
          <input type="time" class="form-control" id="departure-time" name="departure_time" value="08:15" required>
          <div class="invalid-feedback">Please set a departure time.</div>
        </div>
        <div class="col-sm-4">
          <label for="offer-seats" class="form-label">Seats available <span class="text-danger">*</span></label>
          <div class="d-flex align-items-center gap-3">
            <input type="range" class="form-range flex-grow-1" id="offer-seats" name="seats_available" min="1" max="5" value="3">
            <span id="offer-seats-display" style="font-size:1.25rem;font-weight:400;color:var(--color-primary);min-width:24px;text-align:center"><span class="num">3</span></span>
          </div>
        </div>
        <div class="col-sm-4">
          <label for="offer-vehicle" class="form-label">Vehicle <span class="text-danger">*</span></label>
          {{-- TODO: Replace with Vehicle::where('user_id', auth()->id())->get() --}}
          <select class="form-select" id="offer-vehicle" name="vehicle_id" required>
            <option value="" disabled selected>Select vehicle</option>
            @foreach($vehicles ?? [] as $vehicle)
              <option value="{{ $vehicle['id'] }}">{{ $vehicle['make'] }} {{ $vehicle['model'] }} ({{ $vehicle['plate'] }})</option>
            @endforeach
          </select>
          <div class="invalid-feedback">Please select a vehicle.</div>
        </div>
      </div>

      <div class="mb-4">
        <label for="offer-route-note" class="form-label">Route note <span class="caption">(optional)</span></label>
        <input type="text" class="form-control" id="offer-route-note" name="route_note"
               placeholder="e.g. Passing through Sector 62, picking up near Metro Gate 3">
        <div class="caption mt-1">Tell passengers roughly where you'll pass through.</div>
      </div>

      <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <p class="caption mb-0">Your contact info is shared only with confirmed passengers.</p>
        <button type="submit" class="btn-accent-sp" id="btn-post-offer">
          Post ride offer
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/carpool.js') }}"></script>
@endsection
