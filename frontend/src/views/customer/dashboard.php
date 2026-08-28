<?php
$page_title = "Customer Dashboard � SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch Dynamic Profile Data mula sa FastAPI
$profile_res = make_api_request('/api/v1/portal/profile', 'GET');
$raw_profile = $profile_res['data'] ?? [];
$profile     = $raw_profile['data'] ?? $raw_profile;

// 2. Fetch Dynamic Shipments Data
$shipments_res = make_api_request('/api/v1/portal/shipments', 'GET');
$shipments     = $shipments_res['data'] ?? [];

// 3. Extract Profile Fields & Metrics gamit ang Fallbacks
$customer_id     = $profile['customer_id'] ?? '8B41';
$company_name    = $profile['company_name'] ?? 'Charlie Hub Inc.';
$contract_status = $profile['status'] ?? 'Newly Onboarded';
$metrics         = $profile['metrics'] ?? [
    'active_shipments' => 0,
    'in_transit'       => 0,
    'delayed'          => 0,
    'delivered_30d'    => 0
];
// Derived performance metrics (graceful fallbacks)
$on_time_pct   = $profile['on_time_pct'] ?? 94;
$open_breaches = $profile['open_breaches'] ?? 1;
$documents     = $profile['initial_documents'] ?? $profile['documents'] ?? [];

