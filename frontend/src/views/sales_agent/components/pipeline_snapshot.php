<?php
// ---------------------------------------------------------------------------
// Defensive defaults. This component is included by both dashboard.php and
// bi_analytics.php, and it relies on the includer having already prepared
// $pipeline (stage rows) and $closed_won (int). If an includer forgets, the
// old code hit array_column(null, 'count') which is a fatal TypeError in
// PHP 8, so fall back to an empty/zero pipeline instead of killing the page.
// ---------------------------------------------------------------------------
$pipeline   = (isset($pipeline) && is_array($pipeline)) ? $pipeline : [];
$closed_won = (isset($closed_won)) ? (int) $closed_won : 0;
?>
<div class="bg-white border border-slate-200 shadow-sm rounded-xl p-4 md:p-6 flex-[2_1_0px] min-w-[300px]">
  <div class="flex justify-between items-start mb-4 md:mb-6">
    <div class="grid gap-4 grid-cols-2">
      <div>
        <h5 class="inline-flex items-center text-slate-500 text-sm font-medium">Total Leads
          <span class="relative group ms-1 cursor-pointer">
            <svg class="w-4 h-4 text-slate-400 hover:text-slate-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 top-6 z-20 w-72 p-3 text-xs text-slate-500 bg-white border border-slate-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
              <span class="block font-semibold text-slate-700 mb-2">Total Leads</span>
              <span class="block mb-3">All leads currently in your pipeline across every stage. A healthy pipeline keeps this number growing week over week.</span>
              <span class="block font-semibold text-slate-700 mb-1">Calculation</span>
              <span class="block">Counts every lead regardless of stage: New Inquiry, Qualifying, Quote Sent, Negotiation and Won.</span>
              <a href="/kanban" class="inline-flex items-center font-medium text-brand-blue hover:underline mt-2">
                Open board
                <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
              </a>
            </span>
          </span>
        </h5>
        <p class="text-slate-800 text-2xl font-semibold"><?= htmlspecialchars((string)array_sum(array_column($pipeline, 'count'))) ?></p>
      </div>

      <div>
        <h5 class="inline-flex items-center text-slate-500 text-sm font-medium">Won (MTD)
          <span class="relative group ms-1 cursor-pointer">
            <svg class="w-4 h-4 text-slate-400 hover:text-slate-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span class="pointer-events-none absolute left-1/2 -translate-x-1/2 top-6 z-20 w-72 p-3 text-xs text-slate-500 bg-white border border-slate-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
              <span class="block font-semibold text-slate-700 mb-2">Won (Month to Date)</span>
              <span class="block mb-3">Deals marked as Won so far this month. This is the conversion outcome of your pipeline activity.</span>
              <span class="block font-semibold text-slate-700 mb-1">Calculation</span>
              <span class="block">Counts only leads whose stage is Closed Won and whose close date falls within the current calendar month.</span>
              <a href="/kanban" class="inline-flex items-center font-medium text-brand-blue hover:underline mt-2">
                Open board
                <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
              </a>
            </span>
          </span>
        </h5>
        <p class="text-slate-800 text-2xl font-semibold"><?= htmlspecialchars((string)$closed_won) ?></p>
      </div>
    </div>
    <div class="relative group">
      <button id="dropdownDefaultButton" type="button" class="inline-flex items-center text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 focus:ring-4 focus:ring-slate-200 shadow-sm font-medium leading-5 rounded-xl text-sm px-3 py-2 focus:outline-none">
        Last 30 days
        <svg class="w-4 h-4 ms-1.5 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
      </button>
      <div id="lastDaysdropdown" class="absolute right-0 z-10 hidden group-hover:block bg-white border border-slate-200 rounded-xl shadow-lg w-44">
          <ul class="p-2 text-sm text-slate-600 font-medium" aria-labelledby="dropdownDefaultButton">
            <li><a href="#" data-range="1"  class="range-item inline-flex items-center w-full p-2 hover:bg-slate-100 hover:text-slate-800 rounded-lg">Today</a></li>
            <li><a href="#" data-range="7"  class="range-item inline-flex items-center w-full p-2 hover:bg-slate-100 hover:text-slate-800 rounded-lg">Last 7 days</a></li>
            <li><a href="#" data-range="30" class="range-item inline-flex items-center w-full p-2 hover:bg-slate-100 hover:text-slate-800 rounded-lg">Last 30 days</a></li>
            <li><a href="#" data-range="90" class="range-item inline-flex items-center w-full p-2 hover:bg-slate-100 hover:text-slate-800 rounded-lg">Last 90 days</a></li>
          </ul>
      </div>
    </div>

    <a href="reports.php" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 rounded-xl transition-all shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      View full report
    </a>
  </div>

  <div id="line-chart"></div>

  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-4 pt-4 border-slate-200 border-t md:mt-6 md:pt-6">
    <?php foreach ($pipeline as $key => $stage): ?>
      <div class="flex items-center gap-2 text-xs text-slate-500">
        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background: <?= $stage['color'] ?>;"></span>
        <span class="font-medium text-slate-700"><?= htmlspecialchars($stage['label']) ?></span>
        <span class="font-bold text-slate-800"><?= htmlspecialchars((string)$stage['count']) ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="border-slate-200 border-t mt-4 md:mt-6 pt-4 md:pt-6">
    <button type="button" onclick="window.location.href='/kanban'" class="inline-flex items-center text-white bg-brand-blue hover:bg-brand-darkblue shadow-sm font-medium leading-5 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-4 focus:ring-brand-blue/30 transition-colors">
      <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m4 10v-2m3 2v-6m3 6v-3m4-11v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/></svg>
      View full report
    </button>
  </div>
