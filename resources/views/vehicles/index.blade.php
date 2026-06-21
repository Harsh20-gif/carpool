@extends('layouts.app')
@section('title', 'My Vehicles — Smart Park & Share')
@section('content')
<div class="page-header mt-2">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
      <h1 class="headline">My Vehicles</h1>
      <p class="caption">Verified vehicles appear as options when you offer a ride.</p>
    </div>
    <a href="{{ url('/vehicles/create') }}" class="btn-accent-sp" id="btn-add-vehicle-top">
      <i class="fa-solid fa-plus"></i> Add vehicle
    </a>
  </div>
</div>

@if(empty($vehicles))
  <div class="empty-state">
    <div class="empty-state-title">No vehicles added yet</div>
    <div class="empty-state-text">Add your car to start offering rides to colleagues. We verify documents within 24 hours.</div>
    <a href="{{ url('/vehicles/create') }}" class="btn-accent-sp">Add your first vehicle</a>
  </div>
@else
  <div class="row g-4">
    @foreach($vehicles as $vehicle)
    <div class="col-sm-6 col-lg-4">
      <div class="vehicle-card">
        <div class="vehicle-img-placeholder">
          <i class="fa-solid fa-car fa-3x" style="opacity:0.2"></i>
        </div>
        <div class="sp-card-body">
          <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div>
              <div style="font-size:1rem;font-weight:400;color:var(--color-primary)">{{ $vehicle['make'] }} {{ $vehicle['model'] }}</div>
              <div class="caption">{{ $vehicle['year'] }} · {{ ucfirst($vehicle['type']) }}</div>
            </div>
            <span class="sp-badge {{ $vehicle['verified'] ? 'verified' : 'unverified' }}">
              {{ $vehicle['verified'] ? '✓ Verified' : 'Verification pending' }}
            </span>
          </div>
          <div class="row g-2 mt-1" style="font-size:.8125rem">
            <div class="col-6">
              <div class="caption">Plate</div>
              <div style="font-family:monospace;font-size:.9rem">{{ $vehicle['plate'] }}</div>
            </div>
            <div class="col-6">
              <div class="caption">Capacity</div>
              <div><span class="num">{{ $vehicle['seats'] }}</span> seats</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
    <div class="col-sm-6 col-lg-4">
      <a href="{{ url('/vehicles/create') }}" class="quick-action-card h-100">
        <div class="quick-action-icon">
          <i class="fa-solid fa-plus"></i>
        </div>
        <div class="quick-action-label mt-2">Add another vehicle</div>
        <div class="quick-action-desc">Cars, SUVs, vans — any vehicle you drive to HQ</div>
      </a>
    </div>
  </div>
@endif
@endsection
