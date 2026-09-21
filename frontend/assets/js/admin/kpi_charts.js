document.addEventListener("DOMContentLoaded", async function () {
  const API_ENDPOINT = `${window.APP_CONFIG.API_BASE_URL}/api/v1/admin/analytics/cards`;

  try {
    const response = await fetch(API_ENDPOINT);
    if (!response.ok) throw new Error(`HTTP Error Status: ${response.status}`);

    const json = await response.json();
    const data = json.data;

    // 1. UPDATE VALUES
    document.getElementById("kpi-followups").innerText = data.followups_needed;
    document.getElementById("kpi-prepayments").innerText = ` ₱ ${data.prepayments.amount.toLocaleString('de-DE', { minimumFractionDigits: 3 })}`;
    
    const rate = Math.round(data.successful_deals.rate);
    document.getElementById("kpi-success-rate").innerText = `${rate}%`;

    // 2. RENDER MONOCHROME BAR CHART
    renderNewLeadsChart(data.new_leads_chart.labels, data.new_leads_chart.counts);

    // 3. RENDER CUSTOM TICK GAUGE
    renderTickGauge(rate);

  } catch (error) {
    console.error("Error loading KPI cards:", error);
  }
});

function renderNewLeadsChart(labels, counts) {
  const options = {
    series: [{
      name: 'Leads',
      data: counts
    }],
    chart: {
      type: 'bar',
      height: 120,
      toolbar: { show: false },
      sparkline: { enabled: false }
    },
    colors: ['#1A1A1A'],
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '35%',
        distributed: false
      }
    },
    dataLabels: { enabled: false },
    grid: { show: false },
    xaxis: {
      categories: labels,
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: {
        style: { colors: '#666666', fontSize: '11px', fontFamily: 'sans-serif' }
      }
    },
    yaxis: {
      min: 0,
      max: 10,
      tickAmount: 2,
      labels: {
        style: { colors: '#666666', fontSize: '11px' }
      }
    },
    tooltip: { enabled: false }
  };

  const el = document.querySelector("#newLeadsApexChart");
  if (el) {
    el.innerHTML = "";
    new ApexCharts(el, options).render();
  }
}

function renderTickGauge(percentage) {
  const container = document.getElementById("gauge-ticks");
  if (!container) return;

  container.innerHTML = "";
  const totalTicks = 60;
  const activeTicks = Math.round((percentage / 100) * totalTicks);

  const cx = 100, cy = 95, r = 80;

  for (let i = 0; i <= totalTicks; i++) {
    const angle = Math.PI + (i / totalTicks) * Math.PI; // 180deg semi-circle
    
    const x1 = cx + (r - 12) * Math.cos(angle);
    const y1 = cy + (r - 12) * Math.sin(angle);
    const x2 = cx + r * Math.cos(angle);
    const y2 = cy + r * Math.sin(angle);

    const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
    line.setAttribute("x1", x1);
    line.setAttribute("y1", y1);
    line.setAttribute("x2", x2);
    line.setAttribute("y2", y2);
    line.setAttribute("stroke-width", "1.5");
    line.setAttribute("stroke-linecap", "round");

    if (i <= activeTicks) {
      line.setAttribute("stroke", "#1A1A1A"); // Dark active ticks
    } else {
      line.setAttribute("stroke", "#D1D1CB"); // Muted inactive ticks
    }

    container.appendChild(line);
  }
}