<?php
$page_title = "Customer Dashboard - Priority Handling";


include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../helpers/api_helper.php';

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
// Derived performance metrics (graceful fallbacks)
$on_time_pct   = $profile['on_time_pct'] ?? 94;
$open_breaches = $profile['open_breaches'] ?? 1;
$documents     = $profile['initial_documents'] ?? $profile['documents'] ?? [];

// 4. Fetch Active Campaign / Promo Posts (published by Sales Agents)
//    Endpoint: GET /api/v1/campaigns/active-posts - returns a bare JSON list
//    already filtered to is_active = True and auto-drops expired posts.
$campaign_res = make_api_request('/api/v1/campaigns/active-posts', 'GET');
$campaigns    = $campaign_res['data'] ?? [];
if (!is_array($campaigns)) {
    $campaigns = [];
}

// Demo fallback kapag down ang backend API
if (empty($campaigns)) {
    $campaigns = [
        [
            'title'          => 'Free Tumbler Promo on All Domestic Shipments',
            'description'    => 'Book at least 5 domestic shipments this month and get a limited-edition Priority Handling tumbler for your office.',
            'image_url'      => '',
            'is_permanent'   => false,
            'expires_at'     => date('c', strtotime('+36 hours')),
            'author_name'    => 'M. Reyes',
            'author_role'    => 'Sales Agent',
            'is_ending_soon' => true,
        ],
        [
            'title'          => 'Quarterly Rate Review - Priority Lane',
            'description'    => 'Our account team will walk through your Q1 volume and confirm your priority-lane rates for the next quarter.',
            'image_url'      => '',
            'is_permanent'   => true,
            'expires_at'     => null,
            'author_name'    => 'Account Team',
            'author_role'    => 'Priority Handling',
            'is_ending_soon' => false,
        ],
    ];
}

// 5. Fetch Notifications (same source as notification.php)
$notif_res  = make_api_request('/api/v1/portal/notifications', 'GET');
$notif_data = $notif_res['data']['data'] ?? $notif_res['data'] ?? null;
if (!empty($notif_data) && is_array($notif_data)) {
    $notifications = array_slice($notif_data, 0, 4);
} else {
    $notifications = [
        ['id' => 1, 'type' => 'urgent',  'title' => 'SLA Breach - WB12345',        'message' => 'Delivery exceeded the SLA window. Escalated to Ops.', 'time' => '2h ago', 'link' => 'sla-monitoring.php'],
        ['id' => 2, 'type' => 'warning', 'title' => 'Document Pending - WB208812', 'message' => 'Commercial Invoice awaiting your review.',              'time' => '1d ago', 'link' => 'documents.php'],
        ['id' => 3, 'type' => 'success', 'title' => 'POD Confirmed - WB208835',    'message' => 'Proof of Delivery uploaded for Cebu-Manila.',          'time' => '2d ago', 'link' => 'documents.php'],
    ];
}
$unread_count = count($notifications);

