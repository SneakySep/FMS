<?php
$page_title = "BI Analytics · Priority Handling Logistics";
$activePage = 'analytics';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
require_once '../../helpers/api_helper.php';

// --- Fetch live analytics from backend API, with demo fallback ---
$analytics_res = make_api_request('/api/v1/portal/analytics', 'GET');
$analytics     = $analytics_res['data']['data'] ?? $analytics_res['data'] ?? null;

if (!empty($analytics) && is_array($analytics)) {
    // Live data from API
    $months          = $analytics['months'] ?? ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
    $shipments       = $analytics['shipments'] ?? [28, 34, 26, 42, 38, 48];
    $spend           = $analytics['spend'] ?? [42000, 51000, 39000, 64000, 58000, 72000];
    $onTimeTrend     = $analytics['on_time_trend'] ?? [88, 90, 86, 92, 91, 94];
    $statusBreakdown = $analytics['status_breakdown'] ?? ['Delivered' => 142, 'In Transit' => 38, 'Customs' => 14, 'Delayed' => 6];
    $topRoutes       = $analytics['top_routes'] ?? [
        ['Manila &rarr; Cebu', 18],
        ['Manila &rarr; Davao', 12],
        ['Cebu &rarr; Manila', 9],
        ['Manila &rarr; Iloilo', 7],
        ['Manila &rarr; Cagayan de Oro', 5],
    ];
    $totalShipments  = $analytics['total_shipments'] ?? array_sum($shipments);
    $totalSpend      = $analytics['total_spend'] ?? array_sum($spend);
    $slaPct          = $analytics['sla_pct'] ?? 94;
} else {
    // Demo fallback when API is unreachable
    $months          = ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
    $shipments       = [28, 34, 26, 42, 38, 48];
    $spend           = [42000, 51000, 39000, 64000, 58000, 72000];
    $onTimeTrend     = [88, 90, 86, 92, 91, 94];
    $statusBreakdown = ['Delivered' => 142, 'In Transit' => 38, 'Customs' => 14, 'Delayed' => 6];
    $topRoutes       = [
        ['Manila &rarr; Cebu',          18],
        ['Manila &rarr; Davao',         12],
        ['Cebu &rarr; Manila',           9],
        ['Manila &rarr; Iloilo',         7],
        ['Manila &rarr; Cagayan de Oro', 5],
    ];
    $totalShipments  = array_sum($shipments);
    $totalSpend      = array_sum($spend);
    $slaPct          = 94;
}
?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

    <!-- TOP HEADER BAR -->
    <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <button onclick="toggleSidebar()" class="md:hidden text-slate-600 hover:text-slate-900 p-1.5 shrink-0">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <div class="min-w-0">
                <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">BI Analytics</h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Performance insights &middot; Last 6 months</p>
            </div>
        </div>

        <!-- Global Search -->
        <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Track a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-bell text-xs"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">
                Help Desk <i class="fa-solid fa-headset text-xs"></i>
            </button>
            <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                + Book Shipment
            </button>
        </div>
    </header>

    <!-- ANALYTICS CONTENT BODY -->
    <div class="p-6 lg:p-8 space-y-8">

        <!-- KPI SUMMARY CARDS -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <?php
            $kpis = [
                ['Total Shipments', $totalShipments, '+12.4%', 'up',   'fa-box',        'text-brand-blue bg-blue-50 border-blue-100'],
                ['On-Time Delivery', $slaPct . '%',   '+3.0%', 'up',   'fa-truck-fast', 'text-emerald-600 bg-emerald-50 border-emerald-100'],
                ['Freight Spend',    '₱' . number_format($totalSpend), '+8.1%', 'up',   'fa-peso-sign',  'text-violet-600 bg-violet-50 border-violet-100'],
                ['Open Tickets',     '2',              '-1',    'down', 'fa-ticket',     'text-amber-600 bg-amber-50 border-amber-100'],
                ['Avg Transit',      '4.2d',           '-0.3d', 'down', 'fa-clock',      'text-sky-600 bg-sky-50 border-sky-100'],
            ];
            foreach ($kpis as $k):
                $trendColor = $k[3] === 'up' ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50';
                $trendIcon  = $k[3] === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
            ?>
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <span class="w-9 h-9 rounded-xl border flex items-center justify-center text-sm <?= $k[5] ?>">
                        <i class="fa-solid <?= $k[4] ?>"></i>
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full flex items-center gap-1 <?= $trendColor ?>">
                        <i class="fa-solid <?= $trendIcon ?> text-[8px]"></i><?= $k[2] ?>
                    </span>
                </div>
                <p class="text-xl font-black text-slate-900 tracking-tight"><?= $k[1] ?></p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5"><?= $k[0] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CHART ROW 1: VOLUME + STATUS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <!-- Shipment Volume -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Shipment Volume</h3>
                        <p class="text-xs text-slate-400">Monthly shipments &middot; last 6 months</p>
                    </div>
                    <span class="text-xs font-semibold text-brand-blue bg-blue-50 px-2.5 py-1 rounded-lg">12mo</span>
                </div>
                <div class="h-72"><canvas id="volumeChart"></canvas></div>
            </div>

            <!-- Status Breakdown -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Status Breakdown</h3>
                    <p class="text-xs text-slate-400">Current shipment states</p>
                </div>
                <div class="h-56 flex items-center justify-center"><canvas id="statusChart"></canvas></div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-[11px]">
                    <?php foreach ($statusBreakdown as $label => $val):
                        $dot = match (strtolower($label)) {
                            'delivered'   => 'bg-emerald-500',
                            'in transit'  => 'bg-brand-blue',
                            'customs'     => 'bg-amber-500',
                            default       => 'bg-rose-500'
                        };
                    ?>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full <?= $dot ?>"></span>
                        <span class="text-slate-500"><?= htmlspecialchars($label) ?></span>
                        <span class="ml-auto font-bold text-slate-700"><?= $val ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>


        <!-- CHART ROW 2: ON-TIME TREND + SPEND -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <!-- On-Time Trend -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">On-Time Delivery Trend</h3>
                    <p class="text-xs text-slate-400">% delivered within SLA window</p>
                </div>
                <div class="h-64"><canvas id="onTimeChart"></canvas></div>
            </div>

            <!-- Freight Spend -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Freight Spend</h3>
                    <p class="text-xs text-slate-400">Monthly cost (₱)</p>
                </div>
                <div class="h-64"><canvas id="spendChart"></canvas></div>
            </div>
        </div>


        <!-- CHART ROW 3: TOP ROUTES + SLA + INSIGHTS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

            <!-- Top Routes (progress bars) -->
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Top Routes</h3>
                    <p class="text-xs text-slate-400">By shipment volume</p>
                </div>
                <div class="space-y-4">
                    <?php
                    $maxRoute = max(array_column($topRoutes, 1));
                    foreach ($topRoutes as $r):
                        $pct = round($r[1] / $maxRoute * 100);
                    ?>
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs font-semibold text-slate-700"><?= $r[0] ?></span>
                            <span class="text-xs font-mono font-bold text-slate-500"><?= $r[1] ?></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-brand-blue h-full rounded-full" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SLA Compliance Ring -->
            <div class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col items-center justify-center text-center">
                <h3 class="text-base font-extrabold text-slate-900 mb-1 self-start">SLA Compliance</h3>
                <p class="text-xs text-slate-400 mb-4 self-start">Rolling 90 days</p>
                <div class="relative w-36 h-36">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3.2"></circle>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3.2"
                                stroke-dasharray="<?= $slaPct ?>,100" stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black text-slate-900"><?= $slaPct ?>%</span>
                        <span class="text-[10px] text-emerald-600 font-bold">On Target</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-4">6 breaches in last 90 days</p>
            </div>

            <!-- Smart Insights -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-xl bg-violet-50 border border-violet-100 text-violet-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-none">Smart Insights</h3>
                        <p class="text-xs text-slate-400">Auto-generated</p>
                    </div>
                </div>
                <ul class="space-y-3 text-xs text-slate-600">
                    <li class="flex gap-2.5"><i class="fa-solid fa-circle-arrow-up text-emerald-500 mt-0.5"></i><span>July was your busiest month with <b>48 shipments</b> (+14% vs June).</span></li>
                    <li class="flex gap-2.5"><i class="fa-solid fa-route text-brand-blue mt-0.5"></i><span><b>Manila &rarr; Cebu</b> is your top lane, making up 22% of volume.</span></li>
                    <li class="flex gap-2.5"><i class="fa-solid fa-shield-halved text-violet-500 mt-0.5"></i><span>On-time performance improved to <b>94%</b> &mdash; above your SLA target.</span></li>
                    <li class="flex gap-2.5"><i class="fa-solid fa-peso-sign text-amber-500 mt-0.5"></i><span>Freight spend peaked in July at <b>₱72,000</b>; consider bulk booking.</span></li>
                </ul>
            </div>
        </div>

    </div>
