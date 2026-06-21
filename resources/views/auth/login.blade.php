{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign in — Smart Park & Share</title>
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

    <h1 class="headline mb-1">Sign in</h1>
    <p class="caption mb-5">Enter your company email address and we'll send you a verification code.</p>

    {{-- Login form --}}
    {{-- TODO: Replace action URL with POST /auth/send-otp backed by AuthController --}}
    <form method="POST" action="{{ url('/auth/otp-verify') }}" class="sp-validated-form" novalidate id="login-form">
      @csrf

      <div class="mb-4">
        <label for="email" class="form-label">Company email address</label>
        <input
          type="email"
          class="form-control"
          id="email"
          name="email"
          placeholder="you@company.com"
          autocomplete="email"
          required
          value="{{ old('email') }}"
        >
        <div class="invalid-feedback">Please enter a valid company email address.</div>
      </div>

      <button type="submit" class="btn-accent-sp w-100 justify-content-center mb-3" id="btn-send-code">
        Send verification code
      </button>

      <hr class="divider">

      <a href="{{ url('/auth/otp-verify') }}" class="btn-outline-sp w-100 justify-content-center" id="btn-sso">
        <i class="fa-solid fa-check"></i>
        Continue with company SSO
      </a>
    </form>

    <p class="caption text-center mt-5" style="color:var(--color-text-muted)">
      Having trouble signing in? Contact IT support at <a href="mailto:itsupport@company.com" style="color:var(--color-primary)">itsupport@company.com</a>
    </p>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFMKS1rNXZUkMfuS1cnRUCZT2yX" crossorigin="anonymous"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
  // Simulate sending OTP: show brief loading state
  document.getElementById('login-form').addEventListener('submit', function(e) {
    const btn = document.getElementById('btn-send-code');
    const email = document.getElementById('email').value;
    if (this.checkValidity()) {
      btn.textContent = 'Sending…';
      btn.disabled = true;
    }
  });
</script>
</body>
</html>
