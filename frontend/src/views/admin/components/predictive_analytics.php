<div class="space-y-6 p-6 bg-slate-50 text-slate-800 min-h-screen">

  <!-- TOP ROW: Revenue Forecast & Seasonal Sales Projections -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- 1. Revenue Forecast (Time Series Chart: Actual Lime vs Forecast Blue) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-lime-500 inline-block"></span>
            Revenue Forecast
          </h3>
          <span id="revenue-trend-badge" class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
            Trend: --
          </span>
        </div>
        <p class="text-xs text-slate-500 mb-4">Actual revenue history kumpara sa AI-predicted future revenue (Next Month & Quarter)</p>
        
        <!-- Summary Metric Badges -->
        <div class="grid grid-cols-2 gap-3 mb-4">
          <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
            <span class="text-[11px] text-slate-500 block">Next Month Forecast</span>
            <span id="forecast-next-month" class="text-lg font-bold text-blue-600">₱0.00</span>
          </div>
          <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
            <span class="text-[11px] text-slate-500 block">Next Quarter Forecast</span>
            <span id="forecast-next-quarter" class="text-lg font-bold text-indigo-600">₱0.00</span>
          </div>
        </div>

        <div id="chart-revenue-forecast" class="w-full min-h-[280px]"></div>
      </div>
    </div>

    <!-- 2. Seasonal Sales Projection (Time Series Chart: Yellow) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-2">
          <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
            Seasonal Sales Projection
          </h3>
          <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full font-medium">Historical Pattern</span>
        </div>
        <p class="text-xs text-slate-500 mb-4">Buwanang average revenue index at expected demand cycle</p>
        
        <div id="chart-seasonal-sales" class="w-full min-h-[340px]"></div>
      </div>
    </div>

  </div>

  <!-- MIDDLE ROW: Lead Conversion Probability Score & Capacity vs Demand -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- 3. Lead Conversion Probability Score (Progress Bars) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-1">
        <h3 class="text-lg font-semibold text-slate-900">Lead Conversion Probability</h3>
        <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">AI Scoring</span>
      </div>
      <p class="text-xs text-slate-500 mb-5">Predicted likelihood ng bawat active lead na ma-close bilang customer</p>
      
      <div id="lead-conversion-container" class="space-y-3.5 max-h-[340px] overflow-y-auto pr-2 custom-scrollbar">
        <!-- Dynamic Progress Bars -->
      </div>
    </div>

    <!-- 4. Capacity vs Demand Forecast (Bar Chart: Current Grey vs Projected Blue) -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-1">
        <h3 class="text-lg font-semibold text-slate-900">Capacity vs Demand Forecast</h3>
        <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">Volume Projection</span>
      </div>
      <p class="text-xs text-slate-500 mb-4">Inaasahang shipment volume at route capacity utilization</p>
      
      <div id="chart-capacity-demand" class="w-full min-h-[320px]"></div>
    </div>

  </div>

  <!-- BOTTOM ROW: Customer Churn Prediction (Risk Assessment Table with Status Badges) -->
  <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
          Customer Churn Risk Prediction
        </h3>
        <p class="text-xs text-slate-500">Kliyenteng nanganganib na lumipat o huminto sa pag-order batay sa huling aktibidad</p>
      </div>
      <span class="text-xs bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-full font-medium">Risk Assessment</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm text-slate-700">
        <thead class="bg-slate-100 text-xs uppercase text-slate-500 border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Company Name</th>
            <th class="py-3 px-4 text-center">Total Orders</th>
            <th class="py-3 px-4 text-right">Total Spent</th>
            <th class="py-3 px-4 text-center">Days Inactive</th>
            <th class="py-3 px-4">Churn Risk Score</th>
            <th class="py-3 px-4 text-center">Risk Status</th>
          </tr>
        </thead>
        <tbody id="churn-prediction-table-body" class="divide-y divide-slate-100">
          <!-- Dynamic Churn Rows -->
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="../../../../assets/js/admin/predictive_analytics.js"></script>