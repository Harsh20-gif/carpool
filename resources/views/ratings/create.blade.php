{{--
  resources/views/ratings/create.blade.php

  Mock data passed from route:
    $trip — the trip being rated
    $ratee — the person being rated (driver or passenger)
--}}
@extends('layouts.app')

@section('title', 'Rate your trip — Smart Park & Share')

@section('content')
<div class="page-header mt-2">
  <a href="{{ url('/') }}" style="color:var(--color-text-secondary);text-decoration:none;font-size:.875rem;display:inline-flex;align-items:center;gap:var(--space-1);margin-bottom:var(--space-2)">
    <i class="fa-solid fa-chevron-left"></i>
    Back to home
  </a>
  <h1 class="headline">Rate your trip</h1>
  <p class="caption">Your feedback helps colleagues make better carpool decisions.</p>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7 col-md-9">

    {{-- Trip summary --}}
    <div class="sp-card mb-4">
      <div class="sp-card-body">
        <div class="d-flex align-items-center gap-3">
          <div class="driver-avatar">
            {{ strtoupper(substr($ratee['name'], 0, 2)) }}
          </div>
          <div>
            <div style="font-size:.9375rem;font-weight:400;color:var(--color-primary)">{{ $ratee['name'] }}</div>
            <div class="caption">{{ $ratee['department'] }} · {{ $ratee['role'] }}</div>
          </div>
        </div>
        <hr class="divider">
        <div class="d-flex gap-4 flex-wrap" style="font-size:.875rem">
          <div><span class="caption">Trip date: </span><span style="font-weight:400"><span class="num">{{ $trip['date'] }}</span></span></div>
          <div><span class="caption">Route: </span><span style="font-weight:400">{{ $trip['route'] }}</span></div>
          <div><span class="caption">Departure: </span><span style="font-weight:400"><span class="num">{{ $trip['departure_time'] }}</span></span></div>
        </div>
      </div>
    </div>

    {{-- Rating form --}}
    {{-- TODO: POST /ratings => RatingController::store() --}}
    <form method="POST" action="{{ url('/') }}" class="sp-validated-form" novalidate id="rating-form">
      @csrf
      <input type="hidden" name="trip_id" value="{{ $trip['id'] }}">
      <input type="hidden" name="ratee_id" value="{{ $ratee['id'] }}">

      <div class="sp-card mb-4">
        <div class="sp-card-body">

          {{-- Overall rating --}}
          <div class="mb-5">
            <div class="section-label mb-1">Overall experience</div>
            <p class="caption mb-4">How was your trip with {{ $ratee['name'] }}?</p>
            <div class="star-input" data-name="overall_rating" data-selected="0">
              @for($i = 1; $i <= 5; $i++)
              <i class="fa-solid fa-check"></i>
              @endfor
            </div>
            <input type="hidden" name="overall_rating" id="overall-rating-val" value="" required>
            <div class="caption mt-2" id="overall-rating-label" style="color:var(--color-text-muted)">Click a star to rate</div>
          </div>

          <hr class="divider">

          {{-- Sub-ratings --}}
          <div class="label-text mb-4">Detailed ratings</div>
          <div class="d-flex flex-column gap-4">

            @foreach([
              ['key' => 'punctuality',   'label' => 'Punctuality',    'desc' => 'Was on time for pickup'],
              ['key' => 'comfort',       'label' => 'Comfort',        'desc' => 'Vehicle comfort and driving'],
              ['key' => 'communication', 'label' => 'Communication',  'desc' => 'Clear updates and responsiveness'],
            ] as $cat)
            <div class="d-flex align-items-center justify-content-between gap-4 flex-wrap">
              <div style="min-width:140px">
                <div style="font-size:.9rem;font-weight:400;">{{ $cat['label'] }}</div>
                <div class="caption">{{ $cat['desc'] }}</div>
              </div>
              <div class="star-input-sm" data-name="{{ $cat['key'] }}_rating" data-selected="0">
                @for($i = 1; $i <= 5; $i++)
                <i class="fa-solid fa-check"></i>
                @endfor
                <input type="hidden" name="{{ $cat['key'] }}_rating" value="">
              </div>
            </div>
            @endforeach

          </div>
        </div>
      </div>

      {{-- Review text --}}
      <div class="sp-card mb-4">
        <div class="sp-card-body">
          <label for="review-text" class="form-label">
            Write a review
            <span class="caption">(optional)</span>
          </label>
          <textarea class="form-control" id="review-text" name="review_body" rows="4"
                    placeholder="Anything other colleagues should know? e.g. Great driver, always on time. Comfortable car."
                    maxlength="500"></textarea>
          <div class="d-flex justify-content-end mt-1">
            <span class="caption" id="review-char-count">0 / 500</span>
          </div>
        </div>
      </div>

      {{-- Submit --}}
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center">
        <a href="{{ url('/') }}" class="btn-outline-sp">Skip for now</a>
        <button type="submit" class="btn-accent-sp" id="btn-submit-rating">
          Submit rating
        </button>
      </div>
    </form>

  </div>
</div>
@endsection

@section('scripts')
<script>
  // Overall rating label
  const ratingLabels = ['', 'Poor', 'Below average', 'Average', 'Good', 'Excellent'];
  document.querySelector('.star-input').addEventListener('click', function() {
    const val = parseInt(this.getAttribute('data-selected') || '0', 10);
    document.getElementById('overall-rating-val').value = val;
    document.getElementById('overall-rating-label').textContent = val > 0 ? ratingLabels[val] : 'Click a star to rate';
    document.getElementById('overall-rating-label').style.color = val > 0 ? 'var(--color-primary)' : 'var(--color-text-muted)';
  });

  // Character count for review
  const reviewTextarea = document.getElementById('review-text');
  const charCount = document.getElementById('review-char-count');
  reviewTextarea.addEventListener('input', () => {
    charCount.textContent = `${reviewTextarea.value.length} / 500`;
  });

  // Validate that overall rating is selected
  document.getElementById('rating-form').addEventListener('submit', function(e) {
    const overall = document.getElementById('overall-rating-val').value;
    if (!overall) {
      e.preventDefault();
      showToast('Please give an overall star rating before submitting.', 'warning');
    }
  });
</script>
@endsection
