document.addEventListener("DOMContentLoaded", () => {
  fetchDiagnosticAnalytics();
});

let routeChartInstance = null;
let lostChartInstance = null;
let agingChartInstance = null;

async function fetchDiagnosticAnalytics() {
  try {
    const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/admin/analytics/bi/diagnostic`);
    const json = await response.json();

    if (json.status === "success" && json.data) {
      const data = json.data;

      renderWinRateRouteChart(data.win_rate_by_route || []);
      renderLostDealsChart(data.lost_deal_analysis || {});
      renderLeadSourceProgress(data.lead_source_effectiveness || []);
      renderDealAgingChart(data.deal_aging_report || {});
      renderCustomerSegmentTable(data.customer_segment_performance || []);
    }
  } catch (error) {
    console.error("Error fetching diagnostic analytics:", error);
  }
}

// 1. BAR CHART: Win Rate by Route & Service Type (Light Theme + Proper Colors)
function renderWinRateRouteChart(routes) {
  const categories = routes.map((r) => r.service_type);
  const bookings = routes.map((r) => r.total_booked);

  // Inayos na Color Rules:
  // Air -> Sky Blue (#38BDF8)
  // Sea -> Blue (#2563EB)
  // Land -> Yellow (#EAB308)
  const getServiceColor = (type) => {
    const name = (type || "").toLowerCase();
    if (name.includes("air")) return "#38BDF8";   // Sky Blue
    if (name.includes("sea")) return "#2563EB";   // Blue
    if (name.includes("land")) return "#EAB308";  // Yellow
    return "#8B5CF6";                            // Default Purple
  };

  const colors = routes.map((r) => getServiceColor(r.service_type));

  const options = {
    series: [{ name: "Total Bookings", data: bookings }],
    chart: { type: "bar", height: 320, toolbar: { show: false } },
    colors: colors,
    plotOptions: {
      bar: { distributed: true, borderRadius: 6, columnWidth: "40%" },
    },
    dataLabels: { enabled: true, style: { colors: ["#ffffff"] } },
    legend: { show: false },
    xaxis: {
      categories: categories,
      labels: { style: { colors: "#64748B", fontWeight: 500 } },
      axisBorder: { color: "#E2E8F0" },
    },
    yaxis: { labels: { style: { colors: "#64748B" } } },
    grid: { borderColor: "#F1F5F9" },
    tooltip: {
      theme: "light",
      custom: function ({ seriesIndex, dataPointIndex, w }) {
        const item = routes[dataPointIndex];
        return `
          <div class="p-3 bg-white border border-slate-200 rounded shadow-md text-xs">
            <div class="font-bold text-slate-800 mb-1">${item.service_type} Service</div>
            <div class="text-slate-600"><strong>Route:</strong> ${item.route}</div>
            <div class="text-slate-600"><strong>Bookings:</strong> ${item.total_booked}</div>
            <div class="text-emerald-600 font-semibold"><strong>Revenue:</strong> ₱${item.revenue.toLocaleString()}</div>
          </div>
        `;
      },
    },
  };

  if (routeChartInstance) routeChartInstance.destroy();
  routeChartInstance = new ApexCharts(document.querySelector("#chart-win-rate-route"), options);
  routeChartInstance.render();
}

// 2. DONUT CHART: Lost Deal Reasons
function renderLostDealsChart(lostData) {
  const breakdown = lostData.reasons_breakdown || [];
  const labels = breakdown.map((b) => b.reason);
  const series = breakdown.map((b) => b.count);

  document.getElementById("lost-deal-ai-summary").innerText = lostData.ai_summary || "Walang datos.";

  const options = {
    series: series.length ? series : [1],
    labels: labels.length ? labels : ["No Lost Deals"],
    chart: { type: "donut", height: 240 },
    colors: ["#F43F5E", "#FB923C", "#FBBF24", "#A855F7", "#64748B"],
    legend: { position: "bottom", labels: { colors: "#475569" }, fontSize: "11px" },
    stroke: { colors: ["#ffffff"] },
    dataLabels: { enabled: false },
    tooltip: { theme: "light" },
  };

  if (lostChartInstance) lostChartInstance.destroy();
  lostChartInstance = new ApexCharts(document.querySelector("#chart-lost-deals"), options);
  lostChartInstance.render();
}

// 3. PROGRESS BARS: Lead Source Effectiveness
function renderLeadSourceProgress(sources) {
  const container = document.getElementById("lead-source-container");
  container.innerHTML = "";

  if (!sources.length) {
    container.innerHTML = `<p class="text-xs text-slate-400 italic">Walang lead source data.</p>`;
    return;
  }

  sources.forEach((item) => {
    const html = `
      <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200/80">
        <div class="flex justify-between items-center text-xs mb-1.5">
          <span class="font-medium text-slate-800">${item.source}</span>
          <span class="text-slate-500">ML Quality: <strong class="text-indigo-600">${item.quality_score}%</strong> | Win Rate: <strong class="text-emerald-600">${item.win_rate}%</strong></span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
          <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-2.5 rounded-full" style="width: ${Math.min(item.quality_score, 100)}%"></div>
        </div>
        <div class="flex justify-between items-center text-[11px] text-slate-500 mt-1.5">
          <span>Won: ${item.won_leads} / Total: ${item.total_leads}</span>
        </div>
      </div>
    `;
    container.innerHTML += html;
  });
}

// 4. BAR CHART: Deal Aging Report
function renderDealAgingChart(agingReport) {
  const brackets = agingReport.aging_brackets || {};
  document.getElementById("avg-days-label").innerText = `Avg: ${agingReport.avg_days_to_close || 0} days`;

  const categories = ["0-7 Days", "8-14 Days", "15-30 Days", "30+ Days"];
  const counts = [
    brackets["0-7_days"] || 0,
    brackets["8-14_days"] || 0,
    brackets["15-30_days"] || 0,
    brackets["30+_days"] || 0,
  ];

  const options = {
    series: [{ name: "Deals Closed", data: counts }],
    chart: { type: "bar", height: 250, toolbar: { show: false } },
    colors: ["#10B981", "#3B82F6", "#F59E0B", "#EF4444"],
    plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: "35%" } },
    legend: { show: false },
    xaxis: { categories: categories, labels: { style: { colors: "#64748B" } }, axisBorder: { color: "#E2E8F0" } },
    yaxis: { labels: { style: { colors: "#64748B" } } },
    grid: { borderColor: "#F1F5F9" },
    tooltip: { theme: "light" },
  };

  if (agingChartInstance) agingChartInstance.destroy();
  agingChartInstance = new ApexCharts(document.querySelector("#chart-deal-aging"), options);
  agingChartInstance.render();
}

// 5. TABLE: Customer Segment Performance
function renderCustomerSegmentTable(segments) {
  const tbody = document.getElementById("customer-segment-table-body");
  tbody.innerHTML = "";

  if (!segments.length) {
    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-xs text-slate-400">Walang customer segment data.</td></tr>`;
    return;
  }

  segments.forEach((cust) => {
    const isAir = cust.service_type.toLowerCase().includes("air");
    const isSea = cust.service_type.toLowerCase().includes("sea");

    const row = `
      <tr class="hover:bg-slate-50 transition-colors">
        <td class="py-3 px-4 font-medium text-slate-800">${cust.company_name}</td>
        <td class="py-3 px-4">
          <span class="text-xs font-semibold px-2.5 py-1 rounded border ${
            isAir ? "bg-sky-50 text-sky-700 border-sky-200" :
            isSea ? "bg-blue-50 text-blue-700 border-blue-200" :
            "bg-amber-50 text-amber-700 border-amber-200"
          }">
            ${cust.service_type}
          </span>
        </td>
        <td class="py-3 px-4 text-slate-600">${cust.route}</td>
        <td class="py-3 px-4 text-center font-semibold text-slate-800">${cust.total_orders}</td>
        <td class="py-3 px-4 text-right font-bold text-emerald-600">₱${cust.revenue.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
      </tr>
    `;
    tbody.innerHTML += row;
  });
}