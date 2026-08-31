<!-- ROW 2: SALES PERFORMANCE SECTION -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

  <!-- LEFT COLUMN: Revenue Trend Chart (Larger) -->
  <div class="lg:col-span-2">
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm h-full">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-chart-line text-indigo-600 text-sm"></i>
          <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Sales Performance</h2>
        </div>
        <a href="analytics.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
          <i class="fa-solid fa-arrow-right text-[10px]"></i> Detailed Report
        </a>
      </div>
      <p class="text-[11px] text-slate-400 mb-4">Daily lead activity trend (last 7 days)</p>

      <!-- Bar Chart Canvas -->
      <div id="admin-revenue-chart" class="w-full h-[220px]"></div>
        </div>
  </div>

  <!-- RIGHT COLUMN: Lead Funnel -->
  <div class="lg:col-span-1">
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm h-full">
      <div class="flex items-center gap-2 mb-4">
        <i class="fa-solid fa-funnel text-purple-600 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Lead Pipeline Funnel</h2>
      </div>
      <p class="text-[11px] text-slate-400 mb-4">Current pipeline distribution</p>

      <div class="space-y-3">

        <!-- Won (MTD) -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
              <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
              Closed Won
            </span>
            <span class="text-xs font-bold text-slate-900"><?= $closed_won ?></span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2">
            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                 style="width: <?= $total_leads > 0 ? round(($closed_won / max(1, $total_leads)) * 100) : 0 ?>%"></div>
          </div>
        </div>

        <!-- Negotiation -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-amber-700 flex items-center gap-1.5">
              <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
              Negotiation
            </span>
            <span class="text-xs font-bold text-slate-900"><?= $negotiation ?></span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2">
            <div class="bg-amber-500 h-2 rounded-full transition-all duration-500"
                 style="width: <?= $total_leads > 0 ? round(($negotiation / max(1, $total_leads)) * 100) : 0 ?>%"></div>
          </div>
        </div>

        <!-- Quote Sent -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-indigo-700 flex items-center gap-1.5">
              <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
              Quote Sent
            </span>
            <span class="text-xs font-bold text-slate-900"><?= $quote_sent ?></span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2">
            <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500"
                 style="width: <?= $total_leads > 0 ? round(($quote_sent / max(1, $total_leads)) * 100) : 0 ?>%"></div>
          </div>
        </div>

        <!-- Qualifying -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-yellow-700 flex items-center gap-1.5">
              <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
              Qualifying
            </span>
            <span class="text-xs font-bold text-slate-900"><?= $qualifying ?></span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2">
            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500"
                 style="width: <?= $total_leads > 0 ? round(($qualifying / max(1, $total_leads)) * 100) : 0 ?>%"></div>
          </div>
        </div>

        <!-- New Inquiry -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-bold text-violet-700 flex items-center gap-1.5">
              <span class="w-2 h-2 bg-violet-500 rounded-full"></span>
              New Inquiry
            </span>
            <span class="text-xs font-bold text-slate-900"><?= $new_inquiry ?></span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-2">
            <div class="bg-violet-500 h-2 rounded-full transition-all duration-500"
                 style="width: <?= $total_leads > 0 ? round(($new_inquiry / max(1, $total_leads)) * 100) : 0 ?>%"></div>
          </div>
        </div>

      </div>

      <!-- Summary -->
      <div class="mt-4 pt-3 border-t border-slate-100">
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-500">Total Leads</span>
          <span class="font-bold text-slate-900"><?= $total_leads ?></span>
        </div>
        <div class="flex items-center justify-between text-xs mt-1">
          <span class="text-slate-500">Conversion Rate</span>
          <span class="font-bold text-emerald-600">
            <?= $total_leads > 0 ? round(($closed_won / $total_leads) * 100, 1) : 0 ?>%
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Full-Width Lead Conversion Bar -->
  <div class="lg:col-span-3">
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-users-gear text-rose-600 text-sm"></i>
          <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Closed-Won Account Provisioning</h2>
        </div>
        <a href="tickets.php" class="text-xs font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1 transition-colors">
          Manage Tickets <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>
      <p class="text-[11px] text-slate-400 mb-4">
        <?= $ticket_count ?> closed-won tickets pending customer portal account creation
      </p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Pending Tickets Card -->
        <div class="bg-rose-50/50 border border-rose-200/50 rounded-xl p-4 text-center">
          <div class="text-3xl font-bold text-rose-600 mb-1"><?= $ticket_count ?></div>
          <p class="text-[10px] font-bold text-rose-700 uppercase tracking-wider">Pending</p>
        </div>

        <!-- Created Customers Card -->
        <div class="bg-emerald-50/50 border border-emerald-200/50 rounded-xl p-4 text-center">
          <div class="text-3xl font-bold text-emerald-600 mb-1"><?= $customers_mtd ?></div>
          <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Created This Month</p>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-indigo-50/50 border border-indigo-200/50 rounded-xl p-4 text-center">
          <div class="text-2xl font-bold text-indigo-600 mb-1">â‚±<?= number_format($revenue_current, 0) ?></div>
          <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider">Revenue This Month</p>
        </div>
      </div>
    </div>
  </div>
</div>
