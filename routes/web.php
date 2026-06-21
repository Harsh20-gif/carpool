<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Smart Park & Share — Web Routes
|--------------------------------------------------------------------------
| All routes currently return mock data via hardcoded PHP arrays.
| TODO: Replace each mock data block with real Eloquent queries once
| the database, models, and auth are implemented.
|--------------------------------------------------------------------------
*/

/*
|==========================================================================
| ⚠  TEMPORARY MOCK AUTH GATE — REMOVE WHEN REAL AUTH IS IN PLACE
|==========================================================================
| The session key `mock_logged_in` is used as a lightweight substitute for
| Laravel's built-in auth guard while the database layer does not yet exist.
|
| HOW IT WORKS (current):
|   • POST /auth/login  → stores email in session, redirects to OTP page
|   • POST /auth/otp-verify → sets session('mock_logged_in', true), redirects to /
|   • POST /onboarding/wizard → sets mock_logged_in + mock_onboarded, redirects to /
|   • POST /auth/logout → clears all mock session keys
|   • Protected routes check session('mock_logged_in') inline and redirect
|     to /auth/login if the key is absent/false.
|
| WHAT REPLACES THIS (later):
|   • Real auth: Laravel Sanctum / Fortify with a `users` table
|   • Replace the inline session check with middleware:
|       - `auth:sanctum` (or `auth`) for the protected route group
|       - A custom `OnboardingComplete` middleware for post-onboarding pages
|   • The mock session keys (mock_logged_in, mock_onboarded, pending_email)
|     are throwaway — delete them once real auth is wired up.
|==========================================================================
*/

/** Helper closure: redirect to login if mock_logged_in is not set. */
$requireMockAuth = function () {
    if (! session('mock_logged_in')) {
        return redirect()->route('auth.login');
    }
    return null; // continue
};

/* ──────────────────────────────────────────────────────────────────────────
   AUTH ROUTES  (no mock_logged_in check — these are the entry points)
   TODO: Replace with Laravel Sanctum / Fortify auth flow
────────────────────────────────────────────────────────────────────────── */

Route::get('/auth/login', function () {
    return view('auth.login');
})->name('auth.login');

Route::get('/auth/otp-verify', function () {
    return view('auth.otp-verify');
})->name('auth.otp.verify');

// POST /auth/login
// Stores the submitted email in session so the OTP page can reference it,
// then redirects to the OTP verification step.
// TODO: Validate that the email belongs to the corporate domain (@company.com),
//       look up or provision the user record, and trigger a real OTP send
//       (e.g. via an OtpService / Twilio / email magic-link) before redirecting.
Route::post('/auth/login', function (Request $request) {
    session(['pending_email' => $request->email]);
    return redirect()->route('auth.otp.verify');
})->name('auth.login.submit');

// POST /auth/otp-verify
// Sets the mock_logged_in session flag so all protected routes pass,
// then sends the user to the home dashboard.
// TODO: Verify the submitted OTP against the one stored/sent for pending_email.
//       On success, log the real user in with Auth::login($user) and clear the
//       pending_email + otp session keys instead of setting mock_logged_in.
Route::post('/auth/otp-verify', function () {
    session(['mock_logged_in' => true]);   // ← mock auth flag; remove when real auth lands
    return redirect()->route('home');
})->name('auth.otp.submit');

// POST /auth/logout
Route::post('/auth/logout', function () {
    session()->forget(['mock_logged_in', 'mock_onboarded', 'pending_email']);
    return redirect()->route('auth.login');
})->name('auth.logout');

/* ──────────────────────────────────────────────────────────────────────────
   ONBOARDING  (no mock_logged_in gate — happens right after OTP, before
   mock_onboarded is set, so gating on mock_logged_in would block it)
   TODO: Replace with OnboardingController backed by User model
────────────────────────────────────────────────────────────────────────── */

Route::get('/onboarding/wizard', function () {
    return view('onboarding.wizard');
})->name('onboarding.wizard');

// POST /onboarding/wizard
// Accepts the final wizard submission (profile basics + commute details +
// optional vehicle choice), marks the session as onboarded, and sends the
// user to the home dashboard.
// TODO: Once Eloquent models exist, replace this with:
//         User::create([...]) using $request fields (first_name, last_name,
//         department, home_location, preferred_pickup_time, commute_days).
//       If vehicle_choice != 'skip', redirect to /vehicles/create instead of /.
//       Remove the mock_logged_in / mock_onboarded session keys.
Route::post('/onboarding/wizard', function (Request $request) {
    session(['mock_logged_in' => true, 'mock_onboarded' => true]);
    return redirect()->route('home');
})->name('onboarding.wizard.submit');

