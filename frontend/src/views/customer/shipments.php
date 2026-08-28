<?php
$page_title = "Shipments · Priority Handling Logistics";
$activePage = 'shipments';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';

/*
 * Shipment data source.
 * In production this would come from make_api_request('/api/v1/portal/shipments', 'GET').
 * Kept as a local array here so the dashboard renders with realistic demo data
 * and the same structure can be swapped for a live feed later.
 */
$shipments = [
    ['waybill' => 'PH-WB-208841', 'type' => '40ft container · Reefer',    'route' => 'Manila → Cebu',            'carrier' => 'Trans-Pacific Lines', 'status' => 'in-transit', 'eta' => 'Jul 29, 14:00'],
    ['waybill' => 'PH-WB-208835', 'type' => '20ft container · Dry van',   'route' => 'Cebu → Manila',            'carrier' => '2GO Freight',         'status' => 'customs',   'eta' => 'Jul 30, 09:00'],
    ['waybill' => 'PH-WB-208812', 'type' => 'LCL · Break-bulk',           'route' => 'Manila → Davao',           'carrier' => 'Sulpicio Lines',      'status' => 'delivered', 'eta' => 'Jul 25, 11:20'],
    ['waybill' => 'PH-WB-208712', 'type' => '40ft container · Dry van',   'route' => 'Manila → Iloilo',          'carrier' => 'Sulpicio Lines',      'status' => 'delayed',   'eta' => 'Jul 27, 18:00'],
    ['waybill' => 'PH-WB-208699', 'type' => '20ft container · Reefer',    'route' => 'Manila → Cagayan de Oro',  'carrier' => '2GO Freight',         'status' => 'in-transit', 'eta' => 'Aug 02, 07:30'],
    ['waybill' => 'PH-WB-208650', 'type' => 'FCL · Dry van',              'route' => 'Manila → Bacolod',         'carrier' => 'Trans-Pacific Lines', 'status' => 'in-transit', 'eta' => 'Jul 31, 10:00'],
];

// Derive the status counts used by the KPI strip + doughnut chart.
$counts = ['all' => count($shipments), 'in-transit' => 0, 'customs' => 0, 'delayed' => 0, 'delivered' => 0];
foreach ($shipments as $s) {
    if (isset($counts[$s['status']])) $counts[$s['status']]++;
}

// Sort upcoming (non-delivered) by ETA for the "Upcoming Deliveries" timeline.
$upcoming = array_filter($shipments, fn($s) => $s['status'] !== 'delivered');
usort($upcoming, fn($a, $b) => strtotime($a['eta']) <=> strtotime($b['eta']));

