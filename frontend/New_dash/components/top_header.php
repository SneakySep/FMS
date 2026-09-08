<?php
/* ==========================================================================
   TOP HEADER  —  New_dash/dashboard.php
   --------------------------------------------------------------------------
   Static copy of frontend/src/components/top_header.php (the bar every portal
   shares through components/customer_header.php). Same markup, same .crm-*
   classes, same clock + dropdown script, so the delivery dashboard sits
   visually identical to the other dashboards.

   Deliberately NOT connected to the backend API: no session_start(), no
   $_SESSION reads. Everything the upstream file pulled from the session is
   passed in as a plain variable by the view.

   Variables a view sets BEFORE including this file:
     $pageTitle     string   Bar heading.
     $pageSubtitle  string   Small line under the heading.
     $headerSearch  array    ['placeholder'=>, 'id'=>, 'onkeyup'=>, 'value'=>]
     $headerActions string   Raw HTML for the right-hand action group.
     $headerBell    array    ['store'=>, 'count'=>, 'items'=>[]]
     $notif_styles  array    type => [dot,text,label] map for the bell items.
     $userName      string   Profile pill fallback name.
     $userInitials  string   Profile pill avatar.
   -------------------------------------------------------------------------- */

$pageTitle     = $pageTitle     ?? 'Delivery Dashboard';
$userName      = $userName      ?? 'D. Cruz';
$userInitials  = $userInitials  ?? 'DC';

