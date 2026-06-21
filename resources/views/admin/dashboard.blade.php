{{--
  resources/views/admin/dashboard.blade.php

  Mock data passed from route:
    $stats — weekly KPI stats
    $recentReservations — recent parking reservations table rows
    $carpoolTrend — weekly carpool trip counts (array, Mon-Sun, for Chart.js)
--}}
@extends('layouts.app')

@section('title', 'Admin Dashboard — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
      <span class="label-text" style="color:var(--color-secondary)">Admin</span>
      <h1 class="headline mt-1">Operations Dashboard</h1>
      <p class="caption">Week of {{ now()->startOfWeek()->format('j M') }} – {{ now()->endOfWeek()->format('j M Y') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="caption">Auto-refreshes every 5 min</span>
      <div class="status-dot available" style="flex-shrink:0"></div>
    </div>
  </div>
</div>

{{-- ── KPI STAT CARDS ── --}}
{{-- TODO: Replace with aggregated queries from Trip, ParkingReservation, Vehicle models --}}
<div class="row g-3 mb-5">

  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label-text mb-2">Carpool trips this week</div>
      <div class="stat-value"><span class="num">{{ $stats['trips_this_week'] }}</span></div>
      <div class="caption mt-1" style="color:var(--color-available)">↑ <span class="num">{{ $stats['trips_change'] }}</span>% vs last week</div>
    </div>
  </div>

  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label-text mb-2">Avg. occupancy per trip</div>
      <div class="stat-value"><span class="num">{{ number_format($stats['avg_occupancy'], 1) }}</span><span style="font-size:1rem;font-weight:400;color:var(--color-text-secondary)"> pax</span></div>
      <div class="caption mt-1">Target: 3.0+</div>
    </div>
  </div>

  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label-text mb-2">Parking utilisation</div>
      <div class="stat-value"><span class="num">{{ $stats['parking_utilisation'] }}</span><span style="font-size:1rem;font-weight:400;color:var(--color-text-secondary)">%</span></div>
      <div class="caption mt-1" style="color: {{ $stats['parking_utilisation'] > 85 ? 'var(--color-danger)' : 'var(--color-available)' }}">
        {{ $stats['parking_utilisation'] > 85 ? '⚠ High demand' : '✓ Healthy range' }}
      </div>
    </div>
  </div>

  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label-text mb-2">SOV reduction estimate</div>
      <div class="stat-value" style="color:var(--color-available)"><span class="num">{{ $stats['sov_reduction'] }}</span>%</div>
      <div class="caption mt-1">Single-occupancy vehicles vs baseline</div>
    </div>
  </div>

</div>

<div class="row g-4 mb-5">

  {{-- ── CARPOOL TREND CHART ── --}}
  <div class="col-lg-7">
    <div class="sp-card h-100">
      <div class="sp-card-header">
        <h2 class="section-label mb-0">Weekly carpool trips</h2>
        <span class="caption">Last 7 days</span>
      </div>
      <div class="sp-card-body">
        <div class="chart-container">
          <canvas id="carpool-trend-chart"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- ── PARKING ZONE SUMMARY ── --}}
  <div class="col-lg-5">
    <div class="sp-card h-100">
      <div class="sp-card-header">
        <h2 class="section-label mb-0">Parking by zone today</h2>
      </div>
      <div class="sp-card-body">
        {{-- TODO: Replace with ParkingSpot::groupBy('zone')->selectRaw('zone, count(*) as total, sum(is_available) as available')->get() --}}
        @foreach($zoneBreakdown as $zone)
        <div class="{{ !$loop->last ? 'mb-4' : '' }}">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <div style="font-size:.9rem;font-weight:400;">{{ $zone['name'] }}</div>
              <div class="caption">{{ $zone['sublabel'] }}</div>
            </div>
            <x-status-dot status="{{ $zone['status'] }}" label="free" :count="$zone['available']" />
          </div>
          {{-- Progress bar --}}
          <div style="height:6px;background:var(--color-border-light);border-radius:3px;overflow:hidden">
            <div style="height:100%;width:{{ round(($zone['total'] - $zone['available']) / $zone['total'] * 100) }}%;background:{{ $zone['status'] === 'available' ? 'var(--color-available)' : ($zone['status'] === 'low' ? 'var(--color-warning)' : 'var(--color-danger)') }};border-radius:3px;transition:width .3s ease"></div>
          </div>
          <div class="caption mt-1">{{ $zone['total'] - $zone['available'] }} of {{ $zone['total'] }} occupied</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>

{{-- ── RECENT PARKING RESERVATIONS ── --}}
<div class="sp-card">
  <div class="sp-card-header">
    <h2 class="section-label mb-0">Recent parking reservations</h2>
    <span class="caption">Latest 20 reservations</span>
  </div>
  <div style="overflow-x:auto">
    <table class="sp-table">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Employee</th>
          <th>Department</th>
          <th>Zone</th>
          <th>Date</th>
          <th>Time window</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        {{-- TODO: Replace with ParkingReservation::with('user')->latest()->take(20)->get() --}}
        @foreach($recentReservations as $r)
        <tr>
          <td><span style="font-family:monospace;font-size:.8125rem">{{ $r['ref'] }}</span></td>
          <td>
            <div style="display:flex;align-items:center;gap:.5rem">
              <div style="width:26px;height:26px;border-radius:var(--radius-sm);background:var(--color-primary-dim);color:var(--color-secondary);font-size:.6875rem;font-weight:400;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                {{ strtoupper(substr($r['employee_name'], 0, 2)) }}
              </div>
              <span style="font-size:.875rem;font-weight:400;">{{ $r['employee_name'] }}</span>
            </div>
          </td>
          <td><span class="caption">{{ $r['department'] }}</span></td>
          <td><span style="font-size:.875rem;font-weight:400;">{{ $r['zone'] }}</span></td>
          <td><span class="caption">{{ $r['date'] }}</span></td>
          <td><span class="caption">{{ $r['time_window'] }}</span></td>
          <td>
            <span class="sp-badge {{ $r['status'] }}">{{ ucfirst(str_replace('_', ' ', $r['status'])) }}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="sp-card-footer">
    <span class="caption">Showing {{ count($recentReservations) }} most recent reservations.</span>
  </div>
</div>

@endsection

@section('head')
{{-- Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endsection

@section('scripts')
<script>
  // ── CARPOOL TREND CHART ──────────────────────────────────────
  // Data passed from the route as a PHP array → JSON
  // TODO: Replace with real data from Trip::groupBy('date')->thisWeek()->count()
  const carpoolTrendData = @json($carpoolTrend);

  const ctx = document.getElementById('carpool-trend-chart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: carpoolTrendData.labels,
      datasets: [{
        label: 'Carpool trips',
        data: carpoolTrendData.values,
        backgroundColor: 'rgba(21, 38, 59, 0.85)',
        borderRadius: 4,
        borderSkipped: false,
      }, {
        label: 'Passengers',
        data: carpoolTrendData.passengers,
        backgroundColor: 'rgba(39, 140, 247, 0.7)',
        borderRadius: 4,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: { family: 'Helvetica, Arial, sans-serif', size: 12 },
            color: '#5B6B7F',
            boxWidth: 12,
            padding: 16,
          }
        },
        tooltip: {
          backgroundColor: '#15263B',
          titleFont: { family: 'Helvetica, Arial, sans-serif', size: 13, weight: '400' },
          bodyFont:  { family: 'Helvetica, Arial, sans-serif', size: 12 },
          padding: 12,
          cornerRadius: 6,
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { family: 'Helvetica, Arial, sans-serif', size: 12 }, color: '#5B6B7F' }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,.04)' },
          ticks: { font: { family: 'Helvetica, Arial, sans-serif', size: 12 }, color: '#5B6B7F', precision: 0 },
          border: { display: false },
        }
      }
    }
  });
</script>
@endsection
