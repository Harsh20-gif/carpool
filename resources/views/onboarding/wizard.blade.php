{{--
  resources/views/onboarding/wizard.blade.php
  3-step onboarding wizard driven by vanilla JS.
  Steps:
    1 — Profile basics (name, department, photo)
    2 — Home location, work site, preferred pickup time
    3 — Add vehicle or skip (ride as passenger)
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set up your profile — Smart Park & Share</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background:var(--color-bg)">
<div style="min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:var(--space-5) var(--space-4)">

  {{-- Wordmark --}}
  <div style="margin-bottom:var(--space-5);text-align:center">
    <div style="font-size:1rem;font-weight:400;color:var(--color-primary)">Smart Park &amp; Share</div>
    <div style="font-size:.75rem;color:var(--color-text-secondary)">Let's set up your account — takes about 2 minutes</div>
  </div>

  <div class="auth-card" style="max-width:560px">

    {{-- ── Wizard Progress ── --}}
    <div class="wizard-progress mb-2" id="wizard-progress">
      <div class="wizard-step-indicator active" id="step-ind-1">
        <div class="wizard-step-num"><span class="num">1</span></div>
        <span class="wizard-step-name">Your profile</span>
      </div>
      <div class="wizard-step-indicator" id="step-ind-2">
        <div class="wizard-step-num"><span class="num">2</span></div>
        <span class="wizard-step-name">Commute details</span>
      </div>
      <div class="wizard-step-indicator" id="step-ind-3">
        <div class="wizard-step-num"><span class="num">3</span></div>
        <span class="wizard-step-name">Your vehicle</span>
      </div>
    </div>

    {{-- ── STEP 1: Profile basics ── --}}
    <div class="wizard-panel active" id="wizard-step-1">
      <h2 class="section-label mb-1">Tell us about yourself</h2>
      <p class="caption mb-4">This is shown to colleagues who'll share rides with you.</p>

      <div class="mb-3">
        {{-- Photo placeholder --}}
        <label class="form-label">Profile photo <span class="caption">(optional)</span></label>
        <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-1)">
          <div id="photo-preview" style="width:72px;height:72px;border-radius:8px;background:var(--color-primary-dim);display:flex;align-items:center;justify-content:center;color:var(--color-secondary);font-size:1.75rem;font-weight:400;flex-shrink:0;overflow:hidden">
            HB
          </div>
          <div>
            <label for="photo-upload" class="btn-outline-sp" style="cursor:pointer;font-size:.875rem">
              <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>

    {{-- ── STEP 2: Commute details ── --}}
    <div class="wizard-panel" id="wizard-step-2">
      <h2 class="section-label mb-1">Your commute</h2>
      <p class="caption mb-4">We use this to match you with nearby colleagues and suggest pickup times.</p>

      <div class="mb-3">
        <label for="home-location" class="form-label">Home area / neighbourhood <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="home-location" name="home_location" placeholder="e.g. Sector 62, Noida" required value="{{ old('home_location') }}">
        <div class="invalid-feedback">Please enter your home area.</div>
        <div class="caption mt-1">We'll never share your exact address.</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Work site</label>
        {{-- Fixed — read-only, not editable --}}
        <div class="form-control" style="background:var(--color-surface-alt);color:var(--color-text-secondary);cursor:not-allowed">
          HQ — Plot 42, Sector 18, Gurugram (all employees)
        </div>
        <div class="caption mt-1">The work site is fixed for all employees during the transition period.</div>
      </div>

      <div class="mb-3">
        <label for="pickup-time" class="form-label">Preferred pickup time <span class="text-danger">*</span></label>
        <input type="time" class="form-control" id="pickup-time" name="preferred_pickup_time" value="08:30" required>
        <div class="invalid-feedback">Please set a preferred pickup time.</div>
      </div>

      <div class="mb-4">
        <label class="form-label">Typical commute days</label>
        <div class="day-chips mt-1">
          @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
            <button type="button" class="day-chip {{ in_array($day, ['Mon','Tue','Wed','Thu','Fri']) ? 'selected' : '' }}" data-day="{{ $day }}">{{ $day }}</button>
          @endforeach
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <button type="button" class="btn-outline-sp" id="step2-back">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>

    {{-- ── STEP 3: Vehicle ── --}}
    <div class="wizard-panel" id="wizard-step-3">
      <h2 class="section-label mb-1">Do you have a vehicle?</h2>
      <p class="caption mb-5">Adding a vehicle lets you offer rides to colleagues. You can always add one later from My Vehicles.</p>

      <div class="d-flex flex-column gap-3 mb-5">
        {{-- Add vehicle option --}}
        {{-- TODO: POST /onboarding/wizard => real OnboardingController::complete(), then redirect to /vehicles/create --}}
        <form method="POST" action="{{ route('onboarding.wizard.submit') }}">
          @csrf
          <input type="hidden" name="vehicle_choice" value="add">
          <button type="submit" class="btn-primary-sp w-100 justify-content-center" id="btn-add-vehicle" style="padding:var(--space-3) var(--space-5)">
            <i class="fa-solid fa-chevron-left"></i>
        Back
      </button>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFMKS1rNXZUkMfuS1cnRUCZT2yX" crossorigin="anonymous"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
  // ── WIZARD STATE ──────────────────────────────────────────
  let currentStep = 1;

  function goToStep(n) {
    document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('wizard-step-' + n)?.classList.add('active');

    // Update step indicators
    for (let i = 1; i <= 3; i++) {
      const ind = document.getElementById('step-ind-' + i);
      ind.classList.remove('active', 'completed');
      if (i < n)       ind.classList.add('completed');
      else if (i === n) ind.classList.add('active');
    }
    currentStep = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Step 1 → 2
  document.getElementById('step1-next').addEventListener('click', () => {
    const firstName = document.getElementById('first-name');
    const lastName  = document.getElementById('last-name');
    const dept      = document.getElementById('department');

    [firstName, lastName, dept].forEach(el => el.classList.remove('is-invalid'));

    let valid = true;
    if (!firstName.value.trim()) { firstName.classList.add('is-invalid'); valid = false; }
    if (!lastName.value.trim())  { lastName.classList.add('is-invalid');  valid = false; }
    if (!dept.value)              { dept.classList.add('is-invalid');      valid = false; }

    if (!valid) {
      showToast('Please fill in all required fields before continuing.', 'warning');
      return;
    }
    goToStep(2);
  });

  // Step 2 → 3
  document.getElementById('step2-next').addEventListener('click', () => {
    const home = document.getElementById('home-location');
    const time = document.getElementById('pickup-time');

    [home, time].forEach(el => el.classList.remove('is-invalid'));

    let valid = true;
    if (!home.value.trim()) { home.classList.add('is-invalid'); valid = false; }
    if (!time.value)        { time.classList.add('is-invalid'); valid = false; }

    if (!valid) {
      showToast('Please fill in your commute details.', 'warning');
      return;
    }
    goToStep(3);
  });

  document.getElementById('step2-back').addEventListener('click', () => goToStep(1));
  document.getElementById('step3-back').addEventListener('click', () => goToStep(2));

  // Photo preview
  document.getElementById('photo-upload').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      const preview = document.getElementById('photo-preview');
      preview.style.background = 'none';
      preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:8px">`;
    };
    reader.readAsDataURL(file);
  });
</script>
</body>
</html>