/* ──────────────────────────────────────────────────────────────────────────
   HOME  (protected)
   TODO: Replace with HomeController::index() using auth()->user()
         and Trip::todayForUser(), ParkingSpot::availability()
────────────────────────────────────────────────────────────────────────── */

// Route::get('/', function () use ($requireMockAuth) {
//     if ($redirect = $requireMockAuth()) {
//         return $redirect;
//     }

//     // TODO: $user = auth()->user();
//     $user = ['first_name' => 'Harsh', 'last_name' => 'Bhardwaj', 'email' => 'harsh.bhardwaj@company.com'];

//     // TODO: $todayTrip = Trip::todayForUser(auth()->id())->first();
//     $todayTrip = [
//         'id'              => 1,
//         'status'          => 'confirmed',   // 'confirmed' | 'pending' | 'needs_action'
//         'driver'          => 'Aditya Sharma',
//         'pickup_time'     => '08:30',
//         'route'           => 'Sector 62, Noida → HQ Gurugram',
//         'seats_remaining' => 2,
//     ];

//     // TODO: $parking = ParkingSpot::zoneAvailability()->toArray();
//     $parking = [
//         'total'     => 120,
//         'available' => 34,
//         'zone_a'    => ['available' => 14, 'status' => 'available'],
//         'zone_b'    => ['available' => 5,  'status' => 'low'],
//         'zone_c'    => ['available' => 15, 'status' => 'available'],
//     ];

//     $availableSpots = $parking['available'];
//     $totalSpots     = $parking['total'];
//     $parkingStatus  = $availableSpots > 20 ? 'available' : ($availableSpots > 5 ? 'low' : 'full');

//     // TODO: $recentActivity = Activity::forUser(auth()->id())->take(5)->get()->toArray();
//     $recentActivity = [
//         ['type' => 'carpool', 'title' => 'Trip with Aditya Sharma',  'subtitle' => 'Sector 62 → HQ · Driver',   'date' => 'Today'],
//         ['type' => 'parking', 'title' => 'Zone A spot reserved',     'subtitle' => 'Ref: R20260617-2841',        'date' => 'Yesterday'],
//         ['type' => 'carpool', 'title' => 'Trip with Priya Mehta',    'subtitle' => 'Sector 50 → HQ · Passenger', 'date' => '16 Jun'],
//         ['type' => 'carpool', 'title' => 'Trip with Rohan Gupta',   'subtitle' => 'DLF Phase 2 → HQ · Driver',  'date' => '14 Jun'],
//     ];

//     return view('home', compact('user', 'todayTrip', 'parking', 'availableSpots', 'totalSpots', 'parkingStatus', 'recentActivity'));
// })->name('home');

/* ──────────────────────────────────────────────────────────────────────────
   CARPOOL  (protected)
   TODO: Replace with CarpoolOfferController backed by CarpoolOffer model
────────────────────────────────────────────────────────────────────────── */

Route::get('/carpool/search', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $offers = CarpoolOffer::availableForDate(request('date'))->with('driver','vehicle')->get()->toArray();
    $offers = [
        [
            'id'                => 1,
            'driver_name'       => 'Aditya Sharma',
            'driver_department' => 'Engineering',
            'rating'            => 4.8,
            'trips_completed'   => 47,
            'route'             => 'Sector 62, Noida → HQ',
            'departure_time'    => '08:15',
            'vehicle'           => 'Maruti Swift',
            'seats_available'   => 3,
        ],
        [
            'id'                => 2,
            'driver_name'       => 'Priya Mehta',
            'driver_department' => 'Product & Design',
            'rating'            => 4.6,
            'trips_completed'   => 29,
            'route'             => 'Sector 50, Gurugram → HQ',
            'departure_time'    => '08:45',
            'vehicle'           => 'Honda City',
            'seats_available'   => 2,
        ],
        [
            'id'                => 3,
            'driver_name'       => 'Rohan Gupta',
            'driver_department' => 'Finance',
            'rating'            => 4.9,
            'trips_completed'   => 63,
            'route'             => 'DLF Phase 2 → HQ',
            'departure_time'    => '09:00',
            'vehicle'           => 'Hyundai Creta',
            'seats_available'   => 1,
        ],
        [
            'id'                => 4,
            'driver_name'       => 'Sneha Rajput',
            'driver_department' => 'Operations',
            'rating'            => 4.4,
            'trips_completed'   => 18,
            'route'             => 'IFFCO Chowk area → HQ',
            'departure_time'    => '07:50',
            'vehicle'           => 'Tata Nexon EV',
            'seats_available'   => 0,
        ],
    ];

    // TODO: $vehicles = Vehicle::where('user_id', auth()->id())->get()->toArray();
    $vehicles = [
        ['id' => 1, 'make' => 'Maruti', 'model' => 'Swift Dzire', 'plate' => 'DL01AB1234', 'seats' => 5, 'verified' => true],
        ['id' => 2, 'make' => 'Toyota', 'model' => 'Innova',       'plate' => 'HR26DC5678', 'seats' => 7, 'verified' => false],
    ];

    return view('carpool.search', compact('offers', 'vehicles'));
})->name('carpool.search');

