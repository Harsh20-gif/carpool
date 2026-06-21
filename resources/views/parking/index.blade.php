{{--
  resources/views/parking/index.blade.php

  Mock data passed from route:
    $zones — array of zone objects (name, sublabel, available, total, status)
    $reservations — array of existing reservation objects
--}}
@extends('layouts.app')

@section('title', 'Reserve Parking — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
      <h1 class="headline">HQ Parking</h1>
      <p class="caption">Reserve a guaranteed parking spot before you leave home.</p>
    </div>
    {{-- Overall live count --}}
    <div class="sp-card" style="padding:var(--space-3) 1rem">
      <div class="label-text mb-1">Total available now</div>
      {{-- TODO: Replace with ParkingSpot::totalAvailable() --}}
      <x-status-dot status="{{ $overallStatus }}" label="of {{ $totalSpots }} spots free" :count="$availableSpots" />
    </div>
  </div>
</div>

{{-- ── ZONE CARDS ── --}}
<div class="row g-4 mb-5">
  {{-- TODO: Replace $zones with ParkingZone::with('availableSpots')->get() --}}
  @foreach($zones as $zone)
  <div class="col-md-4">
    <div class="zone-card" id="zone-card-{{ $zone['id'] }}">
      <div class="zone-header">
        <div>
          <div class="zone-label">{{ $zone['name'] }}</div>
          <div class="zone-sublabel">{{ $zone['sublabel'] }}</div>
        </div>
        {{-- Per-zone status dot --}}
        <x-status-dot
          status="{{ $zone['status'] }}"
          label=""
          :count="$zone['available']"
        />
      </div>

      <div class="zone-body">
        {{-- Large count display --}}
        <div class="zone-count-display">
          <div class="zone-count-num" id="zone-count-{{ $zone['id'] }}"><span class="num">{{ $zone['available'] }}</span></div>
          <div class="zone-count-total">of <span class="num">{{ $zone['total'] }}</span> spots available</div>
        </div>

        {{-- Reserve controls --}}
        <div class="mb-3">
          <label class="form-label">Date</label>
          <input type="date"
                 class="form-control zone-date-input"
                 id="zone-date-{{ $zone['id'] }}"
                 name="date"
                 value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                 min="{{ date('Y-m-d') }}"
                 max="{{ date('Y-m-d', strtotime('+30 days')) }}">
        </div>
        <div class="mb-4">
          <label class="form-label">Arrival window</label>
          <select class="form-select zone-time-input" id="zone-time-{{ $zone['id'] }}" name="time_window">
            <option value="08:00">08:00 – 10:00 (Morning)</option>
            <option value="10:00">10:00 – 12:00 (Late morning)</option>
            <option value="12:00">12:00 – 14:00 (Midday)</option>
            <option value="14:00">14:00 – 16:00 (Afternoon)</option>
          </select>
        </div>

        @if($zone['available'] > 0)
          <button
            type="button"
            class="btn-accent-sp w-100 justify-content-center btn-reserve-zone"
            data-zone-id="{{ $zone['id'] }}"
            id="btn-reserve-{{ $zone['id'] }}"
          >
            Reserve a spot in {{ $zone['name'] }}
          </button>
        @else
          <button type="button" class="btn-outline-sp w-100 justify-content-center" disabled>
            Zone full — no spots available
          </button>
          <div class="caption text-center mt-2">Check back later or choose another zone.</div>
        @endif

        @if($zone['notes'])
          <div class="caption mt-2 text-center">{{ $zone['notes'] }}</div>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ── MY RESERVATIONS ── --}}
<div id="my-reservations">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="section-label mb-0">My Reservations</h2>
    <span class="caption">Past 30 days</span>
  </div>

  <div class="sp-card">
    @if(count($reservations) === 0)
      <div class="empty-state">
        <i class="fa-solid fa-square-parking"></i>
        <div class="empty-state-title">No reservations yet</div>
        <div class="empty-state-text">Reserve a parking spot above and it will appear here.</div>
      </div>
    @else
    <div style="overflow-x:auto">
      <table class="sp-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Zone</th>
            <th>Date</th>
            <th>Time window</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="reservations-tbody">
          {{-- TODO: Replace with ParkingReservation::where('user_id', auth()->id())->recent()->get() --}}
          @foreach($reservations as $r)
          <tr>
            <td><span style="font-family:monospace"><span class="num">{{ $r['ref'] }}</span></span></td>
            <td>{{ $r['zone'] }}</td>
            <td>{{ $r['date'] }}</td>
            <td>{{ $r['time_window'] }}</td>
            <td>
              <span class="sp-badge {{ $r['status'] }}">
                {{ ucfirst(str_replace('_', ' ', $r['status'])) }}
              </span>
            </td>
            <td>
              @if($r['status'] === 'reserved')
                <button type="button" class="btn-accent-sp btn-sm py-1 px-3 btn-checkin" style="font-size:.8125rem">
                  Check in
                </button>
              @elseif($r['status'] === 'checkedin')
                <span class="caption">Active</span>
              @else
                <span class="caption">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/parking.js') }}"></script>
@endsection