$esc = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$notif_style_map = $notif_styles ?? [
    'success' => ['dot' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400', 'label' => 'Delivered'],
    'warning' => ['dot' => 'bg-amber-500',   'text' => 'text-amber-600 dark:text-amber-400',     'label' => 'At risk'],
    'urgent'  => ['dot' => 'bg-rose-500',    'text' => 'text-rose-600 dark:text-rose-400',       'label' => 'Late'],
    'info'    => ['dot' => 'bg-sky-500',     'text' => 'text-brand-blue',                        'label' => 'Info'],
];
?>

<!-- TOP NAVIGATION HEADER -->
<header class="crm-topbar w-full mb-6">
  <div class="flex items-center gap-4 min-w-0">
    <!-- Mobile Sidebar Toggle -->
    <button
      type="button"
      onclick="toggleSidebar()"
      class="md:hidden crm-icon-btn !h-9 !w-9 border border-line shrink-0"
      aria-label="Toggle Sidebar"
    >
      <i class="fa-solid fa-bars text-sm"></i>
    </button>

    <!-- Page Title -->
    <div class="min-w-0">
      <span class="crm-topbar-title block truncate"><?= $esc($pageTitle) ?></span>
      <?php if (!empty($pageSubtitle)): ?>
        <span class="crm-topbar-sub truncate"><?= $esc($pageSubtitle) ?></span>
      <?php endif; ?>
    </div>
  </div>

  <!-- RIGHT SECTION: Search, Action, Clock & Profile Pill -->
  <div class="crm-topbar-group shrink-0">

    <!-- EXPANDABLE SEARCH BAR -->
    <?php if (($headerSearch ?? null) !== false): ?>
      <?php
        $search = is_array($headerSearch ?? null) ? $headerSearch : [];
        $searchHandler = $search['onkeyup'] ?? '';
      ?>
      <div class="crm-search relative hidden sm:flex items-center">
        <i class="fa-solid fa-magnifying-glass crm-search-ico"></i>
        <input
          type="text"
          id="<?= $esc($search['id'] ?? '') ?>"
          <?php if ($searchHandler !== ''): ?>onkeyup="<?= $esc($searchHandler) ?>"<?php endif; ?>
          value="<?= $esc($search['value'] ?? '') ?>"
          placeholder="<?= $esc($search['placeholder'] ?? 'Search...') ?>"
          class="crm-input !h-9 !w-10 focus:!w-56 sm:!w-40 sm:focus:!w-64 !pl-9 !pr-3 !rounded-full !text-xs transition-all duration-300 ease-in-out cursor-pointer focus:cursor-text"
        />
      </div>
    <?php endif; ?>

    <!-- Action Button: default is the courier desk's hero action. -->
    <?php if (!empty($headerActions)): ?>
      <?= $headerActions ?>
    <?php else: ?>
      <a href="#book-delivery" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
        <i class="fa-solid fa-bolt text-[10px]"></i>
        <span class="hidden sm:inline">Book Delivery</span>
      </a>
    <?php endif; ?>

    <?php if (!empty($headerBell) && is_array($headerBell)): ?>
      <!-- Notification bell + dropdown (driven by /assets/js/customer/notification_bell.js) -->
      <?php
        $bellCount = (int) ($headerBell['count'] ?? 0);
        $bellItems = $headerBell['items'] ?? [];
      ?>
      <div class="relative shrink-0" id="notifBellWrap" data-notif-store="<?= $esc($headerBell['store'] ?? 'newdash_read_notifs') ?>">
        <button type="button" id="notifBellBtn" aria-haspopup="true" aria-expanded="false" title="Notifications"
                class="crm-icon-btn !h-9 !w-9 relative shrink-0">
          <i class="fa-solid fa-bell text-sm"></i>
          <span id="notifBadge" class="absolute top-1 right-1 min-w-[1rem] h-4 px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full items-center justify-center border-2 border-white dark:border-navy-900 <?= $bellCount > 0 ? 'flex' : 'hidden' ?>"><?= $bellCount ?></span>
        </button>

        <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 crm-card overflow-hidden z-50" style="border-radius: .875rem; box-shadow: var(--shadow-lift);">
          <div class="flex items-center justify-between px-4 py-3" style="background: var(--surface-muted); border-bottom: 1px solid var(--line);">
            <h3 class="crm-section-label !mb-0">Notifications</h3>
            <span id="notifUnreadPill" class="crm-badge crm-badge-blue"><?= $bellCount ?> new</span>
          </div>
          <div class="max-h-72 overflow-y-auto crm-scroll">
            <?php if (!empty($bellItems)): ?>
              <?php foreach ($bellItems as $n_idx => $note): ?>
                <?php
                  $n_id    = isset($note['id']) ? (int) $note['id'] : 'idx-' . $n_idx;
                  $n_style = $notif_style_map[$note['type'] ?? 'info'] ?? $notif_style_map['info'];
                ?>
                <a href="<?= $esc($note['link'] ?? '#') ?>" data-notif-id="<?= $esc($n_id) ?>"
                   class="notif-item flex items-start gap-3 px-4 py-3 transition-colors hover:bg-navy-50 dark:hover:bg-navy-850">
                  <span class="w-2 h-2 rounded-full <?= $esc($n_style['dot']) ?> mt-1.5 shrink-0"></span>
                  <span class="min-w-0 flex-1">
                    <span class="flex items-center justify-between gap-2">
                      <span class="text-xs font-semibold truncate" style="color: var(--fg-heading);"><?= $esc($note['title'] ?? 'Notification') ?></span>
                      <span class="text-xs font-semibold shrink-0 <?= $esc($n_style['text']) ?>"><?= $esc($n_style['label']) ?></span>
                    </span>
                    <span class="block text-xs leading-snug mt-0.5 line-clamp-2" style="color: var(--fg-body);"><?= $esc($note['message'] ?? '') ?></span>
                    <span class="block text-xs mt-1" style="color: var(--fg-muted);">&bull; <?= $esc($note['time'] ?? '') ?></span>
                  </span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-xs py-6 text-center" style="color: var(--fg-muted);">You're all caught up.</p>
            <?php endif; ?>
          </div>
          <a href="#feedback" class="block text-center text-xs font-semibold text-brand-blue py-2.5" style="border-top: 1px solid var(--line);">View all notifications &rarr;</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- TIME & PROFILE PILL BADGE -->
    <div class="hidden md:flex items-center gap-2.5 pl-3 pr-1 py-1 rounded-full border border-line bg-surface">
      <div class="flex items-center gap-1.5 text-xs font-mono" style="color: var(--fg-muted);">
        <i class="fa-regular fa-clock text-[10px]" style="color: var(--navy-300);"></i>
        <span id="headerClock">--:-- --</span>
      </div>
      <div class="crm-avatar !w-6 !h-6 !rounded-full !text-[10px]">
        <?= $esc($userInitials) ?>
      </div>
    </div>

  </div>
</header>

<!-- JAVASCRIPT FOR CLOCK & DROPDOWNS -->
<script>
  // Real-time Clock Script (copied from src/components/top_header.php)
  function updateHeaderClock() {
    const clockElement = document.getElementById('headerClock');
    if (!clockElement) return;

    const now = new Date();
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;

    clockElement.textContent = `${hours}:${minutes} ${ampm}`;
  }
  updateHeaderClock();
  setInterval(updateHeaderClock, 1000);

  // Dropdown Toggle Script
  function toggleHeaderDropdown(event, menuId) {
    event.stopPropagation();
    const targetMenu = document.getElementById(menuId);
    document.querySelectorAll('.header-dropdown > div').forEach(menu => {
      if (menu.id !== menuId) menu.classList.add('hidden');
    });
    targetMenu.classList.toggle('hidden');
  }

  document.addEventListener('click', () => {
    document.querySelectorAll('.header-dropdown > div').forEach(menu => {
      menu.classList.add('hidden');
    });
  });
</script>