// POST stub for offer submission
Route::post('/carpool/search', function () {
    return redirect('/carpool/search')->with('success', 'Ride offer posted successfully.');
})->name('carpool.search.post');

Route::get('/carpool/offer', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $vehicles = Vehicle::where('user_id', auth()->id())->verified()->get()->toArray();
    $vehicles = [
        ['id' => 1, 'make' => 'Maruti', 'model' => 'Swift Dzire', 'plate' => 'DL01AB1234', 'seats' => 5, 'verified' => true],
        ['id' => 2, 'make' => 'Toyota', 'model' => 'Innova',       'plate' => 'HR26DC5678', 'seats' => 7, 'verified' => false],
    ];
    return view('carpool.offer', compact('vehicles'));
})->name('carpool.offer');

Route::get('/carpool/trip/{id}', function ($id) use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $trip = Trip::with('driver','passengers','messages')->findOrFail($id)->toArray();
    $trip = [
        'id'             => $id,
        'status'         => 'confirmed',
        'date'           => 'Wednesday, 18 June 2026',
        'departure_time' => '08:15',
        'pickup_point'   => 'Sector 62 Metro Station, Gate 3, Noida',
        'pickup_ready_time' => '08:12',
        'eta'            => '09:10',
        'driver' => [
            'name'          => 'Aditya Sharma',
            'department'    => 'Engineering',
            'rating'        => 4.8,
            'vehicle'       => 'Maruti Swift Dzire',
            'plate'         => 'DL 01 AB 1234',
            'vehicle_color' => 'Silver',
        ],
        'passengers' => [
            ['name' => 'Harsh Bhardwaj', 'department' => 'Engineering',   'is_me' => true],
            ['name' => 'Priya Mehta',    'department' => 'Product & Design','is_me' => false],
        ],
        'chat_messages' => [
            ['sender' => 'Aditya Sharma', 'body' => "Good morning! I'll be at Gate 3 by 8:12. Look for the silver Swift.", 'time' => '07:55', 'is_mine' => false],
            ['sender' => 'Me',            'body' => 'Perfect, I\'ll be there. Running slightly early.', 'time' => '08:02', 'is_mine' => true],
            ['sender' => 'Priya Mehta',   'body' => 'Same here — see you both in a few minutes!',       'time' => '08:05', 'is_mine' => false],
        ],
    ];

    return view('carpool.trip-detail', compact('trip'));
})->name('carpool.trip.detail');

/* ──────────────────────────────────────────────────────────────────────────
   VEHICLES  (protected)
   TODO: Replace with VehicleController backed by Vehicle model
────────────────────────────────────────────────────────────────────────── */

Route::get('/vehicles', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $vehicles = Vehicle::where('user_id', auth()->id())->get()->toArray();
    $vehicles = [
        [
            'id'        => 1,
            'make'      => 'Maruti',
            'model'     => 'Swift Dzire',
            'year'      => 2021,
            'type'      => 'Sedan',
            'plate'     => 'DL 01 AB 1234',
            'seats'     => 5,
            'fuel_type' => 'Petrol',
            'color'     => 'Silver',
            'verified'  => true,
            'features'  => ['AC', 'USB Charging', 'Music System'],
        ],
        [
            'id'        => 2,
            'make'      => 'Toyota',
            'model'     => 'Innova Crysta',
            'year'      => 2020,
            'type'      => 'MPV',
            'plate'     => 'HR 26 DC 5678',
            'seats'     => 7,
            'fuel_type' => 'Diesel',
            'color'     => 'White',
            'verified'  => false,
            'features'  => ['AC', 'Luggage Space'],
        ],
    ];

    return view('vehicles.index', compact('vehicles'));
})->name('vehicles.index');

Route::get('/vehicles/create', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    return view('vehicles.create');
})->name('vehicles.create');

// POST stub — would call VehicleController::store()
Route::post('/vehicles', function () {
    return redirect('/vehicles')->with('success', 'Vehicle submitted for verification.');
})->name('vehicles.store');

