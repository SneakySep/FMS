<?php
$kpis = $kpis ?? [];
?>
<!-- KPI STAT STRIP (dashboard card style) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <!-- 1. Total Leads -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Leads</span>
            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
        <h3 class="text-xl font-extrabold text-slate-900"><?php echo $kpis['total_leads_current_month'] ?? 0; ?></h3>
        <p class="text-[11px] text-slate-500 mt-1">Last Month: <span class="text-slate-700 font-medium"><?php echo $kpis['total_leads_last_month'] ?? 0; ?></span></p>
    </div>

    <!-- 2. Active Leads -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active Leads</span>
            <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-bolt"></i>
            </div>
        </div>
        <h3 class="text-xl font-extrabold text-slate-900"><?php echo $kpis['active_leads'] ?? 0; ?></h3>
        <p class="text-[11px] text-amber-600 mt-1 font-medium">Currently active</p>
    </div>

    <!-- 3. Qualifying Opportunities -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Qualifying Opps</span>
            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-filter-circle-dollar"></i>
            </div>
        </div>
        <h3 class="text-xl font-extrabold text-slate-900"><?php echo $kpis['qualifying_opportunities'] ?? 0; ?></h3>
        <p class="text-[11px] text-indigo-600 mt-1 font-medium">Status: Qualifying</p>
    </div>

    <!-- 4. Won / Lost -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Won / Lost</span>
            <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <h3 class="text-lg font-extrabold text-emerald-600"><?php echo $kpis['total_closed_won'] ?? 0; ?> <span class="text-[10px] font-normal text-slate-400">Won</span></h3>
            <span class="text-slate-300">/</span>
            <h3 class="text-lg font-extrabold text-rose-600"><?php echo $kpis['total_closed_lost'] ?? 0; ?> <span class="text-[10px] font-normal text-slate-400">Lost</span></h3>
        </div>
        <p class="text-[11px] text-slate-500 mt-1">Closed Totals</p>
    </div>

</div>
