{{-- resources/views/auth/otp-verify.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify code — Smart Park & Share</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    {{-- Logo --}}
    <div class="auth-logo">
      <div class="auth-logo-icon">
        <i class="fa-solid fa-parking"></i>
      </div>
      <div>
        <div class="auth-logo-text">Smart Park &amp; Share</div>
        <div class="auth-logo-sub">HQ Internal Tool · Employees only</div>
      </div>
    </div>

    <h1 class="headline mb-1">Check your email</h1>
    <p class="caption mb-1">We sent a 6-digit code to</p>
    <p class="mb-5" style="font-weight:600;font-size:.9375rem;color:var(--color-primary)">harsh.bhardwaj@company.com</p>

    {{-- OTP form --}}
    {{-- TODO: Replace with POST /auth/verify backed by AuthController::verifyOtp() --}}
    <form method="POST" action="{{ url('/') }}" class="sp-validated-form" novalidate id="otp-form">
      @csrf

      <div class="mb-5">
        <label class="form-label text-center d-block mb-3">Enter your 6-digit code</label>
        <div class="otp-input-group" id="otp-inputs">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" aria-label="Digit 1" required>
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 2" required>
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 3" required>
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 4" required>
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 5" required>
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 6" required>
        </div>
        {{-- Hidden combined value for form submission --}}
        <input type="hidden" name="otp" id="otp-combined">
      </div>

      <button type="submit" class="btn-accent-sp w-100 justify-content-center mb-3" id="btn-verify">
        Verify &amp; sign in
      </button>

      <hr class="divider">

      <a href="{{ url('/auth/login') }}" class="btn-outline-sp w-100 justify-content-center mb-4" id="btn-sso-otp">
        <i class="fa-solid fa-check"></i>
        Continue with company SSO instead
      </a>

      <div class="text-center">
        <span class="caption">Didn't receive the code? </span>
        <button type="button" class="btn btn-link p-0 caption" id="btn-resend" style="color:var(--color-primary);font-size:.8125rem">
          Resend code
        </button>
        <span class="caption" id="resend-timer"> (available in <span class="num" id="countdown">30</span>s)</span>
      </div>
    </form>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFMKS1rNXZUkMfuS1cnRUCZT2yX" crossorigin="anonymous"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
  // Combine OTP digits on submit
  document.getElementById('otp-form').addEventListener('submit', function() {
    const digits = document.querySelectorAll('#otp-inputs input');
    document.getElementById('otp-combined').value = Array.from(digits).map(i => i.value).join('');
  });

  // Resend countdown
  let seconds = 30;
  const countdownEl = document.getElementById('countdown');
  const resendBtn   = document.getElementById('btn-resend');
  const resendTimer = document.getElementById('resend-timer');
  resendBtn.disabled = true;

  const interval = setInterval(() => {
    seconds--;
    countdownEl.textContent = seconds;
    if (seconds <= 0) {
      clearInterval(interval);
      resendTimer.style.display = 'none';
      resendBtn.disabled = false;
    }
  }, 1000);

  resendBtn.addEventListener('click', function() {
    showToast('A new code has been sent to your email.');
    this.disabled = true;
    seconds = 30;
    resendTimer.style.display = '';
    countdownEl.textContent = seconds;
    // Restart countdown
    const newInterval = setInterval(() => {
      seconds--;
      countdownEl.textContent = seconds;
      if (seconds <= 0) { clearInterval(newInterval); resendTimer.style.display = 'none'; resendBtn.disabled = false; }
    }, 1000);
  });
</script>
</body>
</html>
