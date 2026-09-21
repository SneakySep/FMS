<div class="space-y-6 p-6 bg-slate-50 text-slate-800 min-h-screen">
  
  <!-- TOP ROW: Win Rate & Lost Deals -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Win Rate by Routes (Bar Chart) -->
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-sky-400 inline-block"></span>
          Win Rate by Route & Service
        </h3>
        <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200 font-medium">Bookings Count</span>
      </div>
      <div id="chart-win-rate-route" class="w-full min-h-[320px]"></div>
    </div>

    <!-- Lost Deal Breakdown (Donut Chart & AI Summary) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
      <div>
        <h3 class="text-lg font-semibold text-slate-900 mb-1 flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
          Lost Deal Breakdown
        </h3>
        <p class="text-xs text-slate-500 mb-4">NLP Analysis mula sa Sales Notes</p>
        <div id="chart-lost-deals" class="w-full flex justify-center min-h-[220px]"></div>
      </div>

      <!-- AI Summary Box -->
      <div class="mt-4 p-3 bg-indigo-50/60 border border-indigo-100 rounded-lg">
        <div class="flex items-center gap-2 text-indigo-700 text-xs font-semibold mb-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          AI Diagnostic Insight
        </div>
        <p id="lost-deal-ai-summary" class="text-xs text-slate-600 italic">Nag-lo-load ng AI summary...</p>
      </div>
    </div>
  </div>

  <!-- MIDDLE ROW: Lead Source Effectiveness & Deal Aging -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Lead Source Effectiveness (Progress Bars) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <h3 class="text-lg font-semibold text-slate-900 mb-1">Platform / Lead Source Quality</h3>
      <p class="text-xs text-slate-500 mb-5">Scikit-Learn ML Quality Score vs Actual Win Rate</p>
      <div id="lead-source-container" class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
        <!-- Dynamic Progress Bars -->
      </div>
    </div>

    <!-- Deal Aging Report (Bar Chart) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-semibold text-slate-900">Deal Aging Report</h3>
        <span id="avg-days-label" class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">
          Avg: 0 days
        </span>
      </div>
      <p class="text-xs text-slate-500 mb-3">Distrubusyon ng bilang ng araw bago ma-close ang deal</p>
      <div id="chart-deal-aging" class="w-full min-h-[250px]"></div>
    </div>
  </div>

  <!-- BOTTOM ROW: Customer Segment Performance Table -->
  <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-semibold text-slate-900">Customer Segment Performance</h3>
        <p class="text-xs text-slate-500">Pangunahing kliyente, kanilang binibiling service type, at kabuuang revenue</p>
      </div>
      <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full font-medium">Active Accounts</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm text-slate-700">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500 border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Company Name</th>
            <th class="py-3 px-4">Service Type</th>
            <th class="py-3 px-4">Primary Route</th>
            <th class="py-3 px-4 text-center">Total Orders</th>
            <th class="py-3 px-4 text-right">Total Revenue</th>
          </tr>
        </thead>
        <tbody id="customer-segment-table-body" class="divide-y divide-slate-100">
          <!-- Dynamic Table Rows -->
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="../../../../assets/js/admin/diagnostic_analytics.js"></script>