// DELETE stub
Route::delete('/vehicles/{id}', function ($id) {
    return redirect('/vehicles')->with('success', 'Vehicle removed.');
})->name('vehicles.destroy');

/* ──────────────────────────────────────────────────────────────────────────
   PARKING  (protected)
   TODO: Replace with ParkingController backed by ParkingZone, ParkingSpot,
         ParkingReservation models
────────────────────────────────────────────────────────────────────────── */

Route::get('/parking', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $zones = ParkingZone::with('availableSpots')->get()->toArray();
    $zones = [
        [
            'id'        => 'A',
            'name'      => 'Zone A',
            'sublabel'  => 'Main Entrance',
            'total'     => 40,
            'available' => 14,
            'status'    => 'available',
            'notes'     => 'Nearest to the main building lobby.',
        ],
        [
            'id'        => 'B',
            'name'      => 'Zone B',
            'sublabel'  => 'EV Charging',
            'total'     => 20,
            'available' => 3,
            'status'    => 'low',
            'notes'     => 'For electric and hybrid vehicles only.',
        ],
        [
            'id'        => 'C',
            'name'      => 'Zone C',
            'sublabel'  => 'Carpool Priority',
            'total'     => 30,
            'available' => 17,
            'status'    => 'available',
            'notes'     => 'Priority for 3+ passengers per vehicle.',
        ],
    ];

    // TODO: $reservations = ParkingReservation::where('user_id', auth()->id())->recent()->get()->toArray();
    $reservations = [
        ['ref' => 'R20260618-4821', 'zone' => 'Zone A', 'date' => '18 Jun 2026', 'time_window' => '08:00 – 10:00', 'status' => 'checkedin'],
        ['ref' => 'R20260617-2841', 'zone' => 'Zone C', 'date' => '17 Jun 2026', 'time_window' => '08:30 – 10:30', 'status' => 'expired'],
        ['ref' => 'R20260619-9031', 'zone' => 'Zone A', 'date' => '19 Jun 2026', 'time_window' => '08:00 – 10:00', 'status' => 'reserved'],
    ];

    $totalSpots     = array_sum(array_column($zones, 'total'));
    $availableSpots = array_sum(array_column($zones, 'available'));
    $overallStatus  = $availableSpots > 20 ? 'available' : ($availableSpots > 5 ? 'low' : 'full');

    return view('parking.index', compact('zones', 'reservations', 'totalSpots', 'availableSpots', 'overallStatus'));
})->name('parking.index');

/* ──────────────────────────────────────────────────────────────────────────
   RATINGS  (protected)
   TODO: Replace with RatingController backed by Rating model
────────────────────────────────────────────────────────────────────────── */

Route::get('/ratings/create', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $trip = Trip::findOrFail(request('trip_id'))->toArray();
    // TODO: $ratee = User::findOrFail(request('ratee_id'))->toArray();
    $trip = [
        'id'             => 1,
        'date'           => 'Wednesday, 18 June 2026',
        'route'          => 'Sector 62 → HQ Gurugram',
        'departure_time' => '08:15',
    ];

    $ratee = [
        'id'         => 2,
        'name'       => 'Aditya Sharma',
        'department' => 'Engineering',
        'role'       => 'Driver',
    ];

    return view('ratings.create', compact('trip', 'ratee'));
})->name('ratings.create');

// POST stub
Route::post('/ratings', function () {
    return redirect('/')->with('success', 'Rating submitted. Thank you!');
})->name('ratings.store');

/* ──────────────────────────────────────────────────────────────────────────
   PROFILE  (protected)
   TODO: Replace with ProfileController backed by User model
────────────────────────────────────────────────────────────────────────── */

Route::get('/profile', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $user = auth()->user()->load('vehicles','trips','ratings')->toArray();
    $user = [
        'full_name'    => 'Harsh Bhardwaj',
        'email'        => 'harsh.bhardwaj@company.com',
        'department'   => 'Engineering',
        'avg_rating'   => 4.7,
        'total_ratings'=> 31,
        'vehicles'     => [1, 2],
        'recent_trips' => [
            ['route' => 'Sector 62 → HQ',    'date' => '18 Jun', 'role' => 'Passenger', 'rating' => 5],
            ['route' => 'DLF Phase 2 → HQ',  'date' => '17 Jun', 'role' => 'Driver',    'rating' => 4],
            ['route' => 'Sector 62 → HQ',    'date' => '16 Jun', 'role' => 'Passenger', 'rating' => null],
            ['route' => 'Sector 50 → HQ',    'date' => '14 Jun', 'role' => 'Driver',    'rating' => 5],
        ],
    ];

    // TODO: $stats = UserStatsService::compute(auth()->id());
    $stats = [
        'trips_taken'           => 47,
        'co2_saved'             => 188,   // kg
        'money_saved'           => 14200, // ₹
        'parking_reservations'  => 18,
        'parking_checkins'      => 15,
    ];

    return view('profile.show', compact('user', 'stats'));
})->name('profile.show');

