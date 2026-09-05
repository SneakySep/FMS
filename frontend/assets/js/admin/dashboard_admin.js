// =================================================================================================
// Admin Dashboard Charts & Interactions
// Initializes ApexCharts for Sales Performance and Customer Tier Distribution
// =================================================================================================
(function () {
  'use strict';

  // Guard: ensure required libraries are loaded
  if (typeof ApexCharts === 'undefined') {
    console.warn('[AdminDashboard] ApexCharts not loaded. Skipping chart initialization.');
    return;
  }

  // Grab injected data from PHP (set in dashboard.php)
  var DATA = window.ADMIN_DASHBOARD_DATA || {};

  /* Colours come from the theme tokens (see window.crmPalette in header.php)
     so the charts follow the Priority navy scale instead of hardcoding their
     own indigo/violet hexes. Each chart builds its options through a function
     so a theme flip can re-read the tokens rather than reuse a frozen set. */
  var palette = function () {
    return (window.crmPalette || function () { return window.CRM_COLORS; })();
  };

  // ------------------------------------------------------------------
  // 1. Revenue / Lead Trend Bar Chart (#admin-revenue-chart)
  // ------------------------------------------------------------------
  (function initRevenueChart() {
    var el = document.getElementById('admin-revenue-chart');
    if (!el) return;

    var trend = DATA.trend || {};
    var dates  = trend.dates || [];
    var counts = trend.counts || [];

    // Fallback labels if API returns empty
    var defaultLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    // Format dates to MM-DD for x-axis
    var xLabels = dates.map(function (d) {
      var parts = d.split('-');
      return parts.length >= 3 ? parts[1] + '/' + parts[2] : d;
    });
    if (xLabels.length === 0) {
      xLabels = defaultLabels.slice(0, counts.length || 7);
    }

    var options = buildOptions(palette());

    function buildOptions(P) {
      return {
      chart: {
        type: 'bar',
        height: 260,
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false },
        animations: {
          enabled: true,
          easing: 'cubic-bezier(0.68, -0.55, 0.265, 1.275)',
          speed: 900,
          animateGradually: { enabled: true, delay: 90 },
          dynamicAnimation: { enabled: true, speed: 500 }
        }
      },
      series: [{
        name: 'New Leads',
        data: counts.length ? counts : [0, 0, 0, 0, 0, 0, 0]
      }],
      plotOptions: {
        bar: {
          borderRadius: 6,
          borderRadiusApplication: 'end',
          borderRadiusWhenOpposite: 'true',
          columnWidth: '65%',
          columnGap: '6px',
          distributed: false,
          foregroundShadow: 'rgba(29, 46, 106, 0.15)',
          barHeight: '70%'
        }
      },
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: 'vertical',
          gradientToColors: [P.sky],
          stops: [0, 100],
          opacityFrom: 0.9,
          opacityTo: 0.6
        }
      },
      xaxis: {
        categories: xLabels,
        labels: {
          style: { colors: P.muted, fontSize: '11px', fontWeight: 500 },
          padding: { left: -4 }
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
        crosshairs: { show: false }
      },
      yaxis: {
        min: 0,
        tickAmount: 5,
        labels: {
          style: { colors: P.muted, fontSize: '11px', fontWeight: 500 },
          formatter: function (val) { return Math.round(val); }
        },
        crosshairs: { show: false }
      },
      grid: {
        borderColor: P.grid,
        strokeDashArray: 3,
        y: { lines: { show: true } },
        x: { lines: { show: false } }
      },
      colors: [P.navy],
      dataLabels: {
        enabled: true,
        position: 'top',
        style: {
          fontSize: '11px',
          fontWeight: 700,
          colors: [P.ink]
        },
        formatter: function (val) {
          return val > 0 ? val : '';
        },
        offsetY: -14,
        textAnchor: 'middle'
      },
      tooltip: {
        theme: 'dark',
        elevation: 8,
        style: { fontSize: '12px' },
        x: {
          format: {
            type: 'category'
          }
        },
        y: {
          formatter: function (val) {
            return val + ' new lead' + (val === 1 ? '' : 's');
          }
        },
        marker: {
          width: 8,
          height: 8,
          radius: 4
        },
        fixed: {
          enabled: true,
          position: 'top'
        }
      },
      responsive: [{
        breakpoint: 640,
        options: {
          chart: { height: 200 },
          plotOptions: {
            bar: {
              columnWidth: '80%',
              columnGap: '4px',
              borderRadius: 4
            }
          },
          dataLabels: {
            style: { fontSize: '10px' }
          }
        }
      }]
      };
    }

    var chart = new ApexCharts(el, options);
    chart.render();

    document.addEventListener('crm:theme-change', function () {
      chart.updateOptions(buildOptions(palette()), false, true);
    });
  })();

  // ------------------------------------------------------------------
  // 2. Customer Tier Distribution Doughnut Chart (#admin-tier-chart)
  // ------------------------------------------------------------------
  (function initTierChart() {
    var el = document.getElementById('admin-tier-chart');
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
      chart: {
        type: 'donut',
        height: 240,
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false }
      },
      series: [platinum, gold, silver, bronze],
      labels: ['Platinum', 'Gold', 'Silver', 'Bronze'],
      /* Tiers are ordinal (Platinum > Gold > Silver > Bronze), so they take
         the sequential navy ramp rather than four unrelated hues. The legend
         dots in customer_monitoring.php mirror this order. */
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
                formatter: function () {
                  return total.toString();
                },
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
      tooltip: {
        y: {
          formatter: function (val) {
            return val + ' customers';
          }
        }
      },
      responsive: [{
        breakpoint: 640,
        options: {
          chart: { height: 200 },
          plotOptions: {
            pie: {
              donut: { size: '65%' }
            }
          }
        }
      }]
      };
    }

    var chart = new ApexCharts(el, options);
    chart.render();

    document.addEventListener('crm:theme-change', function () {
      chart.updateOptions(buildOptions(palette()), false, true);
    });
  })();

})();