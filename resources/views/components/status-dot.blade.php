{{--
  Status Dot Component
  resources/views/components/status-dot.blade.php

  Props:
    $status  — 'available' | 'low' | 'full'
    $label   — text label (e.g. "Spots available")
    $count   — numeric value shown in bold

  Usage:
    <x-status-dot status="available" label="spots available" :count="12" />
--}}
@props(['status' => 'available', 'label' => 'Available', 'count' => 0])

<span class="status-indicator">
  <span class="status-dot {{ $status }}" aria-hidden="true"></span>
  <span class="status-label">
    <span class="status-count num">{{ $count }}</span> {!! $label !!}
  </span>
</span>
