<?php
$page_title = "SLA Monitoring · Priority Handling Logistics";
$activePage = 'sla-monitoring';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
require_once '../../helpers/api_helper.php';

// --- Fetch live SLA data from the backend API, with demo fallback ---
$sla_res  = make_api_request('/api/v1/portal/sla', 'GET');
$sla_data = $sla_res['data']['data'] ?? $sla_res['data'] ?? null;

if (!empty($sla_data) && is_array($sla_data)) {
    $sla_compliance    = (int)($sla_data['compliance_pct'] ?? $sla_data['sla_pct'] ?? 94);
    $total_commitments = (int)($sla_data['total_commitments'] ?? 1024);
    $breaches_total    = (int)($sla_data['breaches_total'] ?? $sla_data['breach_count'] ?? 4);
    $breach_causes     = $sla_data['breach_causes'] ?? [
        ['cause' => 'Customs clearance', 'count' => 2, 'color' => 'amber'],
        ['cause' => 'Transit time',      'count' => 1, 'color' => 'rose'],
        ['cause' => 'On-time pickup',    'count' => 1, 'color' => 'brand-blue'],
        ['cause' => 'Damage-free',       'count' => 0, 'color' => 'emerald'],
    ];
    $breach_log        = $sla_data['breach_log'] ?? [
        ['waybill' => 'PH-WB-208841', 'commitment' => '48h Delivery',   'status' => 'Breached', 'breach_time' => '3h 40m', 'date' => 'Jul 28, 2026'],
        ['waybill' => 'PH-WB-208790', 'commitment' => 'Customs <= 24h',  'status' => 'Breached', 'breach_time' => '6h 15m', 'date' => 'Jul 26, 2026'],
        ['waybill' => 'PH-WB-208812', 'commitment' => 'Damage-Free',    'status' => 'Met',      'breach_time' => '-',      'date' => 'Jul 25, 2026'],
        ['waybill' => 'PH-WB-208799', 'commitment' => 'On-time Pickup', 'status' => 'Breached', 'breach_time' => '1h 20m', 'date' => 'Jul 24, 2026'],
        ['waybill' => 'PH-WB-208712', 'commitment' => '48h Delivery',   'status' => 'Met',      'breach_time' => '-',      'date' => 'Jul 22, 2026'],
    ];
} else {
    // Demo fallback
    $sla_compliance    = 94;
    $total_commitments = 1024;
    $breaches_total    = 4;
    $breach_causes     = [
        ['cause' => 'Customs clearance', 'count' => 2, 'color' => 'amber'],
        ['cause' => 'Transit time',      'count' => 1, 'color' => 'rose'],
        ['cause' => 'On-time pickup',    'count' => 1, 'color' => 'brand-blue'],
        ['cause' => 'Damage-free',       'count' => 0, 'color' => 'emerald'],
    ];
    $breach_log        = [
        ['waybill' => 'PH-WB-208841', 'commitment' => '48h Delivery',   'status' => 'Breached', 'breach_time' => '3h 40m', 'date' => 'Jul 28, 2026'],
        ['waybill' => 'PH-WB-208790', 'commitment' => 'Customs <= 24h',  'status' => 'Breached', 'breach_time' => '6h 15m', 'date' => 'Jul 26, 2026'],
        ['waybill' => 'PH-WB-208812', 'commitment' => 'Damage-Free',    'status' => 'Met',      'breach_time' => '-',      'date' => 'Jul 25, 2026'],
        ['waybill' => 'PH-WB-208799', 'commitment' => 'On-time Pickup', 'status' => 'Breached', 'breach_time' => '1h 20m', 'date' => 'Jul 24, 2026'],
        ['waybill' => 'PH-WB-208712', 'commitment' => '48h Delivery',   'status' => 'Met',      'breach_time' => '-',      'date' => 'Jul 22, 2026'],
    ];
}

?>
<style>
  @keyframes slaFadeUp { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform:none;} }
  .sla-anim { animation: slaFadeUp .5s ease both; }
  .sla-donut { transition: transform .6s cubic-bezier(.2,.8,.2,1); }
  .sla-donut-wrap:hover .sla-donut { transform: rotate(-8deg) scale(1.03); }
