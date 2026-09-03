<?php
// Reusable Dashboard Header for Sales Agent
// $header_title: The main title
// $header_subtitle: Optional subtitle
// $header_actions: Optional HTML for action buttons
?>
<header class="crm-topbar flex-wrap rounded-b-2xl border-x border-line mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <button onclick="toggleSidebar()" class="md:hidden crm-icon-btn !h-9 !w-9 shrink-0">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <div class="min-w-0">
            <h2 class="crm-topbar-title !text-xl"><?= htmlspecialchars($header_title ?? 'Dashboard') ?></h2>
            <?php if (isset($header_subtitle)): ?>
                <p class="crm-topbar-sub"><?= $header_subtitle ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Global Search -->
    <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
        <div class="crm-search">
            <i class="fa-solid fa-magnifying-glass crm-search-ico"></i>
            <input type="text" placeholder="Search leads, customers, quotes..." class="crm-input !pl-9 !py-2 !text-xs">
        </div>
    </div>

    <div class="crm-topbar-group">
        <button class="crm-icon-btn relative border border-line bg-surface">
            <i class="fa-solid fa-bell text-xs"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-navy-850"></span>
        </button>

        <?php if (isset($header_actions)) { echo $header_actions; } ?>
    </div>
</header>