/* ──────────────────────────────────────────────────────────────────────────
   ADMIN  (protected)
   TODO: Replace with AdminDashboardController backed by aggregated queries
────────────────────────────────────────────────────────────────────────── */

Route::get('/admin/dashboard', function () use ($requireMockAuth) {
    if ($redirect = $requireMockAuth()) {
        return $redirect;
    }

    // TODO: $stats = AdminStatsService::weeklyStats();
    $stats = [
        'trips_this_week'     => 143,
        'trips_change'        => 18,  // % vs last week
        'avg_occupancy'       => 2.7,
        'parking_utilisation' => 78,  // %
        'sov_reduction'       => 32,  // % vs pre-HQ-move baseline
    ];

    // TODO: $carpoolTrend = Trip::groupBy('date')->thisWeek()->selectRaw('date, count(*) as trips, sum(passengers) as passengers')->get();
    $carpoolTrend = [
        'labels'     => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'values'     => [24,    31,    28,    35,    25,    0,     0   ],  // trips
        'passengers' => [62,    82,    71,    94,    68,    0,     0   ],  // total passengers
    ];

    // TODO: $zoneBreakdown = ParkingZone::with('todayStats')->get()->toArray();
    $zoneBreakdown = [
        ['name' => 'Zone A', 'sublabel' => 'Main Entrance',   'total' => 40, 'available' => 14, 'status' => 'available'],
        ['name' => 'Zone B', 'sublabel' => 'EV Charging',     'total' => 20, 'available' => 3,  'status' => 'low'],
        ['name' => 'Zone C', 'sublabel' => 'Carpool Priority','total' => 30, 'available' => 17, 'status' => 'available'],
    ];

    // TODO: $recentReservations = ParkingReservation::with('user.department')->latest()->take(20)->get()->toArray();
    $recentReservations = [
        ['ref' => 'R20260618-4821', 'employee_name' => 'Harsh Bhardwaj',  'department' => 'Engineering',      'zone' => 'Zone A', 'date' => '18 Jun 2026', 'time_window' => '08:00–10:00', 'status' => 'checkedin'],
        ['ref' => 'R20260618-4830', 'employee_name' => 'Priya Mehta',     'department' => 'Product & Design',  'zone' => 'Zone C', 'date' => '18 Jun 2026', 'time_window' => '08:30–10:30', 'status' => 'reserved'],
        ['ref' => 'R20260618-4799', 'employee_name' => 'Rohan Gupta',     'department' => 'Finance',           'zone' => 'Zone B', 'date' => '18 Jun 2026', 'time_window' => '09:00–11:00', 'status' => 'reserved'],
        ['ref' => 'R20260618-4812', 'employee_name' => 'Sneha Rajput',    'department' => 'Operations',        'zone' => 'Zone A', 'date' => '18 Jun 2026', 'time_window' => '07:30–09:30', 'status' => 'checkedin'],
        ['ref' => 'R20260618-4790', 'employee_name' => 'Vikram Nair',     'department' => 'Sales',             'zone' => 'Zone A', 'date' => '18 Jun 2026', 'time_window' => '08:00–10:00', 'status' => 'expired'],
        ['ref' => 'R20260617-4701', 'employee_name' => 'Ananya Iyer',     'department' => 'People & Culture',  'zone' => 'Zone C', 'date' => '17 Jun 2026', 'time_window' => '08:00–10:00', 'status' => 'checkedin'],
        ['ref' => 'R20260617-4688', 'employee_name' => 'Rahul Verma',     'department' => 'Engineering',       'zone' => 'Zone B', 'date' => '17 Jun 2026', 'time_window' => '09:00–11:00', 'status' => 'expired'],
        ['ref' => 'R20260617-4672', 'employee_name' => 'Kavita Sharma',   'department' => 'Legal',             'zone' => 'Zone A', 'date' => '17 Jun 2026', 'time_window' => '08:30–10:30', 'status' => 'reserved'],
    ];

    return view('admin.dashboard', compact('stats', 'carpoolTrend', 'zoneBreakdown', 'recentReservations'));
})->name('admin.dashboard');