</style>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

  <!-- TOP HEADER BAR -->
  <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center gap-4">
    <div class="flex items-center gap-3 min-w-0">
      <button onclick="toggleSidebar()" class="sm:hidden text-slate-600 hover:text-slate-900 p-1.5 shrink-0">
        <i class="fa-solid fa-bars text-lg"></i>
      </button>
      <div class="min-w-0">
        <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">SLA Monitoring</h2>
        <p class="text-xs text-slate-400 font-medium mt-0.5">Service level agreement performance &middot; Acct #8B41</p>
      </div>
    </div>

    <!-- Global Search -->
    <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
      <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" id="slaSearchInput" onkeyup="searchBreachLog()" placeholder="Search waybill, commitment, or status..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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
      <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
        <i class="fa-solid fa-plus text-xs"></i> Book Shipment
      </button>
    </div>
  </header>

  <!-- SLA MONITORING CONTENT BODY -->
  <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">

    <!-- STATUS STRIP -->
    <section class="bg-gradient-to-r from-brand-blue to-brand-darkblue rounded-2xl p-6 lg:p-7 text-white shadow-lg shadow-blue-600/10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden relative sla-anim">
      <div class="relative z-10">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-blue-100 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
          Commitments in good standing
        </span>
        <h1 class="text-xl lg:text-2xl font-black italic tracking-tight mt-3">Your SLA compliance is holding at <?= $sla_compliance ?>%</h1>
        <p class="text-sm text-blue-100 mt-1.5 max-w-md">One open breach needs attention. Most service commitments are being met ahead of target.</p>
        <div class="flex flex-wrap gap-2 mt-4">
          <span class="bg-white/10 hover:bg-white/20 text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl border border-white/20 transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-rotate text-[10px]"></i> Refreshed every 15 min
          </span>
          <span class="bg-white/10 hover:bg-white/20 text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl border border-white/20 transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-calendar-check text-[10px]"></i> Next review: Sep 30, 2026
          </span>
        </div>
      </div>
      <div class="hidden sm:flex shrink-0 relative z-10">
        <div class="w-28 h-28 rounded-full bg-white/10 border border-white/20 flex flex-col items-center justify-center backdrop-blur-sm">
          <span class="text-3xl font-black"><?= $sla_compliance ?>%</span>
          <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Compliance</span>
        </div>
      </div>
      <div class="hidden sm:block absolute -right-6 -bottom-10 opacity-20 pointer-events-none select-none">
        <i class="fa-solid fa-gauge-high text-[150px]"></i>
      </div>
    </section>
    <!-- ROW 1: TOP KPI METRIC CARDS -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

      <!-- Overall Compliance -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start">
          <span class="text-xs font-medium text-slate-500">Overall Compliance</span>
          <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
            <i class="fa-solid fa-circle-check text-sm"></i>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-3xl font-extrabold text-slate-900"><?= $sla_compliance ?>%</p>
          <p class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1">
            <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> 2 pts vs last month
          </p>
        </div>
      </div>

      <!-- Open Breaches -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start">
          <span class="text-xs font-medium text-slate-500">Open Breaches</span>
          <div class="p-2 rounded-xl bg-rose-50 text-rose-500">
            <i class="fa-solid fa-triangle-exclamation text-sm"></i>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-3xl font-extrabold text-slate-900">1</p>
          <p class="text-xs text-rose-600 font-semibold mt-2 flex items-center gap-1 truncate">
            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Customs, WB-208712
          </p>
        </div>
      </div>

      <!-- Avg. Resolution Time -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start">
          <span class="text-xs font-medium text-slate-500">Avg. Resolution Time</span>
          <div class="p-2 rounded-xl bg-blue-50 text-brand-blue">
            <i class="fa-solid fa-stopwatch text-sm"></i>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-3xl font-extrabold text-slate-900">6.2h</p>
          <p class="text-xs text-slate-500 font-medium mt-2 flex items-center gap-1">
            <i class="fa-solid fa-circle-check text-[10px] text-emerald-500"></i> Within target
          </p>
        </div>
      </div>

      <!-- On-Time Delivery (NEW) -->
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start">
          <span class="text-xs font-medium text-slate-500">On-Time Delivery</span>
          <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
            <i class="fa-solid fa-truck-fast text-sm"></i>
          </div>
        </div>
        <div class="mt-4">
          <p class="text-3xl font-extrabold text-slate-900">96%</p>
          <p class="text-xs text-slate-500 font-medium mt-2 flex items-center gap-1">
            <i class="fa-solid fa-bullseye text-[10px] text-brand-blue"></i> 612 of 638 shipments
          </p>
        </div>
      </div>

    </section>
    <!-- ROW 2: COMPLIANCE TREND + SLA HEALTH -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- Left: Compliance Trend Bar Chart (8 cols) -->
      <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Compliance Trend</h3>
            <p class="text-xs text-slate-400">Monthly SLA compliance &middot; last 6 months</p>
          </div>
          <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
            <span class="w-3 h-0.5 border-t-2 border-dashed border-brand-blue"></span> 90% target
          </span>
        </div>

        <!-- Custom HTML/CSS Bar Chart -->
        <div class="pt-8 pb-2">
          <div class="h-64 flex items-end justify-between gap-3 sm:gap-6 px-2 sm:px-6 border-b border-slate-100 relative">

            <!-- 90% target line -->
            <div class="absolute left-0 right-0 top-[10%] border-t-2 border-dashed border-brand-blue/50"></div>

            <!-- Feb -->
            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
              <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[86%] group-hover:bg-brand-darkblue relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">86%</div>
              </div>
              <span class="text-xs font-semibold text-slate-400 mt-2">Feb</span>
            </div>

            <!-- Mar -->
            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
              <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[88%] group-hover:bg-brand-darkblue relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">88%</div>
              </div>
              <span class="text-xs font-semibold text-slate-400 mt-2">Mar</span>
            </div>

            <!-- Apr -->
            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
              <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[82%] group-hover:bg-brand-darkblue relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">82%</div>
              </div>
              <span class="text-xs font-semibold text-slate-400 mt-2">Apr</span>
            </div>

            <!-- May -->
            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
              <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[90%] group-hover:bg-brand-darkblue relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">90%</div>
              </div>
              <span class="text-xs font-semibold text-slate-400 mt-2">May</span>
            </div>

            <!-- Jun -->
            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
              <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[92%] group-hover:bg-brand-darkblue relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">92%</div>
              </div>
              <span class="text-xs font-semibold text-slate-400 mt-2">Jun</span>
            </div>

            <!-- Jul -->
            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
              <div class="w-full max-w-[42px] bg-emerald-500 rounded-t-lg transition-all duration-500 h-[94%] group-hover:bg-emerald-600 relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">94%</div>
              </div>
              <span class="text-xs font-semibold text-emerald-600 mt-2">Jul</span>
            </div>

          </div>
        </div>
      </div>
      <!-- Right: SLA Health + Donut (4 cols) -->
      <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">SLA Health</h3>
          <p class="text-xs text-slate-400">By service commitment</p>
        </div>

        <!-- Donut (conic-gradient) -->
        <div class="flex items-center justify-center sla-donut-wrap">
          <div class="sla-donut w-32 h-32 rounded-full flex items-center justify-center"
               style="background: conic-gradient(#10b981 0% 72%, #f59e0b 72% 90%, #ef4444 90% 100%);">
            <div class="w-20 h-20 rounded-full bg-white flex flex-col items-center justify-center text-center shadow-inner">
              <span class="text-xl font-black text-slate-900 leading-none">94%</span>
              <span class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 mt-0.5">Met</span>
            </div>
          </div>
        </div>
        <div class="flex justify-center gap-4 text-[10px] font-semibold">
          <span class="flex items-center gap-1.5 text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Met</span>
          <span class="flex items-center gap-1.5 text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>At risk</span>
          <span class="flex items-center gap-1.5 text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>Breached</span>
        </div>

        <!-- Meters -->
        <div class="space-y-4 text-xs pt-1">
          <div>
            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
              <span>On-time Pickup</span>
              <span class="text-emerald-600">97%</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-full w-[97%] rounded-full"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
              <span>Transit Time</span>
              <span class="text-emerald-600">92%</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-full w-[92%] rounded-full"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
              <span>Customs Clearance</span>
              <span class="text-amber-600">78%</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
              <div class="bg-amber-500 h-full w-[78%] rounded-full"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
              <span>Damage-free Delivery</span>
              <span class="text-emerald-600">99%</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-full w-[99%] rounded-full"></div>
            </div>
          </div>
        </div>
      </div>

    </section>
    <!-- ROW 3: BREACH LOG + BREACH BREAKDOWN -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- Left: Breach Log Table (8 cols) -->
      <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Breach Log</h3>
            <p class="text-xs text-slate-400">Flagged tickets from the SLA engine</p>
          </div>
          <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> 1 open
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs" id="breachLogTable">
            <thead>
              <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                <th class="pb-3">Waybill</th>
                <th class="pb-3">Commitment</th>
                <th class="pb-3">Flagged</th>
                <th class="pb-3 text-right">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">PH-WB-208712</td>
                <td class="py-4 font-semibold text-slate-800">Transit time</td>
                <td class="py-4 font-mono text-slate-600">Jul 27, 06:00</td>
                <td class="py-4 text-right"><span class="bg-red-100 text-red-700 font-semibold px-3 py-1 rounded-full text-[10px]">&#9679; Open</span></td>
              </tr>
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">PH-WB-208601</td>
                <td class="py-4 font-semibold text-slate-800">Customs clearance</td>
                <td class="py-4 font-mono text-slate-600">Jul 20, 15:30</td>
                <td class="py-4 text-right"><span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">&#9679; Resolved</span></td>
              </tr>
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">PH-WB-208588</td>
                <td class="py-4 font-semibold text-slate-800">On-time pickup</td>
                <td class="py-4 font-mono text-slate-600">Jul 18, 08:00</td>
                <td class="py-4 text-right"><span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">&#9679; Resolved</span></td>
              </tr>
              <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">PH-WB-208540</td>
                <td class="py-4 font-semibold text-slate-800">Damage-free delivery</td>
                <td class="py-4 font-mono text-slate-600">Jul 11, 11:15</td>
                <td class="py-4 text-right"><span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">&#9679; Resolved</span></td>
              </tr>
            </tbody>
          </table>
          <div id="breachEmpty" class="hidden text-center text-xs text-slate-400 py-8">No breaches match your search.</div>
        </div>
      </div>
      <!-- Right: Breach Breakdown + CTA (4 cols) -->
      <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <div>
          <h3 class="text-base font-extrabold text-slate-900">Breach Breakdown</h3>
          <p class="text-xs text-slate-400">Last 60 days by cause</p>
        </div>

        <div class="flex items-center justify-center sla-donut-wrap">
          <div class="sla-donut w-32 h-32 rounded-full flex items-center justify-center"
               style="background: conic-gradient(#f59e0b 0% 50%, #ef4444 50% 75%, #0066ff 75% 92%, #10b981 92% 100%);">
            <div class="w-20 h-20 rounded-full bg-white flex flex-col items-center justify-center text-center shadow-inner">
              <span class="text-xl font-black text-slate-900 leading-none"><?= $breaches_total ?></span>
              <span class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 mt-0.5">Breaches</span>
            </div>
          </div>
        </div>
        <div class="space-y-2.5 text-xs">
          <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Customs clearance</span><span class="font-bold text-slate-800"><?= $breach_causes[0]['count'] ?></span></div>
          <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>Transit time</span><span class="font-bold text-slate-800"><?= $breach_causes[1]['count'] ?></span></div>
          <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-brand-blue"></span>On-time pickup</span><span class="font-bold text-slate-800"><?= $breach_causes[2]['count'] ?></span></div>
          <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Damage-free</span><span class="font-bold text-slate-800"><?= $breach_causes[3]['count'] ?></span></div>
        </div>

        <!-- CTA for the open breach -->
        <a href="#" onclick="toggleChat(); return false;" class="block w-full text-center bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
          <i class="fa-solid fa-headset text-xs"></i> Raise ticket for open breach
        </a>
      </div>

    </section>

  </div>
</main>

<?php include_once '../../components/chat_widget.php'; ?>

<!-- Scripts -->
<script src="/assets/js/customer/customer_dashboard.js"></script>
<script>
  // Live search/filter for the breach log table
  function searchBreachLog() {
    var input = document.getElementById('slaSearchInput');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('breachLogTable');
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    var empty = document.getElementById('breachEmpty');
    var visible = 0;
    for (var i = 0; i < rows.length; i++) {
      var text = rows[i].textContent.toLowerCase();
      if (text.indexOf(filter) > -1) {
        rows[i].style.display = '';
        visible++;
      } else {
        rows[i].style.display = 'none';
      }
    }
    empty.classList.toggle('hidden', visible !== 0);
  }
</script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
