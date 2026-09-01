<?php
$pageTitle = "Customer Directory";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Parameters mula sa URL Filter
$current_tier  = $_GET['tier'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// 2. Fetch Live Stats
$stats_res  = make_api_request('/api/v1/customers/stats', 'GET');
$stats_data = $stats_res['data']['data'] ?? $stats_res['data'] ?? [];

$count_all      = (int)($stats_data['all'] ?? 0);
$count_bronze   = (int)($stats_data['bronze'] ?? 0);
$count_silver   = (int)($stats_data['silver'] ?? 0);
$count_gold     = (int)($stats_data['gold'] ?? 0);
$count_platinum = (int)($stats_data['platinum'] ?? 0);

// 3. Build API Endpoint Query String
$api_url = "/api/v1/customers?tier=" . urlencode($current_tier);

if (!empty($search_query)) {
    $api_url .= "&search=" . urlencode($search_query);
}

// Fetch Customers List
$customers_res  = make_api_request($api_url, 'GET');
$customers_list = $customers_res['data']['data'] ?? $customers_res['data'] ?? [];

// Helper function para sa Tier Status Badge
function getCustomerTierBadge($tier) {
    switch (strtoupper(trim($tier ?? 'BRONZE'))) {
        case 'PLATINUM':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'GOLD':
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'SILVER':
            return 'bg-slate-100 text-slate-700 border-slate-200';
        case 'BRONZE':
        default:
            return 'bg-amber-100 text-amber-800 border-amber-200';
    }
}

// Tier share percentages (para sa KPI subtext at distribution)
$tier_total   = max(1, $count_all);
$pct_bronze   = round(($count_bronze   / $tier_total) * 100);
$pct_silver   = round(($count_silver   / $tier_total) * 100);
$pct_gold     = round(($count_gold     / $tier_total) * 100);
$pct_platinum = round(($count_platinum / $tier_total) * 100);
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <?php 
  $header_title = "Customer Directory";
  $header_subtitle = "Manage your assigned accounts, loyalty tiers and booking volume.";
  include_once 'components/dashboard_header.php'; 
  ?>
  
  <div class="p-6 lg:p-8">
    <!-- PAGE HEADING + ACTIONS -->
    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Customer Directory</h1>
        <p class="mt-1 text-sm text-slate-500">Manage your assigned accounts, loyalty tiers and booking volume.</p>
      </div>

    <div class="flex items-center gap-2">
      <!-- SEARCH FORM (preserves active tier) -->
      <form method="GET" action="" class="relative flex items-center">
        <input type="hidden" name="tier" value="<?= htmlspecialchars($current_tier) ?>">
        <span class="absolute left-3 text-slate-400">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </span>
        <input
          type="text"
          name="search"
          value="<?= htmlspecialchars($search_query) ?>"
          placeholder="Search company or contact..."
          class="w-44 focus:w-64 transition-all duration-300 bg-white hover:bg-slate-50 focus:bg-white text-xs text-slate-800 placeholder-slate-400 pl-8 pr-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 shadow-sm"
        >
      </form>

      <!-- EXPORT CSV -->
      <button type="button" onclick="exportCustomerCSV()" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5 active:scale-95">
        <i class="fa-solid fa-file-csv text-purple-600"></i>
        <span class="hidden sm:inline">Export</span>
      </button>
    </div>
  </div>

  <!-- ROW 1: KPI METRIC CARDS -->
  <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-3 lg:grid-cols-5">

    <!-- Total Customers -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Total Customers</span>
        <div class="p-1.5 rounded-lg bg-purple-100 text-purple-600"><i class="fa-solid fa-users text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= $count_all ?></p>
      <p class="mt-2 text-[11px] font-semibold text-slate-400">Across all tiers</p>
    </div>

    <!-- Bronze -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Bronze</span>
        <div class="p-1.5 rounded-lg bg-amber-100 text-amber-600"><i class="fa-solid fa-medal text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= $count_bronze ?></p>
      <p class="mt-2 text-[11px] font-semibold text-amber-600"><?= $pct_bronze ?>% of base</p>
    </div>

    <!-- Silver -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Silver</span>
        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-600"><i class="fa-solid fa-shield-halved text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= $count_silver ?></p>
      <p class="mt-2 text-[11px] font-semibold text-slate-500"><?= $pct_silver ?>% of base</p>
    </div>

    <!-- Gold -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Gold</span>
        <div class="p-1.5 rounded-lg bg-yellow-100 text-yellow-600"><i class="fa-solid fa-award text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= $count_gold ?></p>
      <p class="mt-2 text-[11px] font-semibold text-yellow-600"><?= $pct_gold ?>% of base</p>
    </div>

    <!-- Platinum -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-start justify-between">
        <span class="text-xs font-bold text-slate-700">Platinum</span>
        <div class="p-1.5 rounded-lg bg-purple-100 text-purple-600"><i class="fa-solid fa-gem text-xs"></i></div>
      </div>
      <p class="mt-3 text-3xl font-extrabold text-slate-900"><?= $count_platinum ?></p>
      <p class="mt-2 text-[11px] font-semibold text-purple-600"><?= $pct_platinum ?>% of base</p>
    </div>

  </div>
  <!-- ROW 2: MAIN DASHBOARD GRID -->
  <div class="flex flex-wrap gap-6 items-start">

    <!-- LEFT COLUMN: CUSTOMER DIRECTORY TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex-[2_1_0px] min-w-[320px]">

      <!-- CARD HEADER + TIER FILTER TABS -->
      <div class="p-5 border-b border-slate-100 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-800">Customer Directory</h2>
          <p class="text-xs text-slate-400">View and manage customer tiers and booking accounts</p>
        </div>

        <!-- TIER FILTER TABS -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
          <a href="?tier=all<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>"
             class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap <?= $current_tier === 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            All (<?= $count_all ?>)
          </a>
          <a href="?tier=BRONZE<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>"
             class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap <?= strtoupper($current_tier) === 'BRONZE' ? 'bg-amber-500 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            Bronze (<?= $count_bronze ?>)
          </a>
          <a href="?tier=SILVER<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>"
             class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap <?= strtoupper($current_tier) === 'SILVER' ? 'bg-slate-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            Silver (<?= $count_silver ?>)
          </a>
          <a href="?tier=GOLD<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>"
             class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap <?= strtoupper($current_tier) === 'GOLD' ? 'bg-yellow-500 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            Gold (<?= $count_gold ?>)
          </a>
          <a href="?tier=PLATINUM<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>"
             class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition whitespace-nowrap <?= strtoupper($current_tier) === 'PLATINUM' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            Platinum (<?= $count_platinum ?>)
          </a>
        </div>
      </div>

      <!-- MAIN TABLE -->
      <div class="overflow-x-auto">
        <table id="customerTable" class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
              <th class="py-3 px-4">Company &amp; Contact</th>
              <th class="py-3 px-4">Email Address</th>
              <th class="py-3 px-4">Phone Number</th>
              <th class="py-3 px-4">Total Bookings</th>
              <th class="py-3 px-4">Tier Status</th>
              <th class="py-3 px-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <?php if (empty($customers_list)): ?>
              <tr>
                <td colspan="6" class="py-10 text-center text-slate-400">
                  <i class="fa-solid fa-folder-open text-2xl mb-2 block opacity-40"></i>
                  No customers found.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($customers_list as $customer): ?>
                <?php
                  $company = $customer['company_name'] ?? $customer['company'] ?? 'N/A';
                  $contact = $customer['contact_person'] ?? $customer['name'] ?? 'No Contact Person';
                  $email   = $customer['email'] ?? 'N/A';
                  $phone   = $customer['phone_number'] ?? $customer['phone'] ?? 'N/A';
                  $bookings = $customer['total_bookings'] ?? $customer['bookings'] ?? 0;
                  $tier    = $customer['tier'] ?? 'BRONZE';
                  $cust_id  = $customer['id'] ?? '';
                  $initial = htmlspecialchars(strtoupper(substr($company, 0, 1)));
                  $tierKey = strtoupper(trim($tier));
                  $avatarClass = match ($tierKey) {
                    'PLATINUM' => 'bg-purple-600',
                    'GOLD'     => 'bg-yellow-500',
                    'SILVER'   => 'bg-slate-500',
                    default    => 'bg-amber-500'
                  };
                ?>
                <tr class="hover:bg-slate-50/80 transition">

                  <!-- COMPANY NAME & CONTACT PERSON -->
                  <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                      <div class="shrink-0 w-9 h-9 rounded-full text-white flex items-center justify-center text-sm font-bold <?= $avatarClass ?>">
                        <?= $initial ?>
                      </div>
                      <div>
                        <div class="font-bold text-slate-800"><?= htmlspecialchars($company) ?></div>
                        <div class="text-xs text-slate-500"><?= htmlspecialchars($contact) ?></div>
                      </div>
                    </div>
                  </td>

                  <!-- EMAIL ADDRESS -->
                  <td class="py-4 px-4 text-slate-600 font-medium">
                    <a href="mailto:<?= htmlspecialchars($email) ?>" class="hover:text-purple-600 transition"><?= htmlspecialchars($email) ?></a>
                  </td>

                  <!-- PHONE NUMBER -->
                  <td class="py-4 px-4 text-slate-600"><?= htmlspecialchars($phone) ?></td>

                  <!-- TOTAL BOOKINGS -->
                  <td class="py-4 px-4">
                    <span class="font-bold text-slate-800"><?= (int)$bookings ?></span>
                    <span class="text-[11px] text-slate-400">bookings</span>
                  </td>

                  <!-- TIER STATUS -->
                  <td class="py-4 px-4 align-middle">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getCustomerTierBadge($tier) ?>">
                      <?= htmlspecialchars(strtoupper($tier)) ?>
                    </span>
                  </td>

                  <!-- ACTIONS -->
                  <td class="py-4 px-4 align-middle text-right">
                    <a href="view_customer.php?id=<?= urlencode($cust_id) ?>"
                       class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg inline-flex items-center justify-center transition"
                       title="View Details">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- FOOTER ROW -->
      <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-400">
        <span>Showing <?= count($customers_list) ?> customer(s)</span>
        <a href="view_customer.php" class="text-purple-600 hover:text-purple-700">View details &rarr;</a>
      </div>
    </div>

    <!-- RIGHT COLUMN: TIER DISTRIBUTION -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex-[1_1_0px] min-w-[260px]">
      <div class="flex items-center justify-between mb-1">
        <h2 class="text-sm font-bold text-slate-900">Tier Distribution</h2>
        <span class="text-[11px] text-slate-400">Live</span>
      </div>
      <p class="text-[11px] text-slate-400 mb-4">Share of customers per loyalty tier</p>

      <div class="relative">
        <div id="tierDonut" style="height: 240px;"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <span class="text-3xl font-extrabold text-slate-900"><?= $count_all ?></span>
          <span class="text-[11px] text-slate-400 font-semibold">Total</span>
        </div>
      </div>

      <!-- LEGEND -->
      <div class="mt-4 space-y-2.5">
        <div class="flex items-center justify-between text-xs">
          <span class="flex items-center gap-2 text-slate-600 font-medium"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Bronze</span>
          <span class="font-bold text-slate-800"><?= $count_bronze ?> <span class="text-slate-400 font-normal"><?= $pct_bronze ?>%</span></span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <span class="flex items-center gap-2 text-slate-600 font-medium"><span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span>Silver</span>
          <span class="font-bold text-slate-800"><?= $count_silver ?> <span class="text-slate-400 font-normal"><?= $pct_silver ?>%</span></span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <span class="flex items-center gap-2 text-slate-600 font-medium"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>Gold</span>
          <span class="font-bold text-slate-800"><?= $count_gold ?> <span class="text-slate-400 font-normal"><?= $pct_gold ?>%</span></span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <span class="flex items-center gap-2 text-slate-600 font-medium"><span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>Platinum</span>
          <span class="font-bold text-slate-800"><?= $count_platinum ?> <span class="text-slate-400 font-normal"><?= $pct_platinum ?>%</span></span>
        </div>
      </div>
    </div>

  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- TIER DISTRIBUTION DONUT CHART -->
<script>
(function () {
  if (typeof ApexCharts === "undefined") return;
  var el = document.getElementById("tierDonut");
  if (!el) return;

  var options = {
    chart: {
      type: "donut",
      height: 240,
      fontFamily: "Inter, sans-serif",
      toolbar: { show: false }
    },
    series: [<?= $count_bronze ?>, <?= $count_silver ?>, <?= $count_gold ?>, <?= $count_platinum ?>],
    labels: ["Bronze", "Silver", "Gold", "Platinum"],
    colors: ["#f59e0b", "#64748b", "#eab308", "#7c3aed"],
    stroke: { width: 2, colors: ["#ffffff"] },
    plotOptions: {
      pie: {
        donut: {
          size: "72%",
          labels: { show: false }
        }
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
        return opts.w.globals.series[opts.seriesIndex];
      },
      textAnchor: "middle",
      distributed: false,
      style: {
        fontSize: "11px",
        fontWeight: 700,
        colors: ["#ffffff"]
      },
      dropShadow: { enabled: true, top: 1, left: 1, blur: 1, opacity: 0.45 },
      background: { enabled: false }
    },
    legend: { show: false },
    tooltip: {
      y: { formatter: function (val) { return val + " customers"; } }
    },
    responsive: [{
      breakpoint: 480,
      options: { chart: { height: 220 } }
    }]
  };

  var chart = new ApexCharts(el, options);
  chart.render();
})();

// Export the current customer table to a CSV file
function exportCustomerCSV() {
  var table = document.getElementById("customerTable");
  if (!table) return;
  var rows = table.querySelectorAll("tr");
  var csv = [];
  rows.forEach(function (row) {
    var cells = row.querySelectorAll("th, td");
    if (!cells.length) return;
    var line = [];
    cells.forEach(function (cell, idx) {
      if (idx === cells.length - 1) return; // skip Action column
      var text = cell.innerText.replace(/[\r\n]+/g, " ").replace(/\s+/g, " ").trim();
      var q = String.fromCharCode(34);
      line.push(q + text.replace(new RegExp(q, 'g'), q + q) + q);
    });
    if (line.length) csv.push(line.join(","));
  });
  var blob = new Blob([csv.join(String.fromCharCode(10))], { type: "text/csv;charset=utf-8;" });
  var url = URL.createObjectURL(blob);
  var a = document.createElement("a");
  a.href = url;
  a.download = "customer_directory.csv";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
</script>
