<?php
/* ==========================================================================
   NEW_DASH  —  DELIVERY OPS DASHBOARD  (frontend-only demo)
   --------------------------------------------------------------------------
   A standalone courier-desk dashboard: book a courier, watch the fleet,
   follow a live route on the map, and rate completed deliveries.

   This screen is intentionally NOT wired to the backend. There is no
   session_start(), no helpers/api_helper.php require and no
   make_api_request() call anywhere in New_dash/. Every number on the page
   comes from the static arrays below, and both the ratings and the bookings
   a user creates are persisted to localStorage only.

   Design parity: the sidebar / topbar markup is a static copy of
   src/includes/sidebar.php and src/components/top_header.php (kept in
   New_dash/components/), and the page loads the shared
   assets/css/style.css + assets/css/theme.css layer, so .crm-card, .crm-kpi,
   .crm-table, .crm-badge-*, .crm-pill, .crm-modal and html.dark all behave
   exactly as they do in the live portals. Delivery-only rules live in
   New_dash/css/dashboard.css.

   Open it directly:  /New_dash/dashboard.php
   ========================================================================== */

$page_title = 'Delivery Ops Dashboard - Priority Handling';

/* Greeting name for the welcome banner. The upstream dashboard reads this from
   $_SESSION; this demo has no session, so it is a plain variable. */
$displayName = 'D. Cruz';

/* --------------------------------------------------------------------------
   DEMO DATA  —  active deliveries
   vehicle: motorcycle | van | pickup | tricycle
   status : scheduled | picked_up | in_transit | out_for_delivery | delivered
   rating : null while the job is open, 1-5 once the recipient has scored it
   -------------------------------------------------------------------------- */
$deliveries = [
    [
        'id' => 'WB-90412', 'from' => 'Makati CDC', 'to' => 'BGC, Taguig',
        'recipient' => 'N. Alvarez', 'courier' => 'J. Ramos', 'vehicle' => 'motorcycle',
        'status' => 'out_for_delivery', 'eta' => '14 min', 'progress' => 78,
        'distance' => '8.4 km', 'weight' => '2.1 kg', 'slots' => 1, 'rating' => null,
        'service' => 'Same-day', 'picked' => '09:42 AM',
    ],
    [
        'id' => 'WB-90408', 'from' => 'Quezon City Hub', 'to' => 'Alabang, Muntinlupa',
        'recipient' => 'Triya Retail', 'courier' => 'M. Dela Peña', 'vehicle' => 'van',
        'status' => 'in_transit', 'eta' => '52 min', 'progress' => 41,
        'distance' => '23.7 km', 'weight' => '412 kg', 'slots' => 14, 'rating' => null,
        'service' => 'Scheduled', 'picked' => '09:05 AM',
    ],
    [
        'id' => 'WB-90399', 'from' => 'Makati CDC', 'to' => 'Ortigas, Pasig',
        'recipient' => 'K. Ocampo', 'courier' => 'R. Villanueva', 'vehicle' => 'motorcycle',
        'status' => 'picked_up', 'eta' => '1 hr 20 min', 'progress' => 12,
        'distance' => '11.2 km', 'weight' => '0.8 kg', 'slots' => 1, 'rating' => null,
        'service' => 'Express', 'picked' => '10:18 AM',
    ],
    [
        'id' => 'WB-90385', 'from' => 'Navotas Depot', 'to' => 'Cavite City',
        'recipient' => 'Southline Parts', 'courier' => 'A. Bautista', 'vehicle' => 'pickup',
        'status' => 'scheduled', 'eta' => '2 hr 05 min', 'progress' => 0,
        'distance' => '38.9 km', 'weight' => '265 kg', 'slots' => 9, 'rating' => null,
        'service' => 'Scheduled', 'picked' => '—',
    ],
    [
        'id' => 'WB-90377', 'from' => 'Pasay Branch', 'to' => 'Binondo, Manila',
        'recipient' => 'R. Tan', 'courier' => 'E. Gonzales', 'vehicle' => 'tricycle',
        'status' => 'in_transit', 'eta' => '27 min', 'progress' => 63,
        'distance' => '5.6 km', 'weight' => '12 kg', 'slots' => 3, 'rating' => null,
        'service' => 'Same-day', 'picked' => '10:02 AM',
    ],
    [
        'id' => 'WB-90361', 'from' => 'Quezon City Hub', 'to' => 'Fairview, Quezon City',
        'recipient' => 'B. Mendoza', 'courier' => 'L. Ignacio', 'vehicle' => 'motorcycle',
        'status' => 'delivered', 'eta' => 'Delivered 10:41 AM', 'progress' => 100,
        'distance' => '6.9 km', 'weight' => '1.4 kg', 'slots' => 1, 'rating' => 5,
        'service' => 'Express', 'picked' => '09:58 AM',
    ],
    [
        'id' => 'WB-90344', 'from' => 'Makati CDC', 'to' => 'Rockwell, Makati',
        'recipient' => 'C. Sy', 'courier' => 'J. Ramos', 'vehicle' => 'van',
        'status' => 'delivered', 'eta' => 'Delivered 09:12 AM', 'progress' => 100,
        'distance' => '3.2 km', 'weight' => '188 kg', 'slots' => 6, 'rating' => 4,
        'service' => 'Scheduled', 'picked' => '08:20 AM',
    ],
    [
        'id' => 'WB-90330', 'from' => 'Navotas Depot', 'to' => 'SM North, Quezon City',
        'recipient' => 'Northline Ops', 'courier' => 'P. Aquino', 'vehicle' => 'pickup',
        'status' => 'delivered', 'eta' => 'Delivered 08:47 AM', 'progress' => 100,
        'distance' => '14.8 km', 'weight' => '96 kg', 'slots' => 4, 'rating' => null,
        'service' => 'Same-day', 'picked' => '07:55 AM',
    ],
];

