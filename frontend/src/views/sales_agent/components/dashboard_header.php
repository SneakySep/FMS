<?php
// Reusable Dashboard Header for Sales Agent
// $header_title: The main title
// $header_subtitle: Optional subtitle
// $header_actions: Optional HTML for action buttons
?>
<header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <button onclick="toggleSidebar()" class="md:hidden text-slate-600 hover:text-slate-900 p-1.5 shrink-0">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <div class="min-w-0">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight"><?= $header_title ?? 'Dashboard' ?></h2>
            <?php if (isset($header_subtitle)): ?>
                <p class="text-xs text-slate-400 font-medium mt-0.5"><?= $header_subtitle ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Global Search -->
    <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" placeholder="Search leads, customers, quotes..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all">
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
            <i class="fa-solid fa-bell text-xs"></i>
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        
        <?php if (isset($header_actions)) { echo $header_actions; } ?>
    </div>
</header>
