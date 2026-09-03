<?php
// Dynamic Header Fallbacks
$pageTitle = $pageTitle ?? 'Dashboard';
$role = $userRole ?? $_SESSION['role'] ?? 'customer';
$userName = $displayName ?? $_SESSION['user_name'] ?? 'User';
$userInitials = $initials ?? strtoupper(substr($userName, 0, 2));
$hasNotifications = isset($new_inquiry) && $new_inquiry > 0;
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
      <span class="crm-topbar-title block truncate"><?= htmlspecialchars($pageTitle) ?></span>
    </div>
  </div>

  <!-- RIGHT SECTION: Search, Action, Clock & Profile Pill -->
  <div class="crm-topbar-group shrink-0">

    <!-- EXPANDABLE SEARCH BAR -->
    <div class="relative hidden sm:flex items-center">
      <i class="fa-solid fa-magnifying-glass crm-search-ico"></i>
      <input
        type="text"
        placeholder="Search..."
        class="crm-input !h-9 !w-10 focus:!w-56 !pl-9 !pr-3 !rounded-full !text-xs transition-all duration-300 ease-in-out cursor-pointer focus:cursor-text"
      />
    </div>

    <!-- Action Button -->
    <?php if ($role === 'sales_agent'): ?>
      <button type="button" onclick="openLeadModal()" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
        <i class="fa-solid fa-plus text-[10px]"></i>
        <span class="hidden sm:inline">New Leads</span>
      </button>
    <?php elseif ($role === 'admin'): ?>
      <a href="tickets.php?action=new" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
        <i class="fa-solid fa-plus text-[10px]"></i>
        <span class="hidden sm:inline">Create Ticket</span>
      </a>
    <?php elseif ($role === 'customer'): ?>
      <a href="shipments.php?action=new" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
        <i class="fa-solid fa-box-archive text-[10px]"></i>
        <span class="hidden sm:inline">New Booking</span>
      </a>
    <?php endif; ?>

    <!-- Notification Bell -->
    <button
      type="button"
      class="crm-icon-btn !h-9 !w-9 relative shrink-0"
      title="Notifications"
    >
      <i class="fa-regular fa-bell text-sm"></i>
      <?php if ($hasNotifications): ?>
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-navy-900 animate-pulse"></span>
      <?php endif; ?>
    </button>

    <!-- TIME & PROFILE PILL BADGE -->
    <div class="hidden md:flex items-center gap-2.5 pl-3 pr-1 py-1 rounded-full border border-line bg-surface">
      <div class="flex items-center gap-1.5 text-[11px] font-mono text-navy-500">
        <i class="fa-regular fa-clock text-[10px] text-navy-300"></i>
        <span id="headerClock">--:-- --</span>
      </div>
      <div class="crm-avatar !w-6 !h-6 !rounded-full !text-[10px]">
        <?= htmlspecialchars($userInitials) ?>
      </div>
    </div>

  </div>
</header>

<!-- JAVASCRIPT FOR CLOCK & DROPDOWNS -->
<script>
  // Real-time Clock Script
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
    
    // Close other open dropdowns
    document.querySelectorAll('.header-dropdown > div').forEach(menu => {
      if (menu.id !== menuId) menu.classList.add('hidden');
    });

    targetMenu.classList.toggle('hidden');
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', () => {
    document.querySelectorAll('.header-dropdown > div').forEach(menu => {
      menu.classList.add('hidden');
    });
  });
</script>