document.addEventListener("DOMContentLoaded", () => {
  fetchPredictiveAnalytics();
});

let revenueChartInstance = null;
let seasonalChartInstance = null;
let capacityChartInstance = null;

async function fetchPredictiveAnalytics() {
  try {
    const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/admin/analytics/bi/predictive`);
    const json = await response.json();

    if (json.status === "success" && json.data) {
      const data = json.data;

      renderRevenueForecastChart(data.revenue_forecast || {});
      renderSeasonalSalesChart(data.seasonal_sales_projections || []);
      renderLeadConversionScores(data.lead_conversion_scores || []);
      renderCapacityDemandChart(data.capacity_demand_forecast || []);
      renderCustomerChurnTable(data.customer_churn_predictions || []);
    }
  } catch (error) {
    console.error("Error fetching predictive analytics:", error);
  }
}

// 1. REVENUE FORECAST CHART (Lime for Actual, Blue for Forecast)
function renderRevenueForecastChart(revenueData) {
  const history = revenueData.historical_monthly || [];
  const nextMonthVal = revenueData.next_month || 0;
  const nextQuarterVal = revenueData.next_quarter || 0;

  document.getElementById("forecast-next-month").innerText = `₱${nextMonthVal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
  document.getElementById("forecast-next-quarter").innerText = `₱${nextQuarterVal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
  
  const trendBadge = document.getElementById("revenue-trend-badge");
  const trend = revenueData.trend || "Stable";
  trendBadge.innerText = `Trend: ${trend}`;
  if (trend === "Upward") {
    trendBadge.className = "text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200";
  } else if (trend === "Downward") {
    trendBadge.className = "text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200";
  } else {
    trendBadge.className = "text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200";
  }

  // Build Time Series Data
  const categories = history.map((h) => h.month);
  const actualSeries = history.map((h) => h.revenue);
  
  // Forecast extension line (Idugtong ang huling actual point sa forecast point)
  const forecastSeries = new Array(actualSeries.length - 1).fill(null);
  if (actualSeries.length > 0) {
    forecastSeries.push(actualSeries[actualSeries.length - 1]);
    forecastSeries.push(nextMonthVal);
    categories.push("Next Month (Forecast)");
  }

  const options = {
    series: [
      { name: "Actual Revenue", data: actualSeries },
      { name: "Forecast Revenue", data: forecastSeries }
    ],
    chart: { type: "line", height: 280, toolbar: { show: false } },
    // Actual: Lime (#84CC16), Forecast: Blue (#2563EB)
    colors: ["#84CC16", "#2563EB"],
    stroke: { width: [3, 3], curve: "smooth", dashArray: [0, 5] },
    markers: { size: 5, strokeWidth: 0 },
    xaxis: {
      categories: categories,
      labels: { style: { colors: "#64748B", fontWeight: 500 } },
      axisBorder: { color: "#E2E8F0" }
    },
    yaxis: {
      labels: {
        style: { colors: "#64748B" },
        formatter: (val) => `₱${val.toLocaleString()}`
      }
    },
    grid: { borderColor: "#F1F5F9" },
    legend: { position: "top", labels: { colors: "#475569" } },
    tooltip: { theme: "light" }
  };

  if (revenueChartInstance) revenueChartInstance.destroy();
  revenueChartInstance = new ApexCharts(document.querySelector("#chart-revenue-forecast"), options);
  revenueChartInstance.render();
}

// 2. SEASONAL SALES PROJECTION CHART (Yellow Time Series)
function renderSeasonalSalesChart(projections) {
  const categories = projections.map((p) => p.month);
  const revenues = projections.map((p) => p.historical_avg_revenue);

  const options = {
    series: [{ name: "Avg Revenue", data: revenues }],
    chart: { type: "area", height: 320, toolbar: { show: false } },
    // Yellow (#EAB308)
    colors: ["#EAB308"],
    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.45,
        opacityTo: 0.05,
        stops: [0, 90, 100]
      }
    },
    stroke: { width: 3, curve: "smooth" },
    markers: { size: 4 },
    xaxis: {
      categories: categories,
      labels: { style: { colors: "#64748B" } },
      axisBorder: { color: "#E2E8F0" }
    },
    yaxis: {
      labels: {
        style: { colors: "#64748B" },
        formatter: (val) => `₱${val.toLocaleString()}`
      }
    },
    grid: { borderColor: "#F1F5F9" },
    tooltip: { theme: "light" }
  };

  if (seasonalChartInstance) seasonalChartInstance.destroy();
  seasonalChartInstance = new ApexCharts(document.querySelector("#chart-seasonal-sales"), options);
  seasonalChartInstance.render();
}

// 3. LEAD CONVERSION PROBABILITY SCORE (Progress Bars)
function renderLeadConversionScores(scores) {
  const container = document.getElementById("lead-conversion-container");
  container.innerHTML = "";

  if (!scores.length) {
    container.innerHTML = `<p class="text-xs text-slate-400 italic">Walang active lead data.</p>`;
    return;
  }

  scores.forEach((lead) => {
    const prob = lead.conversion_probability || 0;
    
    // Bar color batay sa scoring
    let barGradient = "from-rose-500 to-amber-500";
    let scoreColor = "text-rose-600";
    if (prob >= 70) {
      barGradient = "from-emerald-400 to-teal-500";
      scoreColor = "text-emerald-600";
    } else if (prob >= 40) {
      barGradient = "from-amber-400 to-indigo-500";
      scoreColor = "text-indigo-600";
    }

    const html = `
      <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80">
        <div class="flex justify-between items-center text-xs mb-1">
          <span class="font-semibold text-slate-800">${lead.company_name}</span>
          <span class="font-bold ${scoreColor}">${prob}% Likelihood</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
          <div class="bg-gradient-to-r ${barGradient} h-2.5 rounded-full" style="width: ${Math.min(prob, 100)}%"></div>
        </div>
        <div class="flex justify-between items-center text-[10px] text-slate-500 mt-1">
          <span>Platform: <strong>${lead.platform}</strong></span>
          <span>Service: <strong>${lead.service_type}</strong></span>
        </div>
      </div>
    `;
    container.innerHTML += html;
  });
}

// 4. CAPACITY VS DEMAND FORECAST CHART (Current Grey vs Projected Blue)
function renderCapacityDemandChart(demandData) {
  const categories = demandData.map((d) => d.route);
  const currentVol = demandData.map((d) => d.current_volume);
  const projectedVol = demandData.map((d) => d.expected_volume_next_month);

  const options = {
    series: [
      { name: "Current Volume", data: currentVol },
      { name: "Projected Demand", data: projectedVol }
    ],
    chart: { type: "bar", height: 320, toolbar: { show: false } },
    // Current: Grey (#94A3B8), Projected: Blue (#2563EB)
    colors: ["#94A3B8", "#2563EB"],
    plotOptions: {
      bar: { horizontal: false, columnWidth: "45%", borderRadius: 4 }
    },
    dataLabels: { enabled: false },
    xaxis: {
      categories: categories,
      labels: { style: { colors: "#64748B", fontSize: "11px" } },
      axisBorder: { color: "#E2E8F0" }
    },
    yaxis: { labels: { style: { colors: "#64748B" } } },
    grid: { borderColor: "#F1F5F9" },
    legend: { position: "top", labels: { colors: "#475569" } },
    tooltip: { theme: "light" }
  };

  if (capacityChartInstance) capacityChartInstance.destroy();
  capacityChartInstance = new ApexCharts(document.querySelector("#chart-capacity-demand"), options);
  capacityChartInstance.render();
}

// 5. CUSTOMER CHURN PREDICTION TABLE
function renderCustomerChurnTable(churnList) {
  const tbody = document.getElementById("churn-prediction-table-body");
  tbody.innerHTML = "";

  if (!churnList.length) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-xs text-slate-400">Walang customer churn risk data.</td></tr>`;
    return;
  }

  churnList.forEach((item) => {
    const isHigh = item.risk_level === "High Risk";
    const isMed = item.risk_level === "Medium Risk";

    const badgeStyle = isHigh
      ? "bg-rose-50 text-rose-700 border-rose-200"
      : isMed
      ? "bg-amber-50 text-amber-700 border-amber-200"
      : "bg-emerald-50 text-emerald-700 border-emerald-200";

    const progressBg = isHigh ? "bg-rose-500" : isMed ? "bg-amber-500" : "bg-emerald-500";

    const row = `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="py-3 px-4 font-medium text-slate-800">${item.company_name}</td>
        <td class="py-3 px-4 text-center text-slate-700 font-medium">${item.total_orders}</td>
        <td class="py-3 px-4 text-right font-bold text-slate-800">₱${item.total_spent.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
        <td class="py-3 px-4 text-center font-semibold text-slate-600">${item.days_since_last_order} days</td>
        <td class="py-3 px-4">
          <div class="flex items-center gap-2">
            <div class="w-full bg-slate-200 rounded-full h-2">
              <div class="${progressBg} h-2 rounded-full" style="width: ${Math.min(item.churn_risk_score, 100)}%"></div>
            </div>
            <span class="text-xs font-bold text-slate-700 w-10">${item.churn_risk_score}%</span>
          </div>
        </td>
        <td class="py-3 px-4 text-center">
          <span class="text-xs font-semibold px-2.5 py-1 rounded-full border ${badgeStyle}">
            ${item.risk_level}
          </span>
        </td>
      </tr>
    `;
    tbody.innerHTML += row;
  });
}