/* Status -> shared badge class, so the pills match the rest of the portal. */
$status_badges = [
    'scheduled'        => ['badge' => 'crm-badge-slate',  'label' => 'Scheduled'],
    'picked_up'        => ['badge' => 'crm-badge-violet', 'label' => 'Picked up'],
    'in_transit'       => ['badge' => 'crm-badge-blue',   'label' => 'In transit'],
    'out_for_delivery' => ['badge' => 'crm-badge-amber',  'label' => 'Out for delivery'],
    'delivered'        => ['badge' => 'crm-badge-green',  'label' => 'Delivered'],
];

/* Vehicle -> label + icon, reused by the fleet strip, the table and the
   booking form's select so the wording never drifts between the three. */
$vehicle_meta = [
    'motorcycle' => ['label' => 'Motorcycle', 'icon' => 'fa-motorcycle', 'capacity' => 'up to 5 kg',    'rate' => 79],
    'tricycle'   => ['label' => 'Tricycle',   'icon' => 'fa-bicycle',    'capacity' => 'up to 25 kg',   'rate' => 110],
    'van'        => ['label' => 'L300 Van',   'icon' => 'fa-truck',      'capacity' => 'up to 600 kg',  'rate' => 420],
    'pickup'     => ['label' => 'Pickup',     'icon' => 'fa-truck-pickup', 'capacity' => 'up to 1,200 kg', 'rate' => 680],
];

/* --------------------------------------------------------------------------
   DEMO DATA  —  fleet availability (the vehicle-variety strip)
   Keyed by vehicle type so $vehicle_meta above can be merged into it and the
   booking picker, the fleet cards and the fare quote all read one source.
   -------------------------------------------------------------------------- */
$fleet = [
    'motorcycle' => ['available' => 14, 'total' => 18, 'note' => 'Single parcels and documents, beats the traffic.'],
    'tricycle'   => ['available' => 6,  'total' => 8,  'note' => 'Short hops inside barangays and narrow alleys.'],
    'van'        => ['available' => 5,  'total' => 9,  'note' => 'Palletised retail drops and multi-stop routes.'],
    'pickup'     => ['available' => 3,  'total' => 6,  'note' => 'Oversized freight, warehouse-to-warehouse runs.'],
];
foreach ($fleet as $f_key => &$f_row) {
    $f_row = array_merge($vehicle_meta[$f_key] ?? [], $f_row);
}
unset($f_row);

/* --------------------------------------------------------------------------
   DEMO DATA  —  notification bell (mirrors the shape top_header.php expects)
   -------------------------------------------------------------------------- */
$notifications = [
    ['id' => 1, 'type' => 'urgent',  'title' => 'WB-90385 at risk',   'message' => 'Pickup window closes in 40 min; no courier assigned yet.', 'time' => '6 min ago', 'link' => '#active-deliveries'],
    ['id' => 2, 'type' => 'warning', 'title' => 'Van capacity low',   'message' => 'Only 5 of 9 vans free after 1:00 PM today.',               'time' => '22 min ago', 'link' => '#fleet'],
    ['id' => 3, 'type' => 'success', 'title' => 'POD uploaded',       'message' => 'Photo proof of delivery captured for WB-90361.',           'time' => '1 hr ago',  'link' => '#active-deliveries'],
    ['id' => 4, 'type' => 'info',    'title' => 'New 5-star review',  'message' => 'B. Mendoza rated L. Ignacio "On time, careful handling".', 'time' => '2 hr ago',  'link' => '#feedback'],
];

/* --------------------------------------------------------------------------
   DERIVED  —  KPI figures computed from the arrays above so the cards, the
   fleet strip and the table can never disagree with each other.
   -------------------------------------------------------------------------- */
$total_deliveries   = count($deliveries);
$active_count       = count(array_filter($deliveries, fn($d) => $d['status'] !== 'delivered'));
$delivered_count    = $total_deliveries - $active_count;
$open_bikes         = $fleet['motorcycle']['available'];
$open_vans          = $fleet['van']['available'];
$slots_in_transit   = array_sum(array_column(array_filter($deliveries, fn($d) => $d['status'] !== 'delivered'), 'slots'));
$fleet_available    = array_sum(array_column($fleet, 'available'));
$fleet_total        = array_sum(array_column($fleet, 'total'));

/* Ratings shown on the feedback card. js/dashboard.js re-reads this JSON and
   folds the localStorage entries on top of it, so a submitted star updates
   the average and the distribution bar without a page reload. */