// Shared notification type styling (header dropdown + right-column widget)
$notif_styles = [
    'urgent'  => ['dot' => 'bg-rose-500',   'label' => 'Urgent',    'text' => 'text-rose-600'],
    'warning' => ['dot' => 'bg-amber-500',  'label' => 'Warning',   'text' => 'text-amber-600'],
    'success' => ['dot' => 'bg-emerald-500','label' => 'Confirmed', 'text' => 'text-emerald-600'],
    'info'    => ['dot' => 'bg-sky-500',    'label' => 'Info',      'text' => 'text-sky-600'],
];

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
<?php include_once __DIR__ . '/../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

    <?php
    // Shared top bar: title + subtitle, global search, notification bell, actions.
    $pageTitle    = 'Priority Handling';
    $pageSubtitle = $company_name . ' · Acct #' . $customer_id;
    $headerSearch = ['placeholder' => 'Track a waybill, invoice, or document...'];
    $headerBell   = [
        'store' => 'crm_read_notifs_' . $customer_id,
        'count' => $unread_count,
        'items' => $notifications,
    ];
    include_once __DIR__ . '/../../components/customer_header.php'; ?>

    <!-- DASHBOARD CONTENT BODY -->
    <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">

        <!-- WELCOME BANNER -->
        <section class="bg-gradient-to-r from-brand-blue to-brand-darkblue rounded-2xl p-6 lg:p-8 text-white shadow-lg shadow-blue-600/10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-blue-100 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <?= htmlspecialchars($contract_status) ?>
                </span>
                <h1 class="text-2xl lg:text-3xl font-black italic text-white tracking-tight mt-3">Welcome back, <?= htmlspecialchars($company_name) ?> &#128075;</h1>
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

        <!-- ROW 1.5: CAMPAIGNS & PROMOTIONS FEED -->
        <section class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Campaigns &amp; Promotions</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Latest offers and announcements from your account team</p>
                </div>
                <div class="flex items-center gap-2">
                    <?php $ending_soon = array_filter($campaigns, fn($c) => !empty($c['is_ending_soon'])); ?>
                    <?php if (!empty($ending_soon)): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                            <i class="fa-solid fa-fire text-[9px]"></i> <?= count($ending_soon) ?> ending soon
                        </span>
                    <?php endif; ?>
                    <span id="campaignCountBadge" class="text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-full"><?= count($campaigns) ?> Active</span>
                </div>
            </div>

            <div id="campaignGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                <?php if (!empty($campaigns)): ?>
                    <?php foreach ($campaigns as $camp): ?>
                        <?php
                            $camp_title   = htmlspecialchars($camp['title'] ?? 'Untitled Campaign');
                            $camp_desc    = htmlspecialchars($camp['description'] ?? 'No description provided.');
                            $camp_image   = trim((string) ($camp['image_url'] ?? ''));
                            $is_permanent = !empty($camp['is_permanent']);
                            $expires_at   = $camp['expires_at'] ?? $camp['end_date'] ?? null;
                            $author_name  = htmlspecialchars($camp['author_name'] ?? 'Account Team');
                            $author_role  = htmlspecialchars($camp['author_role'] ?? 'Priority Handling');
                        ?>
                        <article class="campaign-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                            <!-- Poster -->
                            <div class="relative h-40 bg-gradient-to-br from-brand-blue to-brand-darkblue">
                                <?php if ($camp_image !== ''): ?>
                                    <img src="<?= htmlspecialchars($camp_image) ?>" alt="<?= $camp_title ?>"
                                         class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
                                    <div class="hidden absolute inset-0 flex items-center justify-center text-white/80">
                                        <i class="fa-solid fa-bullhorn text-3xl"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-white/80">
                                        <i class="fa-solid fa-bullhorn text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-3 right-3">
                                    <?php if ($is_permanent): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/90 text-emerald-600 border border-emerald-100">Permanent</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/90 text-amber-600 border border-amber-100">Limited-Time</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Body -->
                            <div class="p-4 space-y-1.5">
                                <h3 class="font-bold text-slate-800 text-sm leading-snug tracking-tight"><?= $camp_title ?></h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-3"><?= $camp_desc ?></p>
                            </div>
                            <!-- Footer: author + countdown -->
                            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                                <span class="flex items-center gap-1.5 text-[10px] text-slate-400 font-medium min-w-0">
                                    <i class="fa-solid fa-user-tie text-[9px] shrink-0"></i>
                                    <span class="truncate"><?= $author_name ?> &middot; <?= $author_role ?></span>
                                </span>
                                <?php if ($is_permanent): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300 shrink-0">
                                        <i class="fa-solid fa-infinity text-[9px]"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="campaign-timer inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300 shrink-0"
                                          data-expires="<?= htmlspecialchars((string) ($expires_at ?? '')) ?>">
                                        <i class="fa-solid fa-clock text-[9px]"></i> <span class="campaign-timer-text">&mdash;</span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full py-12 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        No active campaigns right now. Check back soon for offers from your account team.
                    </div>
                <?php endif; ?>
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

                <!-- NOTIFICATIONS WIDGET -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Notifications</h2>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars((string) $unread_count) ?> recent alerts</p>
                        </div>
                        <a href="notification.php" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue">View all &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($notifications as $note):
                            $n_type  = $note['type'] ?? 'info';
                            $n_style = $notif_styles[$n_type] ?? $notif_styles['info'];
                            $n_title = htmlspecialchars($note['title'] ?? 'Notification');
                            $n_msg   = htmlspecialchars($note['message'] ?? '');
                            $n_time  = htmlspecialchars($note['time'] ?? '');
                            $n_link  = htmlspecialchars($note['link'] ?? 'notification.php');
                        ?>
                            <a href="<?= $n_link ?>" class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-100 transition">
                                <span class="w-2 h-2 rounded-full <?= $n_style['dot'] ?> mt-1.5 shrink-0"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-bold text-slate-800 truncate"><?= $n_title ?></span>
                                        <span class="text-[10px] <?= $n_style['text'] ?> font-semibold shrink-0"><?= $n_style['label'] ?></span>
                                    </span>
                                    <span class="block text-[11px] text-slate-500 leading-snug mt-0.5 line-clamp-2"><?= $n_msg ?></span>
                                    <span class="block text-[10px] text-slate-400 mt-1">&bull; <?= $n_time ?></span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

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

<?php include_once __DIR__ . '/../../components/booking_modal.php'; ?>

<?php include_once __DIR__ . '/../../components/chat_widget.php'; ?>

<!-- Scripts -->
<script src="/assets/js/customer/customer_dashboard.js"></script>
<script src="/assets/js/customer/campaign_countdown.js"></script>
<script src="/assets/js/customer/notification_bell.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
