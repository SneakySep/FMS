<div class="bg-[#F7F7F2] p-8 rounded-3xl mb-8 font-sans text-[#1A1A1A]">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 items-center">

    <!-- CARD 1: NEW LEADS (BAR CHART) -->
    <div class="lg:col-span-4 flex flex-col justify-between h-44">
      <h3 class="text-xl font-medium tracking-tight text-[#1A1A1A]">New leads</h3>
      <div id="newLeadsApexChart" class="w-full h-32 -ml-2"></div>
    </div>

    <!-- CARD 2: SUCCESSFUL DEALS (CUSTOM TICK GAUGE) -->
    <div class="lg:col-span-4 flex flex-col items-center justify-center relative h-44">
      <div class="relative w-full max-w-[260px] flex flex-col items-center justify-center">
        <!-- SVG Ticks Gauge -->
        <svg class="w-full h-auto overflow-visible" viewBox="0 0 200 110">
          <g id="gauge-ticks"></g>
        </svg>
        <!-- Center Text -->
        <div class="absolute bottom-2 text-center">
          <span id="kpi-success-rate" class="text-4xl font-semibold tracking-tight block text-[#1A1A1A]">0%</span>
          <span class="text-xs text-[#666666] font-normal mt-1 block">Successful deals</span>
        </div>
      </div>
    </div>

    <!-- CARD 3: TASKS / FOLLOW-UPS IN PROGRESS -->
    <div class="lg:col-span-2 flex flex-col justify-between h-44 py-2 border-l border-[#E5E5DF] lg:border-l-0 lg:pl-0 pl-4">
      <div id="kpi-followups" class="text-5xl font-normal tracking-tight text-[#1A1A1A]">0</div>
      <div class="flex items-center justify-between pr-2">
        <span class="text-xs text-[#666666] leading-snug max-w-[90px]">Tasks<br>in progress</span>
        <svg class="w-4 h-4 text-[#1A1A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </div>
    </div>

    <!-- CARD 4: PREPAYMENTS FROM CUSTOMERS -->
    <div class="lg:col-span-2 flex flex-col justify-between h-44 py-2 border-l border-[#E5E5DF] lg:border-l-0 lg:pl-0 pl-4">
      <div id="kpi-prepayments" class="text-4xl font-normal tracking-tight text-[#1A1A1A]">₱ 0</div>
      <div class="flex items-center justify-between pr-2">
        <span class="text-xs text-[#666666] leading-snug max-w-[110px]">Prepayments<br>from customers</span>
        <svg class="w-4 h-4 text-[#1A1A1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </div>
    </div>

  </div>
</div>

<!-- LOAD JS SERVICE FOR KPI -->
<script src="../../../../../assets/js/admin/kpi_charts.js"></script>