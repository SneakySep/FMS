<?php
/* ==========================================================================
   SIDEBAR  —  New_dash/dashboard.php
   --------------------------------------------------------------------------
   Static copy of frontend/src/includes/sidebar.php. The markup and every
   .crm-* class are identical, so the rail renders exactly like the customer /
   sales-agent / admin portals and inherits theme.css (including the
   hover-expand behaviour and the section-16 mobile drawer).

   Deliberately NOT connected to the backend API: src/services/SidebarService
   is not used, so there is no session read and no make_api_request() call for
   the nav badges. The navigation tree and the profile block below are plain
   PHP arrays literal to this demo portal.

   Variables a view may set BEFORE including this file:
     $activePage   string  Key of the highlighted nav item ('dashboard').
     $portalLabel  string  Badge under the brand name.
     $displayName  string  Name in the bottom profile card.
     $initials     string  Two-letter avatar.
     $dispatchId   string  Shown as "Dispatch #..." in the profile meta line.
     $sideMetric   array   ['label'=>, 'value'=>] mini bar (defaults to the
                           same-day SLA figure the customer rail shows).
   -------------------------------------------------------------------------- */

$activePage  = $activePage  ?? 'dashboard';
$portalLabel = $portalLabel ?? 'DELIVERY OPS';
$displayName = $displayName ?? 'D. Cruz';
$initials    = $initials    ?? 'DC';
$dispatchId  = $dispatchId  ?? 'CV-07';
$sideMetric  = $sideMetric  ?? ['label' => 'Same-day SLA', 'value' => '98%'];

/* Same shape SidebarService::buildNavigation() returns:
   SECTION TITLE => [ key => ['label','icon','url','badge'?] ] */
$navSections = [
    'OVERVIEW' => [
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-border-all', 'url' => 'dashboard.php'],
    ],
    'COURIER' => [
        'book'       => ['label' => 'Book Delivery', 'icon' => 'fa-bolt', 'url' => 'dashboard.php#book-delivery'],
        'deliveries' => ['label' => 'Active Deliveries', 'icon' => 'fa-list-check', 'url' => 'dashboard.php#active-deliveries', 'badge' => '6'],
    ],
    'FLEET' => [
        'motorcycles' => ['label' => 'Motorcycles', 'icon' => 'fa-motorcycle', 'url' => 'dashboard.php#fleet'],
        'vans'        => ['label' => 'Vans', 'icon' => 'fa-truck', 'url' => 'dashboard.php#fleet'],
    ],
    'TRACKING' => [
        'live' => ['label' => 'Live Map', 'icon' => 'fa-location-crosshairs', 'url' => 'dashboard.php#live-tracking'],
    ],
    'FEEDBACK' => [
        'ratings'  => ['label' => 'Ratings & Reviews', 'icon' => 'fa-star', 'url' => 'dashboard.php#feedback'],
        'settings' => ['label' => 'Settings', 'icon' => 'fa-gear', 'url' => 'dashboard.php'],
    ],
];
?>
<!-- MOBILE OVERLAY BACKDROP -->


<!-- SIDEBAR NAVIGATION CONTAINER -->
<!--
  Desktop pinning is load-bearing and copied verbatim from
  src/includes/sidebar.php: h-screen (NOT min-h-screen) caps the rail at one
  viewport so it can slide within the taller body, and md:sticky md:top-0 holds
  it there while <main> scrolls. md:self-start stops flex `stretch` from
  re-growing it, md:overflow-y-auto lets the nav list scroll internally.
  Do not reintroduce md:relative or min-h-screen here - either one breaks the
  pin. See assets/css/theme.css section 3 + section 16 (MOBILE RAIL).
-->
<aside id="sidebar" class="group crm-sidebar w-20 hover:w-64 text-slate-300 h-screen flex flex-col justify-between p-4 shrink-0 z-40 transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0 fixed md:sticky md:top-0 md:self-start overflow-x-hidden md:overflow-y-auto">
    <div class="space-y-6">

        <!-- Brand Logo & Badge -->
        <div class="flex items-center gap-3 px-1.5 py-2">
            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-lg shadow-navy flex items-center justify-center bg-white/5 border border-white/10 shrink-0">
                <img src="../assets/image/logo.png" alt="Company Logo" class="w-full h-full object-contain p-1">
            </div>
            <div class="leading-none crm-reveal whitespace-nowrap overflow-hidden">
                <h1 class="crm-brand-name">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                <span class="crm-brand-badge"><?= htmlspecialchars($portalLabel) ?></span>
            </div>
        </div>

        <!-- Navigation Links Loop -->
        <nav class="space-y-5 text-xs font-medium">
            <?php foreach ($navSections as $sectionTitle => $items): ?>
                <div>
                    <span class="crm-nav-group crm-reveal whitespace-nowrap overflow-hidden">
                        <?= htmlspecialchars($sectionTitle) ?>
                    </span>
                    <ul class="space-y-1">
                        <?php foreach ($items as $key => $item): ?>
                            <?php $isActive = ($activePage === $key); ?>
                            <li>
                                <a href="<?= htmlspecialchars($item['url']) ?>" class="crm-nav-item <?= $isActive ? 'is-active' : '' ?>">
                                    <span class="flex items-center gap-2.5 min-w-0 shrink-0">
                                        <span class="crm-nav-ico"><i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i></span>
                                        <span class="crm-reveal whitespace-nowrap overflow-hidden">
                                            <?= htmlspecialchars($item['label']) ?>
                                        </span>
                                    </span>
                                    <?php if (isset($item['badge'])): ?>
                                        <span class="crm-nav-badge crm-reveal"><?= htmlspecialchars($item['badge']) ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Sidebar Bottom Footer -->
    <div class="crm-side-foot space-y-3 pt-4">
        <!-- SLA Widget -->
        <div class="crm-sla-box crm-reveal">
            <div class="flex justify-between items-center text-[10px] font-semibold text-slate-400 mb-1.5 whitespace-nowrap">
                <span><?= htmlspecialchars($sideMetric['label']) ?></span>
                <span class="text-emerald-400 font-bold"><?= htmlspecialchars($sideMetric['value']) ?></span>
            </div>
            <div class="crm-sla-track">
                <div class="crm-sla-fill w-[98%]"></div>
            </div>
        </div>

        <!-- Profile Card & Sign-out Button -->
        <div class="crm-side-card">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="crm-avatar-navy">
                    <?= htmlspecialchars($initials) ?>
                </div>
                <div class="leading-tight min-w-0 flex-1 crm-reveal whitespace-nowrap overflow-hidden">
                    <h4 class="crm-side-name"><?= htmlspecialchars($displayName) ?></h4>
                    <span class="crm-side-meta">Courier Desk &bull; Dispatch #<?= htmlspecialchars($dispatchId) ?></span>
                </div>
            </div>

            <!-- No session exists on this screen, so this stays a visual stub. -->
            <button type="button" id="logoutBtn" title="Sign out (not connected yet)" class="crm-logout-btn crm-reveal">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
            </button>
        </div>
    </div>
</aside>


<div id="sidebarOverlay" class="fixed inset-0 bg-navy-950/60 z-30 hidden md:hidden backdrop-blur-sm transition-opacity"></div>
