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

  /* Colours come from the theme tokens (see window.crmPalette in header.php)
     so the charts follow the Priority navy scale instead of hardcoding their
     own indigo/violet hexes. Each chart builds its options through a function
     so a theme flip can re-read the tokens rather than reuse a frozen set. */
  var palette = function () {
    return (window.crmPalette || function () { return window.CRM_COLORS; })();
  };

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

    var options = buildOptions(palette());

    var chart = new ApexCharts(el, options);
    chart.render();

    // Re-read the tokens when the scheme flips so the series don't keep
    // light-mode hexes on the dark canvas.
    document.addEventListener('crm:theme-change', function () {
      chart.updateOptions(buildOptions(palette()), false, true);
    });

    function buildOptions(P) {
      return {
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
      colors: [P.chart[0]],
      stroke: { curve: 'smooth', width: 2.5 },
      xaxis: {
        categories: xLabels,
        labels: { style: { colors: P.muted, fontSize: '11px', fontWeight: 500 }, padding: { left: -4 }, hideOverlappingLabels: true },
        axisBorder: { show: false },
        axisTicks: { show: false },
        crosshairs: { show: true, stroke: { color: P.navy, width: 1, dashArray: 4 } }
      },
      yaxis: {
        min: 0, tickAmount: 5,
        labels: { style: { colors: P.muted, fontSize: '11px', fontWeight: 500 }, formatter: function (val) { return Math.round(val); } }
      },
      grid: { borderColor: P.grid, strokeDashArray: 3, y: { lines: { show: true } }, x: { lines: { show: false } } },
      markers: { size: 3, colors: [P.navy], strokeColors: P.surface, strokeWidth: 2, hover: { sizeOffset: 3 } },
      tooltip: {
        theme: 'dark', elevation: 8, style: { fontSize: '12px' },
        y: { formatter: function (val) { return val + ' lead' + (val === 1 ? '' : 's'); } },
        marker: { width: 8, height: 8, radius: 4 },
        fixed: { enabled: true, position: 'top' }
      },
      responsive: [{ breakpoint: 640, options: { chart: { height: 220 }, markers: { size: 2 } } }]
      };
    }
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

    var options = buildOptions(palette());

    var chart = new ApexCharts(el, options);
    chart.render();

    document.addEventListener('crm:theme-change', function () {
      chart.updateOptions(buildOptions(palette()), false, true);
    });

    function buildOptions(P) {
      return {
      chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
      series: [ni, qf, qs, ng, cw, cl],
      labels: ['New Inquiry', 'Qualifying', 'Quote Sent', 'Negotiation', 'Closed Won', 'Closed Lost'],
      /* The funnel stages walk the navy ramp light -> dark, so the series reads
         as one brand and the order is legible without a legend. Won/lost keep
         their universal green/red so the terminal states still pop. */
      colors: [P.stage.new, P.stage.qualifying, P.stage.quote, P.stage.negotiation, P.stage.won, P.stage.lost],
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
                color: P.ink
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
    }
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

    var options = buildOptions(palette());

    function buildOptions(P) {
      return {
      chart: { type: 'donut', height: 240, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
      series: [platinum, gold, silver, bronze],
      labels: ['Platinum', 'Gold', 'Silver', 'Bronze'],
      /* Tiers are ordinal (Platinum > Gold > Silver > Bronze), so they take
         the sequential navy ramp light-to-dark rather than four unrelated hues.
         The legend dots in customer_monitoring.php mirror this order. */
      colors: [P.chart[0], P.chart[1], P.chart[2], P.chart[3]],
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
                color: P.ink
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
    }

    var chart = new ApexCharts(el, options);
    chart.render();

    document.addEventListener('crm:theme-change', function () {
      chart.updateOptions(buildOptions(palette()), false, true);
    });
  })();

})();