// Shared status → badge styling helper (soft pastel, matches dashboard.css).
function shipmentBadge($status) {
    switch ($status) {
        case 'in-transit': return ['label' => 'In Transit', 'class' => 'bg-blue-50 text-blue-700 border-blue-200',   'dot' => 'bg-blue-500'];
        case 'customs':    return ['label' => 'Customs',    'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-500'];
        case 'delayed':    return ['label' => 'Delayed',    'class' => 'bg-rose-50 text-rose-700 border-rose-200',    'dot' => 'bg-rose-500'];
        case 'delivered':  return ['label' => 'Delivered',  'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500'];
        default:           return ['label' => ucfirst($status), 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'];
    }
}
function carrierInitials($name) {
    $parts = preg_split('/\s+/', $name);
    return strtoupper(substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}
?>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button onclick="toggleSidebar()" class="sm:hidden text-slate-600 hover:text-slate-900 p-1.5 shrink-0">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="min-w-0">
                    <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Shipments</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Waybill manifest &amp; live tracking · Robles Cargo Corp.</p>
                </div>
            </div>

            <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="shipmentSearchInput" onkeyup="searchShipmentsTable()" placeholder="Track a waybill, carrier, or route..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors shrink-0">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">
                    Help Desk <i class="fa-solid fa-headset text-xs"></i>
                </button>
                <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Book Shipment
                </button>
            </div>
        </header>

        <!-- SHIPMENTS DASHBOARD BODY -->
        <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">

            <!-- ATTENTION BANNER (only when something is delayed) -->
            <?php if ($counts['delayed'] > 0): ?>
            <section class="flex items-center gap-4 bg-rose-50 border border-rose-200 rounded-2xl p-4 lg:px-6 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-rose-800"><?= $counts['delayed'] ?> shipment<?= $counts['delayed'] > 1 ? 's' : '' ?> need<?= $counts['delayed'] > 1 ? '' : 's' ?> your attention</p>
                    <p class="text-xs text-rose-600/80 mt-0.5">Delayed waybills may miss their delivery window. Contact support or rebook to avoid SLA breaches.</p>
                </div>
                <button onclick="filterShipments('delayed', document.querySelector('.filter-tab[data-status-key=\'delayed\']'))" class="text-xs font-semibold text-rose-700 hover:text-rose-900 bg-white border border-rose-200 px-3.5 py-2 rounded-xl transition-colors shrink-0 hidden sm:block">
                    View delayed &rarr;
                </button>
            </section>
            <?php endif; ?>

            <!-- ROW 1: KPI METRICS -->
            <section class="grid grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-5">
                <?php
                $kpis = [
                    ['key' => 'all',        'label' => 'Total Shipments',  'icon' => 'fa-boxes-stacked', 'chip' => 'bg-blue-50 text-brand-blue',     'note' => 'All waybills',       'noteClass' => 'text-slate-500'],
                    ['key' => 'in-transit', 'label' => 'In Transit',       'icon' => 'fa-truck-fast',     'chip' => 'bg-blue-50 text-blue-600',       'note' => 'On the move',       'noteClass' => 'text-blue-600 font-semibold'],
                    ['key' => 'customs',    'label' => 'Customs',          'icon' => 'fa-file-invoice',   'chip' => 'bg-amber-50 text-amber-600',     'note' => 'Clearance pending', 'noteClass' => 'text-amber-600 font-semibold'],
                    ['key' => 'delayed',    'label' => 'Delayed',          'icon' => 'fa-triangle-exclamation', 'chip' => 'bg-rose-50 text-rose-500', 'note' => 'Needs attention',   'noteClass' => 'text-rose-600 font-semibold'],
                    ['key' => 'delivered',  'label' => 'Delivered (30d)',  'icon' => 'fa-circle-check',   'chip' => 'bg-emerald-50 text-emerald-600', 'note' => 'Completed',         'noteClass' => 'text-emerald-600 font-semibold'],
                ];
                foreach ($kpis as $k): ?>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-medium text-slate-500"><?= $k['label'] ?></span>
                        <div class="p-2 rounded-xl <?= $k['chip'] ?>">
                            <i class="fa-solid <?= $k['icon'] ?> text-sm"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-extrabold text-slate-900"><?= $counts[$k['key']] ?></p>
                        <p class="text-xs <?= $k['noteClass'] ?> mt-2"><?= $k['note'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </section>

            <!-- ROW 2: MAIN GRID -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- LEFT COLUMN: STATUS CHART + FILTERED TABLE -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- STATUS DISTRIBUTION (Chart.js doughnut — already loaded globally) -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Status Distribution</h2>
                                <p class="text-xs text-slate-400 mt-0.5">Live snapshot across all waybills</p>
                            </div>
                        </div>
                        <div class="relative h-56 flex items-center justify-center">
                            <canvas id="shipmentStatusChart"></canvas>
                        </div>
                    </div>


                    <!-- SHIPMENT MANIFEST TABLE -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 flex flex-wrap justify-between items-start gap-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Shipment Manifest</h2>
                                <p class="text-xs text-slate-400 mt-0.5">Full waybill history for Robles Cargo Corp.</p>
                            </div>
                            <button onclick="exportShipmentsCSV()" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue hover:underline flex items-center gap-1.5 bg-blue-50 border border-blue-100 px-3 py-2 rounded-xl transition-colors">
                                <i class="fa-solid fa-file-export"></i> Export CSV
                            </button>
                        </div>

                        <!-- STATUS FILTER TABS -->
                        <div class="px-6 pb-5">
                            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100 rounded-2xl w-fit text-xs font-semibold">
                                <button data-status-key="all" onclick="filterShipments('all', this)" class="filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm transition-all">All (<?= $counts['all'] ?>)</button>
                                <button data-status-key="in-transit" onclick="filterShipments('in-transit', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">In Transit (<?= $counts['in-transit'] ?>)</button>
                                <button data-status-key="customs" onclick="filterShipments('customs', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Customs (<?= $counts['customs'] ?>)</button>
                                <button data-status-key="delayed" onclick="filterShipments('delayed', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Delayed (<?= $counts['delayed'] ?>)</button>
                                <button data-status-key="delivered" onclick="filterShipments('delivered', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Delivered (<?= $counts['delivered'] ?>)</button>
                            </div>
                        </div>

                        <!-- TABLE -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs" id="shipmentsTable">
                                <thead>
                                    <tr class="border-y border-slate-100 bg-slate-50/60 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                        <th class="py-3 px-6">WAYBILL</th>
                                        <th class="py-3 px-6">ROUTE</th>
                                        <th class="py-3 px-6">CARRIER</th>
                                        <th class="py-3 px-6">STATUS</th>
                                        <th class="py-3 px-6 text-right">ETA</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($shipments as $s):
                                        $b = shipmentBadge($s['status']);
                                        $etaClass = $s['status'] === 'delayed' ? 'text-rose-500' : 'text-slate-700';
                                    ?>
                                    <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="<?= $s['status'] ?>">
                                        <td class="py-4 px-6">
                                            <strong class="font-mono text-slate-900 text-xs block"><?= htmlspecialchars($s['waybill']) ?></strong>
                                            <span class="text-[10px] text-slate-400"><?= htmlspecialchars($s['type']) ?></span>
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-800"><?= htmlspecialchars($s['route']) ?></td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-bold border border-slate-200 shrink-0">
                                                    <?= carrierInitials($s['carrier']) ?>
                                                </div>
                                                <span class="text-slate-700 font-medium"><?= htmlspecialchars($s['carrier']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="inline-flex items-center gap-1.5 border <?= $b['class'] ?> font-semibold px-2.5 py-1 rounded-full text-[10px]">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $b['dot'] ?>"></span><?= $b['label'] ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right font-mono font-medium <?= $etaClass ?>"><?= htmlspecialchars($s['eta']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- RIGHT COLUMN: QUICK TRACK + UPCOMING + ACTIONS -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- QUICK TRACK -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-location-crosshairs text-brand-blue"></i> Quick Track
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5 mb-4">Jump straight to live map tracking.</p>
                        <form onsubmit="quickTrack(event)" class="space-y-3">
                            <div class="relative">
                                <i class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input id="quickTrackInput" type="text" placeholder="Enter waybill e.g. PH-WB-208841" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                            </div>
                            <button type="submit" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-2.5 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-radar"></i> Track Now
                            </button>
                        </form>
                    </div>

                    <!-- UPCOMING DELIVERIES TIMELINE -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Upcoming Deliveries</h2>
                                <p class="text-xs text-slate-400 mt-0.5">Next ETAs, soonest first</p>
                            </div>
                            <a href="/tracking" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue">Map &rarr;</a>
                        </div>
                        <div class="relative pl-4 space-y-5 before:absolute before:inset-y-1 before:left-[5px] before:w-px before:bg-slate-200">
                            <?php if (!empty($upcoming)): ?>
                                <?php foreach ($upcoming as $u):
                                    $b = shipmentBadge($u['status']);
                                ?>
                                <div class="relative">
                                    <span class="absolute -left-4 top-1.5 w-2.5 h-2.5 rounded-full <?= $b['dot'] ?> ring-4 ring-white"></span>
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-bold font-mono text-slate-800"><?= htmlspecialchars($u['waybill']) ?></p>
                                        <span class="text-[10px] font-semibold <?= $b['class'] ?> border px-2 py-0.5 rounded-full"><?= $b['label'] ?></span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-0.5"><?= htmlspecialchars($u['route']) ?></p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">ETA <?= htmlspecialchars($u['eta']) ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-xs text-slate-400 italic">No active shipments.</p>
                            <?php endif; ?>
                        </div>
                    </div>


                    <!-- QUICK ACTIONS -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h2 class="text-base font-bold text-slate-900 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="alert('Opening Freight Booking Form...')" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-plus text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">Book</span>
                            </button>
                            <a href="/tickets" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-headset text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">Ticket</span>
                            </a>
                            <a href="/documents" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-folder-open text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">Docs</span>
                            </a>
                            <a href="/sla-monitoring" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-gauge-high text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">SLA</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>


    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>
    <script>
        /* ---------- Status distribution doughnut ---------- */
        (function () {
            var canvas = document.getElementById('shipmentStatusChart');
            if (!canvas || typeof Chart === 'undefined') return;
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['In Transit', 'Customs', 'Delayed', 'Delivered'],
                    datasets: [{
                        data: [<?= $counts['in-transit'] ?>, <?= $counts['customs'] ?>, <?= $counts['delayed'] ?>, <?= $counts['delivered'] ?>],
                        backgroundColor: ['#3b82f6', '#f59e0b', '#f43f5e', '#10b981'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle', font: { size: 11, family: 'Inter' }, color: '#64748b' }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })();

        /* ---------- Quick Track → redirect to live map ---------- */
        function quickTrack(e) {
            e.preventDefault();
            var v = (document.getElementById('quickTrackInput').value || '').trim();
            if (!v) { alert('Enter a waybill number to track.'); return; }
            window.location.href = '/tracking?waybill=' + encodeURIComponent(v);
        }
    </script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

