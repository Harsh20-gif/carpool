{{--
  resources/views/carpool/trip-detail.blade.php
--}}
@extends('layouts.app')
@section('title', 'Trip details — Smart Park & Share')
@section('content')
<div class="page-header mt-2">
  <a href="{{ url('/carpool/search') }}" style="color:var(--color-text-secondary);text-decoration:none;font-size:.875rem;display:inline-flex;align-items:center;gap:var(--space-1);margin-bottom:var(--space-2)">
    <i class="fa-solid fa-arrow-left"></i> Back to Carpool
  </a>
  <h1 class="headline">Trip details</h1>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="sp-card mb-4">
      <div class="sp-card-body">
        <div class="d-flex align-items-start gap-3 mb-4">
          <div style="width:36px;height:36px;background:rgba(39,140,247,.15);color:var(--color-secondary);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div>
            <div style="font-size:.9375rem;font-weight:400;">{{ $trip['pickup_point'] ?? 'Your Pickup Point' }}</div>
            <div class="caption">Be there by <span class="num">{{ $trip['pickup_ready_time'] ?? '08:00' }}</span> · Driver will wait 3 minutes</div>
          </div>
        </div>
        <hr class="divider">
        <div class="d-flex align-items-start gap-3">
          <div style="width:36px;height:36px;background:rgba(39,174,96,.15);color:var(--color-success);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-flag-checkered"></i>
          </div>
          <div>
            <div style="font-size:.9375rem;font-weight:400;">HQ — Sector 18, Gurugram</div>
            <div class="caption">Estimated arrival <span class="num">{{ $trip['eta'] ?? '09:00' }}</span></div>
          </div>
        </div>
      </div>
    </div>

    {{-- Chat --}}
    <div class="sp-card">
      <div class="sp-card-header">
        <h2 class="section-label mb-0">Trip chat</h2>
        <span class="caption"><span class="num">{{ count($trip['passengers'] ?? []) + 1 }}</span> people in this trip</span>
      </div>
      <div class="chat-container">
        <div class="chat-messages" id="chat-messages">
          @foreach($trip['chat_messages'] ?? [] as $msg)
          <div class="chat-message {{ $msg['is_mine'] ? 'mine' : 'theirs' }}">
            @if(!$msg['is_mine'])
              <div style="font-size:.6875rem;font-weight:400;color:var(--color-text-secondary);padding:0 var(--space-2);margin-bottom:2px">
                {{ $msg['sender'] }}
              </div>
            @endif
            <div class="chat-bubble">{{ $msg['body'] }}</div>
            <div class="chat-time">{{ $msg['time'] }}</div>
          </div>
          @endforeach
        </div>
        <div class="chat-input-row">
          <input type="text" id="chat-input" placeholder="Type a message…" autocomplete="off" maxlength="500">
          <button class="chat-send-btn" id="chat-send" type="button">Send</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    {{-- Driver card --}}
    <div class="sp-card mb-3">
      <div class="sp-card-body">
        <div class="label-text mb-3">Driver</div>
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="driver-avatar" style="border-radius:var(--radius-round)">{{ strtoupper(substr($trip['driver']['name'] ?? 'NA', 0, 2)) }}</div>
          <div>
            <div style="font-size:.9375rem;font-weight:400;">{{ $trip['driver']['name'] ?? 'Driver Name' }}</div>
            <div class="caption">{{ $trip['driver']['department'] ?? 'Department' }}</div>
            <div class="star-rating-display mt-1" style="color:var(--color-warning)">
              @for($i = 1; $i <= 5; $i++)
                @if($i <= round($trip['driver']['rating'] ?? 5))
                  <i class="fa-solid fa-star"></i>
                @else
                  <i class="fa-regular fa-star"></i>
                @endif
              @endfor
              <span style="font-size:.75rem;color:var(--color-text-secondary);margin-left:4px"><span class="num">{{ number_format($trip['driver']['rating'] ?? 5, 1) }}</span></span>
            </div>
          </div>
        </div>
        <div style="font-size:.875rem">
          <div class="d-flex justify-content-between mb-2">
            <span class="caption">Vehicle</span>
            <span style="font-weight:400;">{{ $trip['driver']['vehicle'] ?? 'Unknown' }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="caption">Plate</span>
            <span style="font-weight:400;font-family:monospace">{{ $trip['driver']['plate'] ?? 'XX00XX0000' }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="caption">Colour</span>
            <span style="font-weight:400;">{{ $trip['driver']['vehicle_color'] ?? 'Unknown' }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Passengers --}}
    <div class="sp-card mb-3">
      <div class="sp-card-body">
        <div class="label-text mb-3">Passengers (<span class="num">{{ count($trip['passengers'] ?? []) }}</span>)</div>
        @foreach($trip['passengers'] ?? [] as $passenger)
        <div class="d-flex align-items-center gap-2 {{ !$loop->last ? 'mb-2' : '' }}">
          <div style="width:32px;height:32px;border-radius:var(--radius-round);background:var(--color-surface-alt);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:400;color:var(--color-primary);flex-shrink:0">
            {{ strtoupper(substr($passenger['name'], 0, 2)) }}
          </div>
          <div>
            <div style="font-size:.875rem;font-weight:400;">{{ $passenger['name'] }}</div>
            <div class="caption">{{ $passenger['department'] }}</div>
          </div>
          @if($passenger['is_me'])
            <span class="ms-auto caption" style="background:var(--color-surface-alt);padding:var(--space-1) var(--space-2);border-radius:var(--radius-sm);font-size:.6875rem;font-weight:400;color:var(--color-secondary)">You</span>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    {{-- Actions --}}
    <div class="sp-card">
      <div class="sp-card-body d-flex flex-column gap-2">
        @if(($trip['status'] ?? '') === 'confirmed')
          <form method="POST" action="{{ url('/carpool/search') }}">
            @csrf
            <button type="submit" class="btn-primary-sp w-100 justify-content-center" id="btn-complete-trip">
              Mark trip complete
            </button>
          </form>
        @endif
        <a href="{{ url('/ratings/create') }}" class="btn-outline-sp w-100 justify-content-center">
          Rate your trip
        </a>
        <form method="POST" action="{{ url('/carpool/search') }}" onsubmit="return confirm('Cancel your seat on this trip?')">
          @csrf
          <button type="submit" class="btn-outline-sp w-100 justify-content-center" style="border-color:var(--color-danger);color:var(--color-danger)">
            Cancel my seat
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
