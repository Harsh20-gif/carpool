<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Smart Park & Share — Internal carpool and parking reservation tool for HQ employees.">
  <title>@yield('title', 'Smart Park & Share')</title>

  {{-- Bootstrap 5 CSS --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  {{-- Font Awesome 6 CDN --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  {{-- App Design System --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  @yield('head')
</head>
<body class="app-layout">

  {{-- ═══ TOP NAVBAR (desktop) ═══ --}}
  <nav class="navbar app-navbar navbar-expand-md">
    <div class="container-fluid page-container">

      <a class="navbar-brand" href="{{ url('/') }}">
        <span class="brand-icon">
          <i class="fa-solid fa-parking" style="color:var(--color-secondary);font-size:14px"></i>
        </span>
        <span>
          Smart Park &amp; Share
          <span class="d-block" style="font-size:.65rem;font-weight:400;opacity:.65;letter-spacing:0">HQ Internal Tool</span>
        </span>
      </a>

      {{-- Desktop nav links --}}
      <div class="navbar-collapse">
        <ul class="navbar-nav ms-auto align-items-center gap-1">
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/') }}" data-route="/">
              <i class="fa-solid fa-house" style="margin-right:4px"></i> Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/carpool/search') }}" data-route="/carpool">
              <i class="fa-solid fa-car-side" style="margin-right:4px"></i> Carpool
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/parking') }}" data-route="/parking">
              <i class="fa-solid fa-square-parking" style="margin-right:4px"></i> Parking
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ url('/vehicles') }}" data-route="/vehicles">
              <i class="fa-solid fa-car" style="margin-right:4px"></i> My Vehicles
            </a>
          </li>
          <li class="nav-item ms-2">
            <div class="d-flex align-items-center gap-2">
              <span style="font-size:.8125rem;color:rgba(255,255,255,.6)">Admin</span>
              <a href="{{ url('/admin/dashboard') }}" class="nav-link py-1 px-2" style="font-size:.8125rem">Dashboard</a>
            </div>
          </li>
          <li class="nav-item ms-2">
            <a href="{{ url('/profile') }}" class="navbar-user-badge text-decoration-none" title="My profile">HB</a>
          </li>
        </ul>
      </div>

    </div>
  </nav>

  {{-- ═══ PAGE CONTENT ═══ --}}
  <main class="main-content" id="main-content">
    <div class="page-container">
      @yield('content')
    </div>
  </main>

  {{-- ═══ BOTTOM NAV (mobile) ═══ --}}
  <nav class="bottom-nav" aria-label="Mobile navigation">
    <ul class="bottom-nav-list">
      <li class="bottom-nav-item">
        <a href="{{ url('/') }}" class="bottom-nav-link" data-route="/">
          <i class="fa-solid fa-house" style="font-size:1.1rem;margin-bottom:2px"></i> Home
        </a>
      </li>
      <li class="bottom-nav-item">
        <a href="{{ url('/carpool/search') }}" class="bottom-nav-link" data-route="/carpool">
          <i class="fa-solid fa-car-side" style="font-size:1.1rem;margin-bottom:2px"></i> Carpool
        </a>
      </li>
      <li class="bottom-nav-item">
        <a href="{{ url('/parking') }}" class="bottom-nav-link" data-route="/parking">
          <i class="fa-solid fa-square-parking" style="font-size:1.1rem;margin-bottom:2px"></i> Parking
        </a>
      </li>
      <li class="bottom-nav-item">
        <a href="{{ url('/profile') }}" class="bottom-nav-link" data-route="/profile">
          <i class="fa-solid fa-user" style="font-size:1.1rem;margin-bottom:2px"></i> Profile
        </a>
      </li>
    </ul>
  </nav>

  {{-- Toast container --}}
  <div class="toast-container-sp" id="toast-container" role="status" aria-live="polite" aria-atomic="true"></div>

  {{-- Bootstrap 5 JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFMKS1rNXZUkMfuS1cnRUCZT2yX" crossorigin="anonymous"></script>

  {{-- Shared app scripts --}}
  <script src="{{ asset('js/app.js') }}"></script>

  @yield('scripts')
</body>
</html>
