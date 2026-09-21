<div class="space-y-6 font-sans text-[#1A1A1A]">

  <!-- KPI CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Total Revenue -->
    <div class="bg-white p-5 rounded-2xl border border-[#E5E5DF] shadow-sm flex flex-col justify-between">
      <span class="text-sm font-medium text-gray-500">Total revenue</span>
      <h3 id="kpi-revenue" class="text-3xl font-extrabold text-[#1A1A1A] mt-2 mb-3">₱0</h3>
      <div id="badge-revenue"></div>
    </div>

    <!-- Quotes Issued -->
    <div class="bg-white p-5 rounded-2xl border border-[#E5E5DF] shadow-sm flex flex-col justify-between">
      <span class="text-sm font-medium text-gray-500">Quotes issued</span>
      <h3 id="kpi-quotes" class="text-3xl font-extrabold text-[#1A1A1A] mt-2 mb-3">0</h3>
      <div id="badge-quotes"></div>
    </div>

    <!-- New Inquiries -->
    <div class="bg-white p-5 rounded-2xl border border-[#E5E5DF] shadow-sm flex flex-col justify-between">
      <span class="text-sm font-medium text-gray-500">New inquiries</span>
      <h3 id="kpi-inquiries" class="text-3xl font-extrabold text-[#1A1A1A] mt-2 mb-3">0</h3>
      <div id="badge-inquiries"></div>
    </div>

    <!-- Open Deals -->
    <div class="bg-white p-5 rounded-2xl border border-[#E5E5DF] shadow-sm flex flex-col justify-between">
      <span class="text-sm font-medium text-gray-500">Open deals</span>
      <h3 id="kpi-open" class="text-3xl font-extrabold text-[#1A1A1A] mt-2 mb-3">0</h3>
      <div id="badge-open"></div>
    </div>

  </div>

  <!-- REVENUE TREND & CLOSED WON VS LOST -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Revenue Trend Chart -->
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-[#E5E5DF] shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h4 class="text-base font-bold text-[#1A1A1A]">Revenue trend</h4>
          <p class="text-xs text-gray-400">Gross booked freight revenue from tickets</p>
        </div>
        <div class="inline-flex p-1 bg-[#F3F3EF] rounded-xl border border-[#E5E5DF] text-xs font-medium">
          <button id="btn-monthly" onclick="switchRevenueView('monthly')" class="px-3 py-1 rounded-lg bg-white text-[#1A1A1A] font-semibold transition-all">Monthly</button>
          <button id="btn-weekly" onclick="switchRevenueView('weekly')" class="px-3 py-1 rounded-lg text-gray-500 transition-all">Weekly</button>
        </div>
      </div>
      <div id="revenue-trend-chart" class="w-full h-64"></div>
    </div>

    <!-- Closed Won vs Lost Chart -->
    <div class="bg-white p-6 rounded-2xl border border-[#E5E5DF] shadow-sm flex flex-col justify-between">
      <div>
        <h4 class="text-base font-bold text-[#1A1A1A]">Closed won vs. lost</h4>
        <p class="text-xs text-gray-400">Inquiries conversion ratio</p>
      </div>
      <div id="win-loss-chart" class="w-full flex justify-center py-2"></div>
      <div class="flex items-center justify-center gap-6 text-xs font-medium border-t border-[#F0F0EC] pt-4">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
          <span>Won · <strong id="won-pct-text">0%</strong></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span>
          <span>Lost · <strong id="lost-pct-text">0%</strong></span>
        </div>
      </div>
    </div>
  </div>

  <!-- SERVICE TYPE & TOP CUSTOMERS -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Sales by Service Type -->
    <div class="bg-white p-6 rounded-2xl border border-[#E5E5DF] shadow-sm">
      <h4 class="text-base font-bold text-[#1A1A1A]">Sales by service type</h4>
      <p class="text-xs text-gray-400 mb-4">Share of booked revenue</p>
      <div id="service-type-chart" class="w-full h-56"></div>
    </div>

    <!-- Top Accounts -->
    <div class="bg-white p-6 rounded-2xl border border-[#E5E5DF] shadow-sm">
      <h4 class="text-base font-bold text-[#1A1A1A]">Top accounts by revenue</h4>
      <p class="text-xs text-gray-400 mb-4">All-time closed tickets revenue</p>
      <div id="top-accounts-container" class="space-y-4"></div>
    </div>
  </div>

</div>
<!-- JS SCRIPT ATTACHMENT -->
<script src="../../../../../assets/js/admin/descriptive_analytics.js"></script>