$rating_breakdown = [5 => 38, 4 => 14, 3 => 5, 2 => 2, 1 => 1];
$rating_total     = array_sum($rating_breakdown);
$rating_average   = round(array_sum(array_map(fn($stars, $count) => $stars * $count, array_keys($rating_breakdown), $rating_breakdown)) / $rating_total, 2);
$rating_seed = [
    'breakdown' => $rating_breakdown,
    'average'   => $rating_average,
    'reviews'   => array_values(array_map(
        fn($d) => ['id' => $d['id'], 'rating' => $d['rating'], 'courier' => $d['courier'], 'to' => $d['to']],
        array_filter($deliveries, fn($d) => $d['rating'] !== null)
    )),
];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Tailwind CSS CDN + the shared palette config (copied markup from
         src/includes/header.php so the utility classes used by the sidebar,
         topbar and cards resolve to the same colours as the live portals). -->
    <script src="https://cdn.tailwindcss.com"></script>
    <?php include_once __DIR__ . '/../src/includes/tailwind_config.php'; ?>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/image/logo.png">

    <!-- Shared design layer: base styles, then the token/component layer.
         theme.css must load AFTER style.css so its html.dark rules win. -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/theme.css">

    <!-- Delivery-only additions -->
    <link rel="stylesheet" href="css/dashboard.css">

    <!-- Leaflet (live tracking map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- SAVED-PREFERENCE BOOTSTRAP
         Same synchronous pre-paint script src/includes/header.php runs, reading
         the shared 'crm_customer_prefs' record so the dark scheme and accent
         picked in the customer settings carry into this demo screen. -->
    <script>
    (function () {
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            if (prefs.dark_mode === true) document.documentElement.classList.add('dark');
            var accent = (typeof prefs.accent_color === 'string') ? prefs.accent_color : '';
            if (accent) document.documentElement.setAttribute('data-accent', accent);
            if (prefs.density === 'compact') document.documentElement.setAttribute('data-density', 'compact');
        } catch (e) { /* localStorage unavailable: fall back to defaults */ }
    })();

    /* Toggle the dark scheme and persist it in the same 'crm_customer_prefs'
       record the customer settings page writes (mirrors header.php). */
    window.crmSetDarkMode = function (on) {
        document.documentElement.classList.toggle('dark', on);
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', on ? '#080d1f' : '#f2f4f9');
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            prefs.dark_mode = !!on;
            window.localStorage.setItem('crm_customer_prefs', JSON.stringify(prefs));
        } catch (e) { /* the toggle still applies for this page */ }
        document.dispatchEvent(new CustomEvent('crm:theme-change', { detail: { dark: !!on } }));
    };
    </script>
</head>
<body class="crm-body bg-canvas text-navy-600 font-sans antialiased min-h-screen flex">

