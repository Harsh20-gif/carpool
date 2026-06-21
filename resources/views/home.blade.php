<!-- {{--
  resources/views/home.blade.php

  Mock data passed from route:
    $todayTrip  — array: status, driver, pickup_time, route, seats_remaining
    $parkingAvailability — array: total, available, status
--}}
@extends('layouts.app')

@section('title', 'Home — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <p class="caption mb-0">{{ now()->format('l, j F Y') }}</p>
  <h1 class="headline">Good morning, {{ $user['first_name'] ?? 'Harsh' }} 👋</h1>
</div>

<div class="row g-4">

  {{-- ── TODAY'S COMMUTE CARD ── --}}
  <div class="col-12">
    <div class="commute-card">
      <div class="commute-day">Today's commute</div>

      @if($todayTrip['status'] === 'confirmed')
        <div class="commute-route">{{ $todayTrip['route'] }}</div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;border-radius:var(--radius-md);background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:400;color:#fff">
              {{ strtoupper(substr($todayTrip['driver'], 0, 2)) }}
            </div>
            <span style="font-size:.875rem;opacity:.85">{{ $todayTrip['driver'] }}</span>
          </div>
          <div style="font-size:.875rem;opacity:.85">
            <i class="fa-solid fa-car-side"></i> {{ $todayTrip['seats_remaining'] }} seats remaining
          </div>
        </div>
      @else
        <div class="commute-route">No trip planned yet</div>
        <div class="caption mt-2" style="color:rgba(255,255,255,.7)">Offer a ride or find a seat to commute together.</div>
      @endif
    </div>
  </div>

  <div class="col-sm-6">
    <a href="{{ url('/carpool/search') }}" class="quick-action-card h-100">
      <div class="quick-action-icon">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <div class="quick-action-label">Find a ride</div>
      <div class="quick-action-desc">See who's driving near you tomorrow</div>
    </a>
  </div>

  <div class="col-sm-6">
    <a href="{{ url('/parking') }}" class="quick-action-card h-100">
      <div class="quick-action-icon">
        <i class="fa-solid fa-check"></i>
      </div>
      <div class="quick-action-label">Reserve parking</div>
      <div class="quick-action-desc">Book a guaranteed spot at HQ</div>
    </a>
  </div>

  {{-- ── HQ PARKING LIVE STATUS ── --}}
  <div class="col-12">
    <div class="sp-card">
      <div class="sp-card-header">
        <div>
          <h2 class="section-label mb-0">HQ Parking — Live availability</h2>
          <p class="caption mb-0">Updated every 5 minutes</p>
        </div>
        <a href="{{ url('/parking') }}" class="btn-outline-sp" style="font-size:.8125rem;padding:var(--space-2) var(--space-3)">
          Reserve a spot
        </a>
      </div>
      <div class="sp-card-body">
        <div class="row g-3">
          <div class="col-12 col-sm-4">
            <div style="padding:var(--space-3);background:var(--color-surface-alt);border-radius:var(--radius-md);border:1px solid var(--color-border-light)">
              <div class="label-text mb-2">Zone A — Main Entrance</div>
              {{-- TODO: Replace with real-time count from ParkingSpot::zoneAvailability('A') --}}
              <x-status-dot status="{{ $parking['zone_a']['status'] }}" label="spots available" :count="$parking['zone_a']['available']" />
            </div>
          </div>
          <div class="col-12 col-sm-4">
            <div style="padding:var(--space-3);background:var(--color-surface-alt);border-radius:var(--radius-md);border:1px solid var(--color-border-light)">
              <div class="label-text mb-2">Zone B — EV Charging</div>
              {{-- TODO: Replace with real-time count from ParkingSpot::zoneAvailability('B') --}}
              <x-status-dot status="{{ $parking['zone_b']['status'] }}" label="spots available" :count="$parking['zone_b']['available']" />
            </div>
          </div>
          <div class="col-12 col-sm-4">
            <div style="padding:var(--space-3);background:var(--color-surface-alt);border-radius:var(--radius-md);border:1px solid var(--color-border-light)">
              <div class="label-text mb-2">Zone C — Carpool Priority</div>
              {{-- TODO: Replace with real-time count from ParkingSpot::zoneAvailability('C') --}}
              <x-status-dot status="{{ $parking['zone_c']['status'] }}" label="spots available" :count="$parking['zone_c']['available']" />
            </div>
          </div>
        </div>

        {{-- Overall --}}
        <hr class="divider">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="label-text">Total available today</span>
            {{-- TODO: Replace with ParkingSpot::totalAvailable() --}}
            <div class="mt-1">
              <x-status-dot status="{{ $parkingStatus }}" label="of <span class=&quot;num&quot;>{{ $parking['total'] }}</span> spots available" :count="$parking['available']" />
            </div>
          </div>
          <a href="{{ url('/parking') }}" style="font-size:.8125rem;color:var(--color-primary);font-weight:400;text-decoration:none">
            View all zones →
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- ── RECENT ACTIVITY ── --}}
  <div class="col-12">
    <div class="sp-card">
      <div class="sp-card-header">
        <h2 class="section-label mb-0">Recent activity</h2>
      </div>
      <div class="sp-card-body p-0">
        {{-- TODO: Replace with Trip::recentForUser(auth()->id())->take(5)->get() --}}
        @forelse($recentActivity as $activity)
        <div style="padding:var(--space-4) var(--space-5);border-bottom:1px solid var(--color-border-light);display:flex;align-items:center;justify-content:space-between;gap:var(--space-4)">
          <div style="display:flex;align-items:center;gap:.75rem">
            <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:{{ $activity['type'] === 'carpool' ? 'var(--color-primary)' : 'var(--color-secondary)' }}"></div>
            <div>
              <div style="font-size:.9rem;font-weight:400;">{{ $activity['title'] }}</div>
              <div class="caption">{{ $activity['subtitle'] }}</div>
            </div>
          </div>
          <div class="caption text-end" style="white-space:nowrap">{{ $activity['date'] }}</div>
        </div>
        @empty
        <div class="empty-state">
          <div class="empty-state-title">No recent activity</div>
          <div class="empty-state-text">Your trips and parking reservations will appear here.</div>
        </div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection -->
