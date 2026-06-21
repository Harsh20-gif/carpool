{{--
  resources/views/profile/show.blade.php

  Mock data passed from route:
    $user — user profile data
    $stats — trips, CO2 saved, money saved
--}}
@extends('layouts.app')

@section('title', 'My Profile — Smart Park & Share')

@section('content')
<div class="mt-2">

  {{-- ── PROFILE HERO ── --}}
  <div class="profile-hero mb-5" style="position:relative">
    <div style="font-size:.8125rem;opacity:.65;margin-bottom:var(--space-1)">{{ $user['department'] }}</div>
    <div style="font-size:1.5rem;font-weight:400;margin-bottom:var(--space-1)">{{ $user['full_name'] }}</div>
    <div style="font-size:.875rem;opacity:.7">{{ $user['email'] }}</div>
    <div class="mt-3 d-flex align-items-center gap-2">
      <div class="profile-stars" style="color:var(--color-warning)">
        @for($i = 1; $i <= 5; $i++)
          @if($i <= round($user['avg_rating']))
            <i class="fa-solid fa-star"></i>
          @else
            <i class="fa-regular fa-star"></i>
          @endif
        @endfor
      </div>
      <span style="font-size:.875rem;opacity:.8"><span class="num">{{ number_format($user['avg_rating'], 1) }}</span> avg · <span class="num">{{ $user['total_ratings'] }}</span> ratings</span>
    </div>
    {{-- Avatar positioned at bottom of card --}}
    <div class="profile-avatar" style="border-radius:var(--radius-round)">
      {{ strtoupper(substr($user['full_name'], 0, 2)) }}
    </div>
  </div>

  {{-- ── STAT CARDS ── --}}
  <div class="row g-3 mb-5">
    <div class="col-sm-4">
      <div class="stat-card" style="border-radius:var(--radius-md)">
        <div class="stat-value"><span class="num">{{ $stats['trips_taken'] }}</span></div>
        <div class="stat-label">Carpool trips taken</div>
        <div class="caption" style="margin-top:var(--space-1)">Since joining Smart Park &amp; Share</div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="stat-card" style="border-radius:var(--radius-md)">
        <div class="stat-value" style="color:var(--color-success)"><span class="num">{{ $stats['co2_saved'] }}</span> kg</div>
        <div class="stat-label">CO₂ saved (estimated)</div>
        <div class="caption" style="margin-top:var(--space-1)">vs. driving alone each day</div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="stat-card" style="border-radius:var(--radius-md)">
        <div class="stat-value" style="color:var(--color-secondary)">₹<span class="num">{{ number_format($stats['money_saved']) }}</span></div>
        <div class="stat-label">Fuel cost saved (est.)</div>
        <div class="caption" style="margin-top:var(--space-1)">Shared fuel &amp; parking costs</div>
      </div>
    </div>
  </div>

  {{-- ── PROFILE LINKS ── --}}
  <div class="row g-4">

    <div class="col-lg-6">
      <div class="sp-card" style="border-radius:var(--radius-md)">
        <div class="sp-card-header">
          <h2 class="section-label mb-0" style="font-weight:800">Account</h2>
        </div>
        <div class="sp-card-body p-0">
          @foreach([
            ['icon' => 'fa-solid fa-car', 'label' => 'My Vehicles', 'desc' => '<span class="num">'.count($user["vehicles"]).'</span> vehicle(s) registered', 'href' => '/vehicles'],
            ['icon' => 'fa-solid fa-gear', 'label' => 'Settings', 'desc' => 'Profile, home location, commute days', 'href' => '/onboarding/wizard'],
            ['icon' => 'fa-solid fa-bell', 'label' => 'Notification preferences', 'desc' => 'Email and push alerts for trips & parking', 'href' => '/profile'],
          ] as $link)
          <a href="{{ url($link['href']) }}" class="d-flex align-items-center gap-3 text-decoration-none"
             style="padding:var(--space-3) var(--space-4);border-bottom:1px solid var(--color-disabled);transition:background-color var(--transition-fast);"
             onmouseenter="this.style.backgroundColor='var(--color-surface-alt)'"
             onmouseleave="this.style.backgroundColor=''">
            <div style="width:36px;height:36px;border-radius:var(--radius-md);background:rgba(127,140,32,.06);display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i class="{{ $link['icon'] }}" style="color:var(--color-primary);font-size:1.1rem"></i>
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:.9375rem;font-weight:400;color:var(--color-text-primary)">{{ $link['label'] }}</div>
              <div class="caption">{!! $link['desc'] !!}</div>
            </div>
            <i class="fa-solid fa-chevron-right" style="color:var(--color-disabled)"></i>
          </a>
          @endforeach

          {{-- Log out --}}
          <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="d-flex align-items-center gap-3 text-decoration-none w-100 bg-transparent border-0 text-start"
                    style="padding:var(--space-3) var(--space-4);cursor:pointer;transition:background-color var(--transition-fast);"
                    onmouseenter="this.style.backgroundColor='rgba(233,75,75,.05)'"
                    onmouseleave="this.style.backgroundColor=''">
              <div style="width:36px;height:36px;border-radius:var(--radius-md);background:rgba(233,75,75,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fa-solid fa-right-from-bracket" style="color:var(--color-danger);font-size:1.1rem"></i>
              </div>
              <span style="font-size:.9375rem;font-weight:400;color:var(--color-danger)">Log out</span>
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      {{-- Recent trips --}}
      <div class="sp-card mb-4" style="border-radius:var(--radius-md)">
        <div class="sp-card-header">
          <h2 class="section-label mb-0" style="font-weight:800">Recent trips</h2>
          <a href="{{ url('/carpool/search') }}" style="font-size:.8125rem;color:var(--color-secondary);font-weight:400;text-decoration:none">View all</a>
        </div>
        <div class="sp-card-body p-0">
          {{-- TODO: Replace with Trip::forUser(auth()->id())->recent()->take(4)->get() --}}
          @foreach($user['recent_trips'] as $trip)
          <div style="padding:var(--space-3) var(--space-4);border-bottom:1px solid var(--color-disabled);display:flex;align-items:center;justify-content:space-between;gap:var(--space-4)">
            <div>
              <div style="font-size:.9rem;font-weight:400;">{{ $trip['route'] }}</div>
              <div class="caption"><span class="num">{{ $trip['date'] }}</span> · {{ $trip['role'] }}</div>
            </div>
            <div class="star-rating-display" style="font-size:.7rem;color:var(--color-warning)">
              @if($trip['rating'])
                @for($i = 1; $i <= 5; $i++)
                  @if($i <= $trip['rating'])
                    <i class="fa-solid fa-star"></i>
                  @else
                    <i class="fa-regular fa-star"></i>
                  @endif
                @endfor
              @else
                <a href="{{ url('/ratings/create') }}" style="font-size:.75rem;color:var(--color-secondary);font-weight:400;text-decoration:none">Rate</a>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Parking history summary --}}
      <div class="sp-card" style="border-radius:var(--radius-md)">
        <div class="sp-card-body">
          <div class="label-text mb-3" style="font-weight:400">Parking this month</div>
          <div class="d-flex gap-4">
            <div>
              <div style="font-size:1.5rem;font-weight:400;color:var(--color-primary)"><span class="num">{{ $stats['parking_reservations'] }}</span></div>
              <div class="caption">Reservations</div>
            </div>
            <div>
              <div style="font-size:1.5rem;font-weight:400;color:var(--color-success)"><span class="num">{{ $stats['parking_checkins'] }}</span></div>
              <div class="caption">Check-ins</div>
            </div>
          </div>
          <a href="{{ url('/parking') }}" class="btn-outline-sp mt-3 d-inline-flex" style="font-size:.8125rem;border-radius:var(--radius-md)">
            Reserve a spot
          </a>
        </div>
      </div>
    </div>

  </div>

</div>
@endsection