<!-- SIDEBAR (static local copy of src/includes/sidebar.php) -->
<?php include_once __DIR__ . '/components/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

    <?php
    // Shared top bar: title + subtitle, search, notification bell, actions.
    $pageTitle    = 'Courier Desk';
    $pageSubtitle = 'Delivery Operations · Metro Manila & Cavite lanes';
    $headerSearch = ['placeholder' => 'Search a waybill, courier, or recipient...', 'id' => 'deliverySearch', 'onkeyup' => 'DeliveryDash.filterTable(this.value)'];
    $headerBell   = [
        'store' => 'newdash_read_notifs',
        'count' => count($notifications),
        'items' => $notifications,
    ];
    include_once __DIR__ . '/components/top_header.php'; ?>

    <!-- DASHBOARD CONTENT BODY -->
    <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">

        <!-- WELCOME BANNER -->
        <section class="bg-gradient-to-r from-brand-blue to-brand-darkblue rounded-2xl p-6 lg:p-8 text-white shadow-lg shadow-blue-600/10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-blue-100 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <?= $open_bikes + $open_vans ?> vehicles free right now
                </span>
                <h1 class="text-2xl lg:text-3xl font-black italic text-white tracking-tight mt-3">Good day, <?= htmlspecialchars($displayName) ?> &#128075;</h1>
                <p class="text-sm text-blue-100 mt-1.5 max-w-md">Book a courier, pick the right vehicle for the load, and follow every route live &mdash; then rate the handover once it lands.</p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <a href="#book-delivery" class="bg-white text-brand-blue hover:bg-blue-50 font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i> Book a Courier
                    </a>
                    <a href="#live-tracking" class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl border border-white/20 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-location-crosshairs text-xs"></i> Open Live Map
                    </a>
                </div>
            </div>
            <div class="hidden sm:block absolute -right-8 -top-8 opacity-20 pointer-events-none select-none">
                <i class="fa-solid fa-truck-fast text-[150px]"></i>
            </div>
        </section>

        <!-- ROW 1: KPI CARDS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            <div class="crm-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="crm-kpi-label">Active Deliveries</span>
                        <p class="crm-kpi-value" id="kpiActive"><?= $active_count ?></p>
                    </div>
                    <span class="crm-kpi-ico"><i class="fa-solid fa-route"></i></span>
                </div>
                <div class="crm-kpi-foot">
                    <span class="crm-delta crm-delta-up"><i class="fa-solid fa-arrow-trend-up text-[10px]"></i> +3</span>
                    vs. yesterday
                </div>
            </div>

            <div class="crm-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="crm-kpi-label">Delivered Today</span>
                        <p class="crm-kpi-value" id="kpiDelivered"><?= $delivered_count ?></p>
                    </div>
                    <span class="crm-kpi-ico"><i class="fa-solid fa-circle-check"></i></span>
                </div>
                <div class="crm-kpi-foot">
                    <span class="crm-badge crm-badge-green"><span class="crm-badge-dot"></span> 96% on time</span>
                </div>
            </div>

            <div class="crm-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="crm-kpi-label">Fleet Available</span>
                        <p class="crm-kpi-value"><span id="kpiFleet"><?= $fleet_available ?></span><span class="text-base font-bold text-navy-300"> / <?= $fleet_total ?></span></p>
                    </div>
                    <span class="crm-kpi-ico"><i class="fa-solid fa-motorcycle"></i></span>
                </div>
                <div class="crm-kpi-foot">
                    <?= $open_bikes ?> bikes &middot; <?= $open_vans ?> vans &middot; 9 other
                </div>
            </div>

            <div class="crm-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="crm-kpi-label">Courier Rating</span>
                        <p class="crm-kpi-value"><span id="kpiRating"><?= number_format($rating_seed['average'], 1) ?></span><span class="text-base font-bold text-navy-300"> / 5</span></p>
                    </div>
                    <span class="crm-kpi-ico"><i class="fa-solid fa-star"></i></span>
                </div>
                <div class="crm-kpi-foot">
                    <span class="dlv-stars" aria-hidden="true">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-solid fa-star <?= $i <= 4 ? 'is-on' : '' ?>"></i>
                        <?php endfor; ?>
                    </span>
                    <span id="kpiRatingCount"><?= $rating_total ?> ratings</span>
                </div>
            </div>
        </section>

        <!-- ROW 2: LIVE TRACKING  —  map + courier telemetry + milestones -->
        <section id="live-tracking" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start scroll-mt-24">

            <!-- MAP CARD -->
            <div class="lg:col-span-8 crm-card overflow-hidden">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Live Tracking</h2>
                        <span class="crm-panel-sub">
                            <span class="crm-badge crm-badge-amber"><span class="crm-badge-dot"></span> Courier moving</span>
                            <span class="ml-1">Waybill <span id="trackWaybill" class="font-semibold text-navy-700 dark:text-slate-200">WB-90412</span> &middot; Makati CDC to BGC, Taguig</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="trackPauseBtn" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]">
                            <i class="fa-solid fa-pause text-[10px]"></i> Pause
                        </button>
                        <button type="button" id="trackRecenterBtn" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[10px]" title="Recenter on courier">
                            <i class="fa-solid fa-crosshairs"></i>
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Map surface -->
                    <div class="relative h-[340px] sm:h-[400px] rounded-xl overflow-hidden border border-line">
                        <div id="trackingMap" class="dlv-map"></div>
                        <!-- Floating ETA chip -->
                        <div class="absolute top-3 left-3 z-[500] crm-card !rounded-xl px-3 py-2 flex items-center gap-2.5">
                            <span class="crm-kpi-ico !w-8 !h-8 !text-[13px]"><i class="fa-solid fa-stopwatch"></i></span>
                            <div class="leading-tight">
                                <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Arrival in</span>
                                <span id="trackEta" class="block text-base font-black" style="color: var(--fg-heading);">14 min</span>
                            </div>
                        </div>
                        <!-- Vehicle chip -->
                        <div class="absolute top-3 right-3 z-[500] crm-badge crm-badge-navy !rounded-xl !px-3 !py-2">
                            <i class="fa-solid fa-motorcycle text-sm"></i>
                            <span class="text-[11px]">Motorcycle &middot; J. Ramos</span>
                        </div>
                    </div>

                    <!-- Progress rail -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider" style="color: var(--fg-muted);">
                            <span>Route progress</span>
                            <span><span id="trackProgressPct">78</span>% &middot; <span id="trackDistance">1.9 km</span> left</span>
                        </div>
                        <div class="dlv-bar !h-2">
                            <div id="trackProgressBar" class="dlv-bar-fill !bg-brand-blue" style="width: 78%;"></div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- COURIER TELEMETRY -->
            <div class="lg:col-span-4 space-y-6">
                <div class="crm-card">
                    <div class="crm-panel-head">
                        <div>
                            <h2 class="crm-panel-title">Assigned Courier</h2>
                            <span class="crm-panel-sub">Live from the dispatch board</span>
                        </div>
                        <span class="crm-badge crm-badge-green"><span class="crm-badge-dot"></span> On route</span>
                    </div>
                    <div class="crm-panel-body space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="crm-avatar !w-11 !h-11 !rounded-xl !text-sm">JR</span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate" style="color: var(--fg-heading);">J. Ramos</p>
                                <span class="crm-panel-sub">Motorcycle &middot; NKA-4471 &middot; 4 yrs on the lane</span>
                            </div>
                            <div class="ml-auto text-right">
                                <span class="dlv-stars" aria-hidden="true">
                                    <i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i>
                                </span>
                                <span class="block text-[10px] font-bold" style="color: var(--fg-muted);">4.9 &middot; 312 drops</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-xl border border-line p-2.5" style="background: var(--surface-muted);">
                                <span class="block text-[9px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Speed</span>
                                <span id="trackSpeed" class="block text-sm font-black" style="color: var(--fg-heading);">28</span>
                                <span class="block text-[9px] font-bold" style="color: var(--fg-muted);">km/h</span>
                            </div>
                            <div class="rounded-xl border border-line p-2.5" style="background: var(--surface-muted);">
                                <span class="block text-[9px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Stop</span>
                                <span id="trackStop" class="block text-sm font-black" style="color: var(--fg-heading);">1</span>
                                <span class="block text-[9px] font-bold" style="color: var(--fg-muted);">of 3</span>
                            </div>
                            <div class="rounded-xl border border-line p-2.5" style="background: var(--surface-muted);">
                                <span class="block text-[9px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Parcels</span>
                                <span class="block text-sm font-black" style="color: var(--fg-heading);">1</span>
                                <span class="block text-[9px] font-bold" style="color: var(--fg-muted);">2.1 kg</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="crm-btn crm-btn-primary flex-1 !h-9 !text-xs">
                                <i class="fa-solid fa-phone text-[10px]"></i> Call courier
                            </button>
                            <button type="button" class="crm-btn crm-btn-ghost !h-9 !text-xs" title="Message courier">
                                <i class="fa-solid fa-comment-dots text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>


                <!-- MILESTONE TIMELINE -->
                <div class="crm-card">
                    <div class="crm-panel-head">
                        <div>
                            <h2 class="crm-panel-title">Milestones</h2>
                            <span class="crm-panel-sub">Advances as the courier moves</span>
                        </div>
                    </div>
                    <div class="crm-panel-body">
                        <ol id="trackTimeline" class="space-y-4">
                            <?php
                            $milestones = [
                                ['label' => 'Order accepted',          'time' => '09:31 AM',      'state' => 'done'],
                                ['label' => 'Picked up at Makati CDC', 'time' => '09:42 AM',      'state' => 'done'],
                                ['label' => 'In transit to BGC',       'time' => '09:55 AM',      'state' => 'active'],
                                ['label' => 'Out for final handover',  'time' => 'Est. 10:38 AM', 'state' => 'todo'],
                                ['label' => 'Delivered + rate courier', 'time' => 'Est. 10:46 AM', 'state' => 'todo'],
                            ];
                            foreach ($milestones as $ms_idx => $ms):
                                $ms_last = ($ms_idx === count($milestones) - 1);
                            ?>
                                <li class="dlv-ms-item flex items-start gap-3" data-ms-state="<?= $ms['state'] ?>">
                                    <span class="dlv-ms-dot <?= $ms['state'] === 'done' ? 'is-done' : '' ?> <?= $ms['state'] === 'active' ? 'is-active' : '' ?>"></span>
                                    <?php if (!$ms_last): ?>
                                        <span class="dlv-ms-line <?= $ms['state'] === 'done' ? 'is-done' : '' ?>"></span>
                                    <?php endif; ?>
                                    <span class="min-w-0 flex-1 pb-1">
                                        <span class="block text-xs font-bold" style="color: var(--fg-heading);"><?= $ms['label'] ?></span>
                                        <span class="block text-[11px] font-semibold" style="color: var(--fg-muted);"><?= $ms['time'] ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- ROW 3: COURIER BOOKING  +  VEHICLE VARIETY -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- BOOKING FORM -->
            <div id="book-delivery" class="lg:col-span-7 crm-card scroll-mt-24">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Book a Courier</h2>
                        <span class="crm-panel-sub">Pick the vehicle, we dispatch the nearest free driver</span>
                    </div>
                    <span class="crm-badge crm-badge-blue"><i class="fa-solid fa-flask text-[9px]"></i> Demo only</span>
                </div>

                <div class="crm-panel-body">
                    <!-- BACKEND WIRING: this form posts nowhere. js/dashboard.js validates it,
                         stores the booking in localStorage under 'newdash_bookings' and
                         prepends a row to the deliveries table below. Point the submit
                         handler at POST /api/v1/portal/shipments when that endpoint exists. -->
                    <form id="bookingForm" novalidate class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="crm-label" for="bookSender">Pickup from</label>
                                <input type="text" id="bookSender" name="sender" class="crm-input" placeholder="e.g. Makati CDC" autocomplete="off">
                                <p class="dlv-field-error" data-error-for="bookSender">Tell us where to collect.</p>
                            </div>
                            <div>
                                <label class="crm-label" for="bookRecipient">Deliver to</label>
                                <input type="text" id="bookRecipient" name="recipient_address" class="crm-input" placeholder="e.g. BGC, Taguig" autocomplete="off">
                                <p class="dlv-field-error" data-error-for="bookRecipient">Destination is required.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="crm-label" for="bookName">Recipient name</label>
                                <input type="text" id="bookName" name="recipient_name" class="crm-input" placeholder="e.g. N. Alvarez" autocomplete="off">
                                <p class="dlv-field-error" data-error-for="bookName">Who is receiving this?</p>
                            </div>
                            <div>
                                <label class="crm-label" for="bookContact">Contact number</label>
                                <input type="tel" id="bookContact" name="contact" class="crm-input" placeholder="09XX XXX XXXX" autocomplete="off" inputmode="tel">
                                <p class="dlv-field-error" data-error-for="bookContact">Enter at least 7 digits.</p>
                            </div>
                        </div>

                        <!-- Vehicle picker: radio cards, so the variety is the decision -->
                        <div>
                            <span class="crm-label">Vehicle</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" role="radiogroup" aria-label="Vehicle type">
                                <?php foreach ($vehicle_meta as $v_key => $v): ?>
                                    <label class="dlv-vehicle-opt" data-vehicle="<?= htmlspecialchars($v_key) ?>">
                                        <input type="radio" name="vehicle" value="<?= htmlspecialchars($v_key) ?>" class="sr-only" <?= $v_key === 'motorcycle' ? 'checked' : '' ?>>
                                        <i class="fa-solid <?= htmlspecialchars($v['icon']) ?> text-base"></i>
                                        <span class="block text-[11px] font-bold leading-tight mt-1"><?= htmlspecialchars($v['label']) ?></span>
                                        <span class="block text-[10px] font-semibold opacity-70"><?= htmlspecialchars($v['capacity']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="crm-label" for="bookService">Service level</label>
                                <select id="bookService" name="service" class="crm-select">
                                    <option value="Express">Express (under 2 hrs)</option>
                                    <option value="Same-day" selected>Same-day</option>
                                    <option value="Scheduled">Scheduled</option>
                                </select>
                            </div>
                            <div>
                                <label class="crm-label" for="bookWeight">Weight (kg)</label>
                                <input type="number" id="bookWeight" name="weight" class="crm-input" min="0.1" step="0.1" value="2.0" inputmode="decimal">
                                <p class="dlv-field-error" data-error-for="bookWeight">Enter a weight above 0.</p>
                            </div>
                            <div>
                                <label class="crm-label" for="bookSlots">Parcels</label>
                                <input type="number" id="bookSlots" name="slots" class="crm-input" min="1" step="1" value="1" inputmode="numeric">
                                <p class="dlv-field-error" data-error-for="bookSlots">At least one parcel.</p>
                            </div>
                        </div>

                        <div>
                            <label class="crm-label" for="bookNote">Handling note (optional)</label>
                            <textarea id="bookNote" name="note" rows="2" class="crm-textarea resize-y" placeholder="Fragile, deliver after 5 PM, gate code 4471..."></textarea>
                        </div>

                        <!-- Live quote -->
                        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-line p-4" style="background: var(--surface-muted);">
                            <div class="flex items-center gap-3">
                                <span class="crm-kpi-ico"><i class="fa-solid fa-receipt"></i></span>
                                <div class="leading-tight">
                                    <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Estimated fare</span>
                                    <span id="bookQuote" class="block text-xl font-black" style="color: var(--fg-heading);">₱79.00</span>
                                </div>
                            </div>
                            <div class="text-right leading-tight">
                                <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Pickup window</span>
                                <span id="bookWindow" class="block text-sm font-bold" style="color: var(--fg-heading);">within 25 min</span>
                            </div>
                            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                                <button type="reset" class="crm-btn crm-btn-ghost !h-10 !text-xs">Clear</button>
                                <button type="submit" class="crm-btn crm-btn-primary !h-10 !px-5 !text-xs">
                                    <i class="fa-solid fa-bolt text-[10px]"></i> Book courier
                                </button>
                            </div>
                        </div>
                        <p class="text-[11px]" style="color: var(--fg-muted);">
                            <i class="fa-solid fa-circle-info text-[10px]"></i>
                            Nothing leaves this browser &mdash; the booking lands in the table below and in localStorage only.
                        </p>
                    </form>
                </div>
            </div>

            <!-- VEHICLE VARIETY / FLEET -->
            <div id="fleet" class="lg:col-span-5 crm-card scroll-mt-24">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Vehicle Variety</h2>
                        <span class="crm-panel-sub">What the dispatch board can release right now</span>
                    </div>
                    <span class="crm-badge crm-badge-navy"><span class="crm-badge-dot"></span> <?= $fleet_available ?> / <?= $fleet_total ?> free</span>
                </div>
                <div class="crm-panel-body space-y-3">
                    <?php foreach ($fleet as $f_key => $f): ?>
                        <?php
                        $f_pct = $f['total'] > 0 ? round(($f['available'] / $f['total']) * 100) : 0;
                        $f_low = $f['available'] <= 3;
                        ?>
                        <div class="dlv-vehicle" data-vehicle="<?= htmlspecialchars($f_key) ?>">
                            <span class="dlv-vehicle-ico"><i class="fa-solid <?= htmlspecialchars($f['icon']) ?>"></i></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($f['label']) ?></span>
                                    <span class="text-[11px] font-extrabold whitespace-nowrap" style="color: var(--fg-muted);">from ₱<?= number_format($f['rate']) ?></span>
                                </div>
                                <span class="block text-[11px] leading-snug mt-0.5" style="color: var(--fg-muted);"><?= htmlspecialchars($f['note']) ?></span>
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="dlv-bar flex-1">
                                        <div class="dlv-bar-fill <?= $f_low ? '!bg-amber-400' : '!bg-emerald-500' ?>" style="width: <?= $f_pct ?>%;"></div>
                                    </div>
                                    <span class="text-[10px] font-extrabold whitespace-nowrap <?= $f_low ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' ?>">
                                        <?= $f['available'] ?> of <?= $f['total'] ?>
                                    </span>
                                </div>
                            </div>
                            <button type="button"
                                    class="crm-btn crm-btn-ghost !h-8 !px-2.5 !text-[11px] shrink-0"
                                    data-book-vehicle="<?= htmlspecialchars($f_key) ?>"
                                    title="Preselect <?= htmlspecialchars($f['label']) ?> in the booking form">
                                <i class="fa-solid fa-arrow-pointer text-[9px]"></i>
                                <span class="hidden xl:inline">Use</span>
                            </button>
                        </div>
                    <?php endforeach; ?>

                    <div class="pt-1 flex flex-wrap items-center gap-2">
                        <span class="crm-badge crm-badge-slate"><i class="fa-solid fa-temperature-low text-[9px]"></i> Cold chain on request</span>
                        <span class="crm-badge crm-badge-slate"><i class="fa-solid fa-boxes-stacked text-[9px]"></i> Pallet jack with van</span>
                        <span class="crm-badge crm-badge-slate"><i class="fa-solid fa-shield-halved text-[9px]"></i> Insured to ₱25k</span>
                    </div>
                </div>
            </div>
        </section>


        <!-- ROW 4: ACTIVE DELIVERIES TABLE -->
        <section id="active-deliveries" class="scroll-mt-24">
            <div class="crm-card overflow-hidden">
                <div class="crm-panel-head flex-wrap">
                    <div>
                        <h2 class="crm-panel-title">Deliveries</h2>
                        <span class="crm-panel-sub"><span id="deliveryVisibleCount"><?= $total_deliveries ?></span> of <?= $total_deliveries ?> shown &middot; <?= $slots_in_transit ?> parcels moving</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Vehicle filter -->
                        <select id="vehicleFilter" class="crm-select !h-8 !w-auto !text-[11px] !pl-2.5 !pr-7" aria-label="Filter by vehicle">
                            <option value="all">All vehicles</option>
                            <?php foreach ($vehicle_meta as $v_key => $v): ?>
                                <option value="<?= htmlspecialchars($v_key) ?>"><?= htmlspecialchars($v['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Status pills -->
                        <div id="statusPills" class="flex flex-wrap items-center gap-1.5">
                            <button type="button" class="crm-pill is-active" data-status="all">All</button>
                            <button type="button" class="crm-pill" data-status="scheduled">Scheduled</button>
                            <button type="button" class="crm-pill" data-status="in_transit">In transit</button>
                            <button type="button" class="crm-pill" data-status="out_for_delivery">Out for delivery</button>
                            <button type="button" class="crm-pill" data-status="delivered">Delivered</button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto crm-scroll">
                    <table class="crm-table" id="deliveriesTable">
                        <thead>
                            <tr>
                                <th>Waybill</th>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Courier</th>
                                <th>Status</th>
                                <th>ETA</th>
                                <th class="text-right">Load</th>
                                <th class="text-right">Feedback</th>
                            </tr>
                        </thead>
                        <tbody id="deliveriesBody">
                            <?php foreach ($deliveries as $d): ?>
                                <?php
                                $d_badge   = $status_badges[$d['status']] ?? ['badge' => 'crm-badge-slate', 'label' => ucfirst($d['status'])];
                                $d_vehicle = $vehicle_meta[$d['vehicle']] ?? ['label' => ucfirst($d['vehicle']), 'icon' => 'fa-truck'];
                                ?>
                                <tr class="dlv-row"
                                    data-status="<?= htmlspecialchars($d['status']) ?>"
                                    data-vehicle="<?= htmlspecialchars($d['vehicle']) ?>"
                                    data-waybill="<?= htmlspecialchars($d['id']) ?>"
                                    data-search="<?= htmlspecialchars(strtolower($d['id'] . ' ' . $d['from'] . ' ' . $d['to'] . ' ' . $d['recipient'] . ' ' . $d['courier'])) ?>">
                                    <td>
                                        <span class="cell-strong font-mono !text-[11px]"><?= htmlspecialchars($d['id']) ?></span>
                                        <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= htmlspecialchars($d['service']) ?> &middot; <?= htmlspecialchars($d['picked']) ?></span>
                                    </td>
                                    <td>
                                        <span class="block text-xs font-semibold" style="color: var(--fg-heading);"><?= htmlspecialchars($d['to']) ?></span>
                                        <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">from <?= htmlspecialchars($d['from']) ?> &middot; <?= htmlspecialchars($d['distance']) ?></span>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center gap-2 text-xs font-semibold">
                                            <i class="fa-solid <?= htmlspecialchars($d_vehicle['icon']) ?> text-[11px]" style="color: var(--navy-400);"></i>
                                            <?= htmlspecialchars($d_vehicle['label']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="block text-xs font-semibold" style="color: var(--fg-heading);"><?= htmlspecialchars($d['courier']) ?></span>
                                        <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">to <?= htmlspecialchars($d['recipient']) ?></span>
                                    </td>
                                    <td>
                                        <span class="crm-badge <?= $d_badge['badge'] ?>"><span class="crm-badge-dot"></span> <?= htmlspecialchars($d_badge['label']) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-bold whitespace-nowrap" style="color: var(--fg-heading);"><?= htmlspecialchars($d['eta']) ?></span>
                                        <div class="dlv-bar w-24 mt-1.5">
                                            <div class="dlv-bar-fill !bg-brand-blue" style="width: <?= (int) $d['progress'] ?>%;"></div>
                                        </div>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <span class="text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($d['weight']) ?></span>
                                        <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= (int) $d['slots'] ?> parcel<?= $d['slots'] == 1 ? '' : 's' ?></span>
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <?php if ($d['status'] === 'delivered'): ?>
                                            <?php if ($d['rating'] !== null): ?>
                                                <span class="dlv-stars" title="Rated <?= (int) $d['rating'] ?> out of 5" data-rating-for="<?= htmlspecialchars($d['id']) ?>">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa-solid fa-star <?= $i <= (int) $d['rating'] ? 'is-on' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </span>
                                                <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Rated</span>
                                            <?php else: ?>
                                                <button type="button"
                                                        class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]"
                                                        data-rate="<?= htmlspecialchars($d['id']) ?>"
                                                        data-courier="<?= htmlspecialchars($d['courier']) ?>"
                                                        data-route="<?= htmlspecialchars($d['from'] . ' to ' . $d['to']) ?>">
                                                    <i class="fa-regular fa-star text-[10px]"></i> Rate courier
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button type="button" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]" data-track="<?= htmlspecialchars($d['id']) ?>">
                                                <i class="fa-solid fa-location-crosshairs text-[10px]"></i> Track
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="deliveriesEmpty" class="crm-empty" style="display: none;">
                    <span class="crm-empty-ico"><i class="fa-solid fa-filter"></i></span>
                    <p class="crm-empty-title">No delivery matches those filters</p>
                    <p class="crm-empty-sub">Clear the search box or switch the status pill back to &ldquo;All&rdquo;.</p>
                    <button type="button" id="clearFiltersBtn" class="crm-btn crm-btn-ghost !h-9 !text-xs mt-2">Reset filters</button>
                </div>
            </div>
        </section>



        <!-- ROW 5: STAR RATING / FEEDBACK  +  QUICK ACTIONS -->
        <section id="feedback" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start scroll-mt-24">

            <!-- RATING SUMMARY -->
            <div class="lg:col-span-8 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Ratings &amp; Reviews</h2>
                        <span class="crm-panel-sub">What recipients said about recent handovers</span>
                    </div>
                    <button type="button" id="openRatingBtn" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
                        <i class="fa-solid fa-star text-[10px]"></i> Rate a delivery
                    </button>
                </div>

                <div class="crm-panel-body grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Score -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center text-center rounded-xl border border-line p-5" style="background: var(--surface-muted);">
                        <span id="ratingAverage" class="text-5xl font-black tracking-tight" style="color: var(--fg-heading);"><?= number_format($rating_average, 1) ?></span>
                        <span class="dlv-stars mt-2 !text-base" id="ratingAverageStars" aria-hidden="true">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star <?= $i <= round($rating_average) ? 'is-on' : '' ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="block text-[11px] font-bold mt-2" style="color: var(--fg-muted);">
                            Based on <span id="ratingTotalCount"><?= $rating_total ?></span> ratings
                        </span>
                        <span class="crm-badge crm-badge-green mt-3"><span class="crm-badge-dot"></span> <span id="ratingPositive"><?= round((($rating_breakdown[4] ?? 0) + ($rating_breakdown[5] ?? 0)) / max($rating_total, 1) * 100) ?></span>% positive</span>
                    </div>

                    <!-- Distribution -->
                    <div class="md:col-span-8 space-y-2.5 self-center">
                        <?php foreach ([5, 4, 3, 2, 1] as $stars): ?>
                            <?php $cnt = $rating_breakdown[$stars] ?? 0; $pct = $rating_total ? round($cnt / $rating_total * 100) : 0; ?>
                            <div class="flex items-center gap-3" data-dist-row="<?= $stars ?>">
                                <span class="w-10 shrink-0 text-[11px] font-bold text-right" style="color: var(--fg-heading);"><?= $stars ?> star<?= $stars > 1 ? 's' : '' ?></span>
                                <div class="dlv-bar flex-1">
                                    <div class="dlv-bar-fill" data-dist-fill="<?= $stars ?>" style="width: <?= $pct ?>%;"></div>
                                </div>
                                <span class="w-9 shrink-0 text-[11px] font-extrabold text-right" style="color: var(--fg-muted);" data-dist-count="<?= $stars ?>"><?= $cnt ?></span>
                            </div>
                        <?php endforeach; ?>

                        <div class="!mt-5 pt-4 space-y-3" style="border-top: 1px solid var(--line);">
                            <span class="crm-section-label">Recent comments</span>
                            <ul id="reviewList" class="space-y-3">
                                <?php foreach ($rating_seed['reviews'] as $rv): ?>
                                    <li class="flex items-start gap-3" data-review-for="<?= htmlspecialchars($rv['id']) ?>">
                                        <span class="crm-avatar !w-8 !h-8 !rounded-lg !text-[10px]"><?= htmlspecialchars(substr($rv['courier'], 0, 2)) ?></span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs font-bold truncate" style="color: var(--fg-heading);"><?= htmlspecialchars($rv['courier']) ?> &middot; <?= htmlspecialchars($rv['id']) ?></span>
                                                <span class="dlv-stars shrink-0" aria-label="<?= (int) $rv['rating'] ?> out of 5">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa-solid fa-star <?= $i <= (int) $rv['rating'] ? 'is-on' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </span>
                                            </div>
                                            <p class="text-[11px] leading-snug mt-0.5" style="color: var(--fg-body);">
                                                Handover at <?= htmlspecialchars($rv['to']) ?> went smoothly &mdash; <?= (int) $rv['rating'] >= 5 ? 'courier was early and careful with the parcels.' : 'delivery completed as scheduled.' ?>
                                            </p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="lg:col-span-4 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Quick Actions</h2>
                        <span class="crm-panel-sub">Everything the desk does most</span>
                    </div>
                </div>
                <div class="crm-panel-body grid grid-cols-2 gap-3">
                    <a href="#book-delivery" class="dlv-vehicle !flex-col !items-start !gap-2">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-bolt"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Book courier</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Instant dispatch</span>
                        </span>
                    </a>
                    <a href="#live-tracking" class="dlv-vehicle !flex-col !items-start !gap-2">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-map-location-dot"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Live map</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= $active_count ?> routes moving</span>
                        </span>
                    </a>
                    <a href="#fleet" class="dlv-vehicle !flex-col !items-start !gap-2">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-warehouse"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Fleet board</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= $fleet_available ?> vehicles free</span>
                        </span>
                    </a>
                    <button type="button" id="openRatingBtnAlt" class="dlv-vehicle !flex-col !items-start !gap-2 text-left">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-star"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Rate a drop</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Leave feedback</span>
                        </span>
                    </button>
                </div>
                <div class="px-5 pb-5">
                    <div class="rounded-xl border p-4" style="border-color: var(--line); background: var(--brand-soft);">
                        <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Saved on this device</span>
                        <p class="text-xs font-semibold mt-1 leading-snug" style="color: var(--fg-body);">
                            <span id="localBookingsCount">0</span> booking(s) and <span id="localRatingsCount">0</span> rating(s) you submitted.
                            <button type="button" id="clearLocalBtn" class="font-bold underline" style="color: var(--danger);">Clear</button>
                        </p>
                    </div>
                </div>
            </div>
        </section>



    </div><!-- /DASHBOARD CONTENT BODY -->
</main><!-- /MAIN CONTENT AREA -->

<!-- RATING MODAL (star picker + feedback, localStorage only) -->
<?php include_once __DIR__ . '/components/rating_modal.php'; ?>

<!-- LEGAL MODALS are intentionally not included here: components/legal_modals.php
     renders #legal-privacy / #legal-terms, which only open through the
     data-legal-open triggers wired in js/auth.js, and nothing on this screen
     links to them. The file stays in place for register.php. -->

<!-- SHARED BEHAVIOUR: sidebar toggle (defines toggleSidebar, used by the
     topbar's mobile button) and the notification bell dropdown driven by
     #notifBellWrap in components/top_header.php. Both files guard on element
     presence, so they are inert where the markup is absent.
     logout.js / footer.js are deliberately NOT loaded: this demo has no
     session, #logoutModal is not rendered, and footer.js's privacy/terms
     helpers target #privacyModal/#termsModal, not legal_modals.php.
     NOTE: there is no assets/js/main.js in this project. -->
<script src="../assets/js/dashboard.js" defer></script>
<script src="../assets/js/customer/notification_bell.js" defer></script>

<!-- SEED + BEHAVIOUR
     The rating seed is inlined as JSON so js/dashboard.js can fold saved
     localStorage ratings on top of the PHP-rendered averages without a fetch. -->
<script id="ratingSeed" type="application/json"><?= json_encode($rating_seed, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script src="js/dashboard.js" defer></script>

</body>
</html>

