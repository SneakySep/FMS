<div class="bg-neutral-primary-soft border border-default rounded-base shadow-xs p-4 md:p-6 flex-[2_1_0px] min-w-[300px]">
  <div class="flex justify-between mb-4 md:mb-6">
    <div class="grid gap-4 grid-cols-2">
      <div>
        <h5 class="inline-flex items-center text-body">Total Leads
          <svg data-popover-target="clicks-info" data-popover-placement="bottom" class="w-4 h-4 text-body hover:text-heading cursor-pointer ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
          <div data-popover id="clicks-info" role="tooltip" class="absolute z-10 p-3 invisible inline-block text-sm text-body transition-opacity duration-300 bg-neutral-primary-soft border border-default rounded-base shadow-xs opacity-0 w-72">
              <div>
                  <h3 class="font-semibold text-heading mb-2">Total Leads</h3>
                  <p class="mb-4">All leads currently in your pipeline across every stage. A healthy pipeline keeps this number growing week over week.</p>
                  <h3 class="font-semibold text-heading mb-2">Calculation</h3>
                  <p class="mb-4">Counts every lead regardless of stage: New Inquiry, Qualifying, Quote Sent, Negotiation and Won.</p>
                  <a href="/kanban" class="flex items-center font-medium text-fg-brand hover:underline">
                      Open board
                      <svg class="w-4 h-4 ms-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
                  </a>
              </div>
              <div data-popper-arrow></div>
          </div>
        </h5>
        <p class="text-heading text-2xl font-semibold"><?= htmlspecialchars((string)array_sum(array_column($pipeline, 'count'))) ?></p>
      </div>

      <div>
        <h5 class="inline-flex items-center text-body">Won (MTD)
          <svg data-popover-target="cpc-info" data-popover-placement="bottom" class="w-4 h-4 text-body hover:text-heading cursor-pointer ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
          <div data-popover id="cpc-info" role="tooltip" class="absolute z-10 p-3 invisible inline-block text-sm text-body transition-opacity duration-300 bg-neutral-primary-soft border border-default rounded-base shadow-xs opacity-0 w-72">
              <div>
                  <h3 class="font-semibold text-heading mb-2">Won (Month to Date)</h3>
                  <p class="mb-4">Deals marked as Won so far this month. This is the conversion outcome of your pipeline activity.</p>
                  <h3 class="font-semibold text-heading mb-2">Calculation</h3>
                  <p class="mb-4">Counts only leads whose stage is Closed Won and whose close date falls within the current calendar month.</p>
                  <a href="/kanban" class="flex items-center font-medium text-fg-brand hover:underline">
                      Open board
                      <svg class="w-4 h-4 ms-1 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/></svg>
                  </a>
              </div>
              <div data-popper-arrow></div>
          </div>
        </h5>
        <p class="text-heading text-2xl font-semibold"><?= htmlspecialchars((string)$closed_won) ?></p>
      </div>
    </div>
    <div class="relative group">
      <button id="dropdownDefaultButton" type="button" class="inline-flex items-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-3 py-2 focus:outline-none">
        Last 30 days
        <svg class="w-4 h-4 ms-1.5 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
      </button>
      <div id="lastDaysdropdown" class="absolute right-0 z-10 hidden group-hover:block bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg w-44">
          <ul class="p-2 text-sm text-body font-medium" aria-labelledby="dropdownDefaultButton">
            <li><a href="#" data-range="7"  class="range-item inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Last week</a></li>
            <li><a href="#" data-range="1"  class="range-item inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Today</a></li>
            <li><a href="#" data-range="7"  class="range-item inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Last 7 days</a></li>
            <li><a href="#" data-range="30" class="range-item inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Last 30 days</a></li>
            <li><a href="#" data-range="90" class="range-item inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Last 90 days</a></li>
          </ul>
      </div>
    </div>
  </div>

  <div id="line-chart"></div>

  <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-4 pt-4 border-default border-t md:mt-6 md:pt-6">
    <?php foreach ($pipeline as $key => $stage): ?>
      <div class="flex items-center gap-2 text-xs text-body">
        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background: <?= $stage['color'] ?>;"></span>
        <span class="font-medium text-heading"><?= htmlspecialchars($stage['label']) ?></span>
        <span class="font-bold text-heading"><?= htmlspecialchars((string)$stage['count']) ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="border-default border-t mt-4 md:mt-6 pt-4 md:pt-6">
    <button type="button" onclick="window.location.href='/kanban'" class="inline-flex items-center text-white bg-brand hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-3 py-2 focus:outline-none">
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
  var STAGES = (window.CHART_STAGES && window.CHART_STAGES.length)
    ? window.CHART_STAGES
    : [{ status: "new_inquiry", label: "New Inquiry", color: "#a78bfa" }];
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
      labels: { style: { colors: "#94a3b8", fontSize: "11px" }, rotate: -45, hideOverlappingLabels: true },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: {
      labels: { style: { colors: "#94a3b8", fontSize: "11px" }, formatter: function (val) { return Math.round(val); } }
    },
    grid: { borderColor: "#e2e8f0", strokeDashArray: 4 },
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





