<?php
/* ==========================================================================
    OVERVIEW  —  views/overview.php  (OverviewModule::render())
    --------------------------------------------------------------------------
    Carved 1:1 out of the old single-page dashboard.php: the welcome banner,
    the four KPI cards, the compact "moving right now" table (the three
    busiest open jobs via DemoData records, minus the Feedback column) and the
    quick-actions column that used to sit beside the feedback card.

    Reads (all from OverviewModule::prepare()):
      $displayName   string  Greeting in the banner.
      $kpis          array   DemoData::kpis() - active / delivered-today /
                             fleet-available figures.
      $freeVehicles  int     openBikes + openVans, the "x free right now" chip.
      $moving        array   up to 3 open deliveries ordered by progress desc.

    Row markup comes from partials/delivery_row.php with $row_compact = true
    (its own documented mode) so a row never renders differently here than it
    does on deliveries.php. The quick-actions panel is included from
    views/quick_actions.php. Anchor jumps became real page links - Book a
    Courier -> book.php, Open Live Map -> tracking.php.
    -------------------------------------------------------------------------- */
$row_compact = true;
?>

        <!-- WELCOME BANNER -->
        <section class="bg-gradient-to-r from-brand-blue to-brand-darkblue rounded-2xl p-6 lg:p-8 text-white shadow-lg shadow-blue-600/10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-blue-100 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <?= $freeVehicles ?> vehicles free right now
                </span>
                <h1 class="text-2xl lg:text-3xl font-black italic text-white tracking-tight mt-3">Good day, <?= htmlspecialchars($displayName) ?> &#128075;</h1>
                <p class="text-sm text-blue-100 mt-1.5 max-w-md">Book a courier, pick the right vehicle for the load, and follow every route live &mdash; then rate the handover once it lands.</p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <a href="book.php" class="bg-white text-brand-blue hover:bg-blue-50 font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i> Book a Courier
                    </a>
                    <a href="tracking.php" class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl border border-white/20 transition-colors flex items-center gap-2">
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
                        <p class="crm-kpi-value" id="kpiActive"><?= (int) $kpis['active'] ?></p>
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
                        <p class="crm-kpi-value" id="kpiDelivered"><?= (int) $kpis['delivered'] ?></p>
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
                        <p class="crm-kpi-value"><span id="kpiFleet"><?= (int) $kpis['fleetAvailable'] ?></span><span class="text-base font-bold text-navy-300"> / <?= (int) $kpis['fleetTotal'] ?></span></p>
                    </div>
                    <span class="crm-kpi-ico"><i class="fa-solid fa-motorcycle"></i></span>
                </div>
                <div class="crm-kpi-foot">
                    <?= (int) $kpis['openBikes'] ?> bikes &middot; <?= (int) $kpis['openVans'] ?> vans &middot; 9 other
                </div>
            </div>

            <div class="crm-kpi">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="crm-kpi-label">Courier Rating</span>
                        <p class="crm-kpi-value"><span id="kpiRating"><?= number_format($kpis['ratingAverage'], 1) ?></span><span class="text-base font-bold text-navy-300"> / 5</span></p>
                    </div>
                    <span class="crm-kpi-ico"><i class="fa-solid fa-star"></i></span>
                </div>
                <div class="crm-kpi-foot">
                    <span class="dlv-stars" aria-hidden="true">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-solid fa-star <?= $i <= round($kpis['ratingAverage']) ? 'is-on' : '' ?>"></i>
                        <?php endfor; ?>
                    </span>
                    <span id="kpiRatingCount"><?= (int) $kpis['ratingTotal'] ?> ratings</span>
                </div>
            </div>
        </section>

        <!-- MOVING RIGHT NOW  —  the three busiest open jobs -->
        <section id="active-deliveries" class="crm-card overflow-hidden scroll-mt-24">
            <div class="crm-panel-head flex-wrap">
                <div>
                    <h2 class="crm-panel-title">Moving Right Now</h2>
                    <span class="crm-panel-sub">The <?= count($moving) ?> busiest open routes &middot; <?= (int) $kpis['slotsMoving'] ?> parcels in play</span>
                </div>
                <a href="deliveries.php" class="crm-btn crm-btn-ghost !h-9 !text-xs">
                    <i class="fa-solid fa-list-check text-[10px]"></i> All deliveries
                </a>
            </div>

            <div class="overflow-x-auto crm-scroll">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th>Waybill</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Courier</th>
                            <th>Status</th>
                            <th>ETA</th>
                            <th class="text-right">Load</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($moving as $d): ?>
                            <?php include __DIR__ . '/partials/delivery_row.php'; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- QUICK ACTIONS  (shared panel - views/quick_actions.php) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <?php include __DIR__ . '/quick_actions.php'; ?>
        </section>