</div>

<script>
(function () {
  if (typeof ApexCharts === "undefined") return;
  var el = document.getElementById("line-chart");
  if (!el) return;

  var agentId = document.body.getAttribute("data-agent-id") || "";
  var currentRange = 30;
  var API_BASE_URL = "<?= rtrim(API_BASE_URL, chr(39)) ?>";

  // Stage config (label + color) injected from dashboard.php so the chart
  // lines match the pipeline legend rendered below the graph.
  var P = (window.crmPalette || function () { return window.CRM_COLORS; })();
  var STAGES = (window.CHART_STAGES && window.CHART_STAGES.length)
    ? window.CHART_STAGES
    : [{ status: "new_inquiry", label: "New Inquiry", color: P.stage.new }];
  var stageColors = STAGES.map(function (s) { return s.color; });
  var stageByStatus = {};
  STAGES.forEach(function (s) { stageByStatus[s.status] = s; });

  var options = {
    chart: {
      type: "line",
      height: 320,
      fontFamily: "Inter, sans-serif",
      toolbar: { show: false },
      animations: { enabled: true, easing: "easeinout", speed: 700 }
    },
    series: STAGES.map(function (s) { return { name: s.label, data: [] }; }),
    xaxis: {
      categories: [],
      labels: { style: { colors: P.muted, fontSize: "12px" }, rotate: -45, hideOverlappingLabels: true },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: {
      labels: { style: { colors: P.muted, fontSize: "12px" }, formatter: function (val) { return Math.round(val); } }
    },
    grid: { borderColor: P.grid, strokeDashArray: 4 },
    stroke: { curve: "smooth", width: 3 },
    colors: stageColors,
    markers: { size: 0, hover: { size: 4 } },
    fill: { type: "solid", opacity: 1 },
    dataLabels: { enabled: false },
    legend: { show: false },
    tooltip: { shared: true, intersect: false, y: { formatter: function (val) { return (val || 0) + " leads"; } } }
  };

  var chart = new ApexCharts(el, options);
  chart.render();

  function loadTrend(range) {
    var url = API_BASE_URL + "/api/v1/leads/trend/stages?range=" + encodeURIComponent(range);
    if (agentId) url += "&agent_id=" + encodeURIComponent(agentId);
    fetch(url, { headers: { "Accept": "application/json" } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var dates = (data.dates || []).map(function (d) { return d.slice(5); }); // MM-DD
        var byStatus = {};
        (data.series || []).forEach(function (ser) { byStatus[ser.status] = ser.data || []; });
        var series = STAGES.map(function (s) {
          return { name: s.label, data: byStatus[s.status] || [] };
        });
        chart.updateOptions({ xaxis: { categories: dates } });
        chart.updateSeries(series);
      })
      .catch(function (err) { console.error("Pipeline stage trend load failed:", err); });
  }

  // Dropdown: update range + label, then reload
  document.querySelectorAll(".range-item").forEach(function (item) {
    item.addEventListener("click", function (e) {
      e.preventDefault();
      var range = parseInt(item.getAttribute("data-range"), 10) || 30;
      currentRange = range;
      var label = item.textContent.trim();
      var btn = document.getElementById("dropdownDefaultButton");
      if (btn) btn.firstChild.textContent = label + " ";
      loadTrend(range);
    });
  });

  // Initial load
  loadTrend(currentRange);
})();
</script>