// Helper function para sa dynamic status pill badging
function getStatusBadgeClass($status) {
    switch (strtolower(trim($status))) {
        case 'in transit':
        case 'in_transit':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'customs':
        case 'customs clearance':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'delivered':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'delayed':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

    <!-- TOP HEADER BAR -->
    <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <button onclick="toggleSidebar()" class="sm:hidden text-slate-600 hover:text-slate-900 p-1.5 shrink-0">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <div class="min-w-0">
                <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Dashboard</h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5"><?= htmlspecialchars($company_name) ?> &middot; Acct #<?= htmlspecialchars($customer_id) ?></p>
            </div>
        </div>

        <!-- Global Search -->
        <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" placeholder="Track a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-3">
            <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors shrink-0">
                <i class="fa-solid fa-bell text-xs"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">
                Help Desk <i class="fa-solid fa-headset text-xs"></i>
            </button>
            <button onclick="openBookingModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> Book Shipment
            </button>
        </div>
    </header>

    <!-- DASHBOARD CONTENT BODY -->
    <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">

        <!-- WELCOME BANNER -->
        <section class="bg-gradient-to-r from-brand-blue to-brand-darkblue rounded-2xl p-6 lg:p-8 text-white shadow-lg shadow-blue-600/10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-blue-100 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <?= htmlspecialchars($contract_status) ?>
                </span>
                <h1 class="text-2xl lg:text-3xl font-black italic tracking-tight mt-3">Welcome back, <?= htmlspecialchars($company_name) ?> &#128075;</h1>
                <p class="text-sm text-blue-100 mt-1.5 max-w-md">Here is a live snapshot of your shipments and account health. Track, manage, and book freight in one place.</p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <a href="javascript:void(0)" onclick="openBookingModal()" class="bg-white text-brand-blue hover:bg-blue-50 font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i> New Booking
                    </a>
                    <a href="/tracking" class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl border border-white/20 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-location-crosshairs text-xs"></i> Track Shipment
                    </a>
                </div>
            </div>
            <div class="hidden sm:block absolute -right-8 -top-8 opacity-20 pointer-events-none select-none">
                <i class="fa-solid fa-box-open text-[150px]"></i>
            </div>
        </section>

        <!-- ROW 1: TOP KPI METRICS -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- Active Shipments -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-medium text-slate-500">Active Shipments</span>
                    <div class="p-2 rounded-xl bg-blue-50 text-brand-blue">
                        <i class="fa-solid fa-boxes-stacked text-sm"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['active_shipments'] ?? 0)) ?></p>
                    <p class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> Active in pipeline
                    </p>
                </div>
            </div>

            <!-- In Transit -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-medium text-slate-500">In Transit</span>
                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-truck-fast text-sm"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['in_transit'] ?? 0)) ?></p>
                    <p class="text-xs text-slate-500 font-medium mt-2">Currently moving</p>
                </div>
            </div>

            <!-- Delayed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-medium text-slate-500">Delayed</span>
                    <div class="p-2 rounded-xl bg-rose-50 text-rose-500">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['delayed'] ?? 0)) ?></p>
                    <p class="text-xs text-rose-600 font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Requires attention
                    </p>
                </div>
            </div>

            <!-- Delivered (30d) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-medium text-slate-500">Delivered (30d)</span>
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)($metrics['delivered_30d'] ?? 0)) ?></p>
                    <p class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-check text-[10px]"></i> Completed past month
                    </p>
                </div>
            </div>
        </section>
        <!-- ROW 2: MAIN DASHBOARD GRID -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- SHIPMENT MANIFEST TABLE -->
            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Shipment Manifest</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Live status across all active waybills</p>
                    </div>
                    <a href="/shipments" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue">View all &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-3">
                                <th class="pb-3">Waybill</th>
                                <th class="pb-3">Details</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">ETA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium">
                            <?php if (!empty($shipments)): ?>
                                <?php foreach ($shipments as $item): ?>
                                    <?php
                                        $waybill    = $item['waybill_number'] ?? $item['id'] ?? 'N/A';
                                        $type       = $item['type'] ?? 'Standard Cargo';
                                        $status     = $item['status'] ?? 'Pending';
                                        $eta        = $item['eta'] ?? 'TBD';
                                        $origin     = $item['origin'] ?? 'Origin';
                                        $dest       = $item['destination'] ?? 'Destination';
                                        $badgeClass = getStatusBadgeClass($status);
                                    ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-4">
                                            <p class="font-bold text-slate-800"><?= htmlspecialchars($waybill) ?></p>
                                            <p class="text-[11px] text-slate-400 font-normal"><?= htmlspecialchars($type) ?></p>
                                        </td>
                                        <td class="py-4">
                                            <p class="text-slate-700 font-medium"><?= htmlspecialchars($origin) ?> &rarr; <?= htmlspecialchars($dest) ?></p>
                                        </td>
                                        <td class="py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $badgeClass ?>">
                                                &bull; <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 font-mono text-slate-600">
                                            <?= htmlspecialchars($eta) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                        No active shipments found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT COLUMN WIDGETS -->
            <div class="lg:col-span-4 space-y-6">

                <!-- ON-TIME DELIVERY (replaces SLA Health) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">On-Time Delivery</h2>
                            <p class="text-xs text-slate-400">Performance (last 30 days)</p>
                        </div>
                        <a href="/sla-monitoring" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue">SLA &rarr;</a>
                    </div>

                    <div class="flex items-center gap-5">
                        <?php $ringDeg = max(0, min(100, (int)$on_time_pct)) * 3.6; ?>
                        <div class="relative w-24 h-24 shrink-0 rounded-full flex items-center justify-center"
                             style="background: conic-gradient(#0066ff <?= $ringDeg ?>deg, #e2e8f0 <?= $ringDeg ?>deg);">
                            <div class="w-[78px] h-[78px] rounded-full bg-white flex flex-col items-center justify-center">
                                <span class="text-2xl font-black text-slate-900 leading-none"><?= htmlspecialchars((string)$on_time_pct) ?>%</span>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mt-0.5">On time</span>
                            </div>
                        </div>

                        <div class="space-y-3 flex-1 min-w-0">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-xs text-slate-500">Delivered on schedule</span>
                            </div>
                            <?php $breachColor = ((int)$open_breaches > 0) ? 'bg-rose-500' : 'bg-emerald-500'; ?>
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full <?= $breachColor ?> shrink-0"></span>
                                <span class="text-xs text-slate-500">Open breaches: <b class="text-slate-800"><?= htmlspecialchars((string)$open_breaches) ?></b></span>
                            </div>
                            <a href="/shipments" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-blue hover:text-brand-darkblue">
                                View shipments <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- QUICK ACTIONS -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="javascript:void(0)" onclick="openBookingModal()" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                            <i class="fa-solid fa-plus text-base group-hover:scale-110 transition-transform"></i>
                            <span class="text-[11px] font-semibold">Book</span>
                        </a>
                        <a href="/tracking" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                            <i class="fa-solid fa-location-crosshairs text-base group-hover:scale-110 transition-transform"></i>
                            <span class="text-[11px] font-semibold">Track</span>
                        </a>
                        <a href="/documents" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                            <i class="fa-solid fa-file-lines text-base group-hover:scale-110 transition-transform"></i>
                            <span class="text-[11px] font-semibold">Documents</span>
                        </a>
                        <a href="/invoices" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                            <i class="fa-solid fa-receipt text-base group-hover:scale-110 transition-transform"></i>
                            <span class="text-[11px] font-semibold">Invoices</span>
                        </a>
                        <a href="/tickets" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                            <i class="fa-solid fa-ticket text-base group-hover:scale-110 transition-transform"></i>
                            <span class="text-[11px] font-semibold">Support</span>
                        </a>
                        <a href="/sla-monitoring" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                            <i class="fa-solid fa-gauge-high text-base group-hover:scale-110 transition-transform"></i>
                            <span class="text-[11px] font-semibold">SLA</span>
                        </a>
                    </div>
                </div>
                <!-- RECENT DOCUMENTS WIDGET -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Recent Documents</h2>
                            <p class="text-xs text-slate-400">Contracts, SLA &amp; waybills</p>
                        </div>
                        <a href="/documents" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue">Open &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        <?php if (!empty($documents)): ?>
                            <?php foreach ($documents as $doc): ?>
                                <?php
                                    $doc_title = $doc['title'] ?? $doc['name'] ?? 'Document.pdf';
                                    $doc_type  = $doc['doc_type'] ?? 'PDF';
                                    $uploaded  = $doc['uploaded_by'] ?? 'Admin';
                                ?>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50/50 hover:bg-blue-50 transition border border-blue-100/60">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="p-2 rounded-lg bg-blue-100 text-brand-blue shrink-0">
                                            <i class="fa-solid fa-file-pdf text-sm"></i>
                                        </div>
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($doc_title) ?></p>
                                            <p class="text-[10px] text-slate-400"><?= htmlspecialchars($doc_type) ?> &bull; By <?= htmlspecialchars($uploaded) ?></p>
                                        </div>
                                    </div>
                                    <button class="text-slate-400 hover:text-slate-700 shrink-0 ml-2" title="Download">
                                        <i class="fa-solid fa-download text-sm"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 italic py-2 text-center">No documents uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </section>

    </div>
</main>

<?php include_once '../../components/booking_modal.php'; ?>

<?php include_once '../../components/chat_widget.php'; ?>

<!-- Scripts -->
<script src="/assets/js/customer/customer_dashboard.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
