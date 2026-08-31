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

        var options = {
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
          foregroundShadow: 'rgba(99, 102, 241, 0.15)',
          barHeight: '70%'
        }
      },
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: 'vertical',
          gradientToColors: ['#818cf8'],
          stops: [0, 100],
          opacityFrom: 0.9,
          opacityTo: 0.6
        }
      },
      xaxis: {
        categories: xLabels,
        labels: {
          style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 },
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
          style: { colors: '#cbd5e1', fontSize: '11px', fontWeight: 500 },
          formatter: function (val) { return Math.round(val); }
        },
        crosshairs: { show: false }
      },
      grid: {
        borderColor: '#e2e8f0',
        strokeDashArray: 3,
        y: { lines: { show: true } },
        x: { lines: { show: false } }
      },
      colors: ['#6366f1'],
      dataLabels: {
        enabled: true,
        position: 'top',
        style: {
          fontSize: '11px',
          fontWeight: 700,
          colors: ['#0f172a']
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
        breakpoint: { table: 640 },
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

    var chart = new ApexCharts(el, options);
    chart.render();
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

    var options = {
      chart: {
        type: 'donut',
        height: 240,
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false }
      },
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
                label: {
                  text: 'Total Customers',
                  style: {
                    fontSize: '10px',
                    color: '#94a3b8',
                    fontWeight: 600
                  }
                },
                val: {
                  formatter: function () {
                    return total.toString();
                  },
                  style: {
                    fontSize: '14px',
                    fontWeight: 700,
                    color: '#0f172a'
                  }
                }
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
        breakpoint: { table: 640 },
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

    var chart = new ApexCharts(el, options);
    chart.render();
  })();

})();