</main>

<?php include_once '../../components/chat_widget.php'; ?>


<!-- CHART.JS INIT -->
<script>
(function () {
    const BRAND = '#0066ff', BRAND_D = '#0052cc';
    const grid = { color: '#f1f5f9' };
    const tick = { color: '#94a3b8', font: { family: 'Inter', size: 11 } };
    Chart.defaults.font.family = 'Inter';

    // Shipment Volume (bar)
    new Chart(document.getElementById('volumeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Shipments',
                data: <?= json_encode($shipments) ?>,
                backgroundColor: BRAND,
                hoverBackgroundColor: BRAND_D,
                borderRadius: 8,
                maxBarThickness: 46
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                    callbacks: { label: c => ' ' + c.parsed.y + ' shipments' }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: tick },
                y: { grid: grid, ticks: tick, beginAtZero: true }
            }
        }
    });

    // Status Breakdown (doughnut)
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($statusBreakdown)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($statusBreakdown)) ?>,
                backgroundColor: ['#10b981', BRAND, '#f59e0b', '#f43f5e'],
                borderWidth: 3, borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 }
            }
        }
    });

    // On-Time Delivery Trend (line/area)
    new Chart(document.getElementById('onTimeChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'On-Time %',
                data: <?= json_encode($onTimeTrend) ?>,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.12)',
                fill: true, tension: 0.4,
                pointBackgroundColor: '#10b981', pointRadius: 4, pointHoverRadius: 6, borderWidth: 3
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                    callbacks: { label: c => ' ' + c.parsed.y + '% on-time' }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: tick },
                y: { grid: grid, ticks: { ...tick, callback: v => v + '%' }, min: 80, max: 100 }
            }
        }
    });

    // Freight Spend (area)
    new Chart(document.getElementById('spendChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Spend (₱)',
                data: <?= json_encode($spend) ?>,
                borderColor: BRAND,
                backgroundColor: 'rgba(0,102,255,0.12)',
                fill: true, tension: 0.4,
                pointBackgroundColor: BRAND, pointRadius: 4, pointHoverRadius: 6, borderWidth: 3
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                    callbacks: { label: c => ' ₱' + c.parsed.y.toLocaleString() }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: tick },
                y: { grid: grid, ticks: { ...tick, callback: v => '₱' + (v / 1000) + 'k' }, beginAtZero: true }
            }
        }
    });
})();
</script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

