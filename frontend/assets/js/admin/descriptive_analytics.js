let globalRevenueData = { monthly: [], weekly: [] };
let revenueChartInstance = null;

document.addEventListener("DOMContentLoaded", async function () {
  try {
    const res = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/admin/analytics/bi`);
    const json = await res.json();
    const data = json.data;

    // 1. KPI Cards
    renderKPI("revenue", `₱${data.kpi_snapshot.total_revenue.value.toLocaleString()}`, data.kpi_snapshot.total_revenue.change_pct);
    renderKPI("quotes", data.kpi_snapshot.quotes_issued.value, data.kpi_snapshot.quotes_issued.change_pct);
    renderKPI("inquiries", data.kpi_snapshot.new_inquiries.value, data.kpi_snapshot.new_inquiries.change_pct);
    renderKPI("open", data.kpi_snapshot.open_deals.value, data.kpi_snapshot.open_deals.change_pct);

    // 2. Revenue Trend Chart (Area Gradient + Smooth Curve)
    globalRevenueData = data.revenue_trend;
    renderRevenueChart(globalRevenueData.monthly);

    // 3. Closed Won vs Lost
    renderWinLossChart(data.closed_won_vs_lost);

    // 4. Service Type Chart (Sea=Blue, Air=Sky Blue, Land=Yellow)
    renderServiceTypeChart(data.sales_by_service_type);

    // 5. Top Accounts
    renderTopAccounts(data.top_customers);

  } catch (err) {
    console.error("Fetch Error:", err);
  }
});

function renderKPI(id, val, pct) {
  document.getElementById(`kpi-${id}`).innerText = val;
  const badgeContainer = document.getElementById(`badge-${id}`);
  const isPos = pct >= 0;

  badgeContainer.innerHTML = `
    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold ${isPos ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
      ${isPos ? '↑' : '↓'} ${Math.abs(pct)}% vs last mo.
    </span>
  `;
}

function renderRevenueChart(trendData) {
  const labels = trendData.map(i => i.label);
  const values = trendData.map(i => i.revenue);

  const options = {
    chart: { 
      type: 'area', 
      height: 250, 
      toolbar: { show: false } 
    },
    stroke: { curve: 'smooth', width: 3, colors: ['#0D9488'] },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.35,
        opacityTo: 0.05,
        stops: [0, 90, 100]
      }
    },
    series: [{ name: 'Revenue', data: values }],
    xaxis: { 
      categories: labels,
      labels: { style: { colors: '#6B7280', fontSize: '12px' } }
    },
    yaxis: { 
      min: 0,
      forceNiceScale: true,
      labels: { 
        formatter: (v) => `₱${v.toLocaleString()}`,
        style: { colors: '#6B7280', fontSize: '12px' } 
      } 
    },
    markers: { size: 5, colors: ['#0D9488'], strokeColors: '#fff', strokeWidth: 2 },
    grid: { borderColor: '#F3F3EF', strokeDashArray: 4 }
  };

  if (revenueChartInstance) revenueChartInstance.destroy();
  revenueChartInstance = new ApexCharts(document.querySelector("#revenue-trend-chart"), options);
  revenueChartInstance.render();
}

function switchRevenueView(mode) {
  document.getElementById("btn-monthly").className = mode === 'monthly' 
    ? "px-3 py-1 rounded-lg bg-white text-[#1A1A1A] font-semibold transition-all" 
    : "px-3 py-1 rounded-lg text-gray-500 transition-all";
    
  document.getElementById("btn-weekly").className = mode === 'weekly' 
    ? "px-3 py-1 rounded-lg bg-white text-[#1A1A1A] font-semibold transition-all" 
    : "px-3 py-1 rounded-lg text-gray-500 transition-all";

  renderRevenueChart(mode === 'monthly' ? globalRevenueData.monthly : globalRevenueData.weekly);
}

function renderWinLossChart(data) {
  document.getElementById("won-pct-text").innerText = `${data.won_pct}%`;
  document.getElementById("lost-pct-text").innerText = `${data.lost_pct}%`;

  const options = {
    chart: { type: 'donut', height: 210 },
    series: [data.won, data.lost],
    labels: ['Won', 'Lost'],
    colors: ['#10B981', '#EF4444'],
    legend: { show: false }
  };

  new ApexCharts(document.querySelector("#win-loss-chart"), options).render();
}

function renderServiceTypeChart(services) {
  const categories = services.map(s => s.service_type);
  const revenues = services.map(s => s.revenue);

  const colors = services.map(s => {
    const st = s.service_type.toLowerCase();
    if (st.includes('sea')) return '#2563EB';   // Royal Blue
    if (st.includes('air')) return '#38BDF8';   // Sky Blue
    if (st.includes('land')) return '#EAB308';  // Yellow
    return '#6B7280';                           // Gray fallback
  });

  const options = {
    chart: { type: 'bar', height: 220, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: true, distributed: true, borderRadius: 6 } },
    colors: colors,
    series: [{ name: 'Revenue', data: revenues }],
    xaxis: { categories: categories, labels: { formatter: (v) => `₱${v.toLocaleString()}` } },
    tooltip: {
      y: { formatter: (val) => `₱${val.toLocaleString()}` }
    }
  };

  new ApexCharts(document.querySelector("#service-type-chart"), options).render();
}

function renderTopAccounts(accounts) {
  const container = document.getElementById("top-accounts-container");
  if (!accounts || accounts.length === 0) {
    container.innerHTML = `<p class="text-xs text-gray-400 text-center py-4">No top accounts found</p>`;
    return;
  }

  container.innerHTML = accounts.map((acc, i) => `
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-3 min-w-[160px]">
        <span class="w-7 h-7 rounded-lg bg-[#F3F3EF] border border-[#E5E5DF] text-xs font-bold flex items-center justify-center">
          #${i + 1}
        </span>
        <div>
          <h5 class="text-xs font-bold text-[#1A1A1A]">${acc.company_name}</h5>
          <span class="text-[11px] text-gray-400">${acc.service_type}</span>
        </div>
      </div>
      <div class="flex-1 bg-[#F0F0EC] h-2 rounded-full overflow-hidden">
        <div class="bg-[#0D9488] h-full rounded-full" style="width: ${acc.bar_width}%"></div>
      </div>
      <span class="text-xs font-bold text-[#1A1A1A]">₱${acc.revenue.toLocaleString()}</span>
    </div>
  `).join("");
}