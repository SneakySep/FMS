// =================================================================================================
// Admin Analytics Dashboard Charts
// Initializes ApexCharts for Lead Trend, Stage Distribution, and Customer Tier charts
// =================================================================================================
(function () {
  'use strict';

  if (typeof ApexCharts === 'undefined') {
    console.warn('[AnalyticsAdmin] ApexCharts not loaded. Skipping chart initialization.');
    return;
  }

  var DATA = window.ANALYTICS_DATA || {};

  // ------------------------------------------------------------------
  // 1. 30-Day Lead Growth Trend — Area Chart
  // ------------------------------------------------------------------
  (function initLeadTrendChart() {
    var el = document.getElementById('analytics-lead-trend-chart');
    if (!el) return;

    var trend = DATA.trend || {};
    var dates  = trend.dates || [];
    var counts = trend.counts || [];

    var xLabels = dates.map(function (d) {
      var parts = d.split('-');
      return parts.length >= 3 ? parts[1] + '/' + parts[2] : d;
    });
    if (xLabels.length === 0) {
      xLabels = [];
      for (var i = 30; i >= 1; i--) {
        var dt = new Date();
        dt.setDate(dt.getDate() - i);
        xLabels.push((dt.getMonth() + 1).toString().padStart(2, '0') + '/' + dt.getDate().toString().padStart(2, '0'));
      }
      counts = xLabels.map(function () { return 0; });
    }

    var options = {
      chart: {
        type: 'area',
        height: 280,
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false },
        animations: { enabled: true, easing: 'easeinout', speed: 800, animateGradually: { enabled: true, delay: 100 } }
      },
      series: [{ name: 'New Leads', data: counts.length ? counts : xLabels.map(function () { return 0; }) }],
      fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] }
      },
      colors: ['#6366f1'],
      stroke: { curve: 'smooth', width: 2.5 },
      xaxis: {
        categories: xLabels,
        labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 }, padding: { left: -4 }, hideOverlappingLabels: true },
        axisBorder: { show: false },
        axisTicks: { show: false },
        crosshairs: { show: true, stroke: { color: '#6366f1', width: 1, dashArray: 4 } }
      },
      yaxis: {
        min: 0, tickAmount: 5,
        labels: { style: { colors: '#cbd5e1', fontSize: '11px', fontWeight: 500 }, formatter: function (val) { return Math.round(val); } }
      },
      grid: { borderColor: '#e2e8f0', strokeDashArray: 3, y: { lines: { show: true } }, x: { lines: { show: false } } },
      markers: { size: 3, colors: ['#6366f1'], strokeColors: '#fff', strokeWidth: 2, hover: { sizeOffset: 3 } },
      tooltip: {
        theme: 'dark', elevation: 8, style: { fontSize: '12px' },
        y: { formatter: function (val) { return val + ' lead' + (val === 1 ? '' : 's'); } },
        marker: { width: 8, height: 8, radius: 4 },
        fixed: { enabled: true, position: 'top' }
      },
      responsive: [{ breakpoint: 640, options: { chart: { height: 220 }, markers: { size: 2 } } }]
    };

    var chart = new ApexCharts(el, options);
    chart.render();
  })();

  // ------------------------------------------------------------------
  // 2. Stage Distribution Donut (#analytics-stage-donut)
  // ------------------------------------------------------------------
  (function initStageDonut() {
    var el = document.getElementById('analytics-stage-donut');
    if (!el) return;

    var p = DATA.pipeline || {};
    var ni = p.new_inquiry || 0;
    var qf = p.qualifying  || 0;
    var qs = p.quote_sent  || 0;
    var ng = p.negotiation || 0;
    var cw = p.closed_won  || 0;
    var cl = p.closed_lost || 0;
    var total = ni + qf + qs + ng + cw + cl;

    var options = {
      chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
      series: [ni, qf, qs, ng, cw, cl],
      labels: ['New Inquiry', 'Qualifying', 'Quote Sent', 'Negotiation', 'Closed Won', 'Closed Lost'],
      colors: ['#8b5cf6', '#0ea5e9', '#6366f1', '#f59e0b', '#10b981', '#f43f5e'],
      plotOptions: {
        pie: {
          donut: {
            size: '60%',
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Total Leads',
                formatter: function () { return total.toString(); },
                fontSize: '14px',
                fontWeight: 700,
                color: '#0f172a'
              }
            }
          }
        }
      },
      dataLabels: { enabled: false },
      legend: { show: false },
      tooltip: { y: { formatter: function (val) { return val + ' lead' + (val === 1 ? '' : 's'); } } },
      responsive: [{ breakpoint: 640, options: { chart: { height: 220 }, plotOptions: { pie: { donut: { size: '65%' } } } } }]
    };

    var chart = new ApexCharts(el, options);
    chart.render();
  })();

  // ------------------------------------------------------------------
  // 3. Customer Tier Distribution Donut (#analytics-tier-chart)
  // ------------------------------------------------------------------
  (function initTierChart() {
    var el = document.getElementById('analytics-tier-chart');
    if (!el) return;

    var tiers = DATA.tiers || {};
    var bronze   = tiers.bronze   || 0;
    var silver   = tiers.silver   || 0;
    var gold     = tiers.gold     || 0;
    var platinum = tiers.platinum || 0;
    var total = platinum + gold + silver + bronze;

    var options = {
      chart: { type: 'donut', height: 240, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
      series: [platinum, gold, silver, bronze],
      labels: ['Platinum', 'Gold', 'Silver', 'Bronze'],
      colors: ['#8b5cf6', '#eab308', '#94a3b8', '#d97706'],
      plotOptions: {
        pie: {
          donut: {
            size: '55%',
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Total Customers',
                formatter: function () { return total.toString(); },
                fontSize: '14px',
                fontWeight: 700,
                color: '#0f172a'
              }
            }
          }
        }
      },
      dataLabels: { enabled: false },
      legend: { show: false },
      tooltip: { y: { formatter: function (val) { return val + ' customer' + (val === 1 ? '' : 's'); } } },
      responsive: [{ breakpoint: 640, options: { chart: { height: 200 }, plotOptions: { pie: { donut: { size: '65%' } } } } }]
    };

    var chart = new ApexCharts(el, options);
    chart.render();
  })();

})();
