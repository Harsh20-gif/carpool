{{--
  resources/views/carpool/search.blade.php

  Mock data passed from route:
    $offers — array of ride offer objects
    $vehicles — array of user vehicles for the offer form
--}}
@extends('layouts.app')

@section('title', 'Carpool — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <h1 class="headline">Carpool</h1>
  <p class="caption">Find a colleague to ride with, or offer seats in your car.</p>
</div>

{{-- ── TABS ── --}}
<div class="sp-tabs">
  <button class="sp-tab" data-panel="panel-find" id="tab-find">Find a ride</button>
  <button class="sp-tab" data-panel="panel-offer" id="tab-offer">Offer a ride</button>
</div>

{{-- ══════════════════════════════════════════
     PANEL: FIND A RIDE
══════════════════════════════════════════ --}}
<div class="carpool-panel" id="panel-find">

  {{-- Filter bar --}}
  <div class="sp-card mb-4">
    <div class="sp-card-body">
      <div class="row g-3 align-items-end">
        <div class="col-sm-3">
          <label class="form-label">Travel date</label>
          <input type="date" class="form-control" id="filter-date" name="date"
                 value="{{ date('Y-m-d', strtotime('+1 day')) }}">
        </div>
        <div class="col-sm-3">
          <label class="form-label">Departure window</label>
          <select class="form-select" id="filter-time">
            <option value="">Any time</option>
            <option selected>07:00 – 09:00</option>
            <option>09:00 – 11:00</option>
            <option>17:00 – 20:00</option>
          </select>
        </div>
        <div class="col-sm-2">
          <label class="form-label">Seats needed</label>
          <select class="form-select" id="filter-seats">
            <option value="1" selected>1 seat</option>
            <option value="2">2 seats</option>
            <option value="3">3 seats</option>
          </select>
        </div>
        <div class="col-sm-4">
          <button class="btn-primary-sp w-100 justify-content-center" type="button" id="btn-search-rides">
            <i class="fa-solid fa-check"></i>
            Search rides
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Results --}}
  <div id="ride-results">
    @if(count($offers) === 0)
      <div class="empty-state">
        <i class="fa-solid fa-check"></i>
        <div class="empty-state-title">No rides posted for tomorrow yet</div>
        <div class="empty-state-text">Be the first to offer one — colleagues near you will thank you.</div>
        <button class="btn-accent-sp" onclick="document.getElementById('tab-offer').click()">Offer a ride</button>
      </div>
    @else
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="label-text"><span class="num">{{ count($offers) }}</span> rides available</span>
        <span class="caption">Sorted by departure time</span>
      </div>
      <div class="d-flex flex-column gap-3">
        {{-- TODO: Replace with CarpoolOffer::availableForDate($date)->with('driver','vehicle')->get() --}}
        @foreach($offers as $offer)
        <div class="ride-card">
          <div class="d-flex align-items-start gap-3">
            {{-- Driver avatar --}}
            <div class="driver-avatar flex-shrink-0">
              {{ strtoupper(substr($offer['driver_name'], 0, 2)) }}
            </div>

            <div class="flex-grow-1 min-width-0">
              <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                <div>
                  <div class="driver-name" style="font-size:.9375rem;font-weight:400;">{{ $offer['driver_name'] }}</div>
                  <div class="caption">{{ $offer['driver_department'] }}</div>
                </div>
                <div class="text-end">
                  {{-- Star rating display --}}
                  <div class="star-rating-display mb-1">
                    @for($i = 1; $i <= 5; $i++)
                      @if($i <= floor($offer['rating']))
                        <i class="fa-solid fa-check"></i>
                      @else
                        <i class="fa-solid fa-check"></i>
                      @endif
                    @endfor
                    <span class="ms-1" style="font-size:.75rem;color:var(--color-text-secondary)"><span class="num">{{ number_format($offer['rating'], 1) }}</span></span>
                  </div>
                  <div class="caption"><span class="num">{{ $offer['trips_completed'] }}</span> trips</div>
                </div>
              </div>

              <hr class="divider my-3">

              <div class="row g-2">
                <div class="col-sm-6">
                  <div class="caption mb-1">Route</div>
                  <div style="font-size:.875rem;font-weight:400;">{{ $offer['route'] }}</div>
                </div>
                <div class="col-sm-3">
                  <div class="caption mb-1">Departure</div>
                  <div style="font-size:.875rem;font-weight:400;color:var(--color-primary)">{{ $offer['departure_time'] }}</div>
                </div>
                <div class="col-sm-3">
                  <div class="caption mb-1">Vehicle</div>
                  <div style="font-size:.875rem">
                    <i class="fa-solid fa-check"></i>
                    {{ $offer['vehicle'] }}
                  </div>
                  <div class="caption">
                    <i class="fa-solid fa-check"></i>
                    {{ $offer['seats_available'] }} seats left
                  </div>
                </div>
              </div>

              <div class="mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                  <x-status-dot
                    status="{{ $offer['seats_available'] > 2 ? 'available' : ($offer['seats_available'] > 0 ? 'low' : 'full') }}"
                    label="{{ $offer['seats_available'] > 0 ? 'seats available' : 'full' }}"
                    :count="$offer['seats_available']"
                  />
                </div>
                <button
                  class="btn-primary-sp btn-request-seat"
                  data-offer-id="{{ $offer['id'] }}"
                  {{ $offer['seats_available'] === 0 ? 'disabled' : '' }}
                >
                  {{ $offer['seats_available'] === 0 ? 'No seats left' : 'Request seat' }}
                </button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- ══════════════════════════════════════════
     PANEL: OFFER A RIDE
══════════════════════════════════════════ --}}
<div class="carpool-panel d-none" id="panel-offer">

  <div class="sp-card">
    <div class="sp-card-header">
      <h2 class="section-label mb-0">Post a ride offer</h2>
    </div>
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
              @foreach($vehicles as $vehicle)
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

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/carpool.js') }}"></script>
@endsection
