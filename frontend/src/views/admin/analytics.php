<?php
$page_title = "Analytics & Reports - Priority Handling";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Dashboard KPIs (Revenue & Customers Closed MTD)
$kpi_res     = make_api_request('/api/v1/leads/dashboard-kpis', 'GET');
$kpi_data    = $kpi_res['data']['data'] ?? $kpi_res['data'] ?? [];

$revenue_current   = (float)($kpi_data['revenue']['current'] ?? 0);
$revenue_previous  = (float)($kpi_data['revenue']['previous'] ?? 0);
$revenue_diff      = (float)($kpi_data['revenue']['diff'] ?? 0);
$customers_current = (int)($kpi_data['customers_closed']['current'] ?? 0);
$customers_previous= (int)($kpi_data['customers_closed']['previous'] ?? 0);
$customers_diff    = (int)($kpi_data['customers_closed']['diff'] ?? 0);

// 2. Lead Stats (Pipeline Funnel)
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

$total_leads    = (int)($stats_data['all'] ?? 0);
$new_inquiry    = (int)($stats_data['new_inquiry'] ?? 0);
$qualifying     = (int)($stats_data['qualifying'] ?? 0);
$quote_sent     = (int)($stats_data['quote_sent'] ?? 0);
$negotiation    = (int)($stats_data['negotiation'] ?? 0);
$closed_won     = (int)($stats_data['closed_won'] ?? 0);
$closed_lost    = (int)($stats_data['closed_lost'] ?? 0);

$all_leads_for_rate = $total_leads + $closed_won + $closed_lost;
$conversion_rate    = $all_leads_for_rate > 0 ? round(($closed_won / $all_leads_for_rate) * 100, 1) : 0;
$total_closed = $closed_won + $closed_lost;
$win_rate     = $total_closed > 0 ? round(($closed_won / $total_closed) * 100, 1) : 0;
$avg_deal_value = $closed_won > 0 ? $revenue_current / $closed_won : 0;

// 3. Customer Tier Stats
$cust_stats_res = make_api_request('/api/v1/customers/stats', 'GET');
$cust_stats     = $cust_stats_res['data'] ?? [];

$tier_bronze   = (int)($cust_stats['bronze'] ?? 0);
$tier_silver   = (int)($cust_stats['silver'] ?? 0);
$tier_gold     = (int)($cust_stats['gold'] ?? 0);
$tier_platinum = (int)($cust_stats['platinum'] ?? 0);
$total_customers_tier = $tier_bronze + $tier_silver + $tier_gold + $tier_platinum;

// 4. Lead Trend (Last 30 days)
$trend_res      = make_api_request('/api/v1/leads/trend?range=30', 'GET');
$trend_data     = $trend_res['data'] ?? [];
$trend_dates    = $trend_data['dates'] ?? [];
$trend_counts   = $trend_data['counts'] ?? [];

// 5. Closed-Won Tickets (Pending Provisioning)
$tickets_res    = make_api_request('/api/v1/admin/close-won-tickets', 'GET');
$all_tickets    = $tickets_res['data'] ?? [];
$pending_tickets = array_filter($all_tickets, function($ticket) {
    return !isset($ticket['customer_id']) || is_null($ticket['customer_id']) || trim((string)$ticket['customer_id']) === '';
});
$ticket_count   = count($pending_tickets);
$revenue_at_stake = 0;
foreach ($pending_tickets as $ticket) {
    $revenue_at_stake += (float)($ticket['agreed_amount'] ?? 0);
}

// 6. Agents Data
$agents_res  = make_api_request('/api/v1/admin/agents', 'GET');
$agents_raw  = [];
if ($agents_res['status_code'] === 200 && !empty($agents_res['data'])) {
    $api_data = $agents_res['data'];
    $agents_raw = isset($api_data['data']) && is_array($api_data['data']) ? $api_data['data'] : (is_array($api_data) && !isset($api_data['detail']) ? $api_data : []);
}
$agents = [];
if (is_array($agents_raw)) {
    foreach ($agents_raw as $row) {
        if (!is_array($row)) continue;
        $status = isset($row['status']) && $row['status'] !== null ? ucfirst((string) $row['status']) : ($row['is_active'] ?? true ? 'Active' : 'Inactive');
        $agents[] = [
            'id'     => $row['id'] ?? $row['agent_id'] ?? 0,
            'name'   => $row['name'] ?? trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
            'email'  => $row['email'] ?? '',
            'status' => $status,
            'sales'  => (float)($row['sales'] ?? $row['total_sales'] ?? $row['revenue'] ?? 0),
        ];
    }
}
usort($agents, function($a, $b) { return $b['sales'] <=> $a['sales']; });
$top_agents = array_slice($agents, 0, 5);
$top_agent_sales = !empty($top_agents) ? max(array_column($top_agents, 'sales')) : 1;

// 7. Active Customer Accounts
$customers_res    = make_api_request('/api/v1/admin/customer-accounts', 'GET');
$customers_list   = $customers_res['data'] ?? [];
if (isset($customers_list['data']) && is_array($customers_list['data'])) {
    $customers_list = $customers_list['data'];
}
$active_customers = count($customers_list);

// 8. Prepare JS data bundle
$analytics_js_data = json_encode([
    'trend' => ['dates' => $trend_dates, 'counts' => $trend_counts],
    'pipeline' => [
        'new_inquiry' => $new_inquiry, 'qualifying' => $qualifying,
        'quote_sent' => $quote_sent, 'negotiation' => $negotiation,
        'closed_won' => $closed_won, 'closed_lost' => $closed_lost,
    ],
    'tiers' => [
        'bronze' => $tier_bronze, 'silver' => $tier_silver,
        'gold' => $tier_gold, 'platinum' => $tier_platinum, 'total' => $total_customers_tier,
    ],
    'agents' => array_map(function($a) { return ['name' => $a['name'], 'sales' => $a['sales']]; }, $top_agents),
]);
echo '<script>window.ANALYTICS_DATA = ' . $analytics_js_data . ';</script>';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <?php include_once 'components/top_header.php'; ?>

  <!-- PAGE TITLE & BREADCRUMBS -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
      <nav class="flex items-center gap-1 text-[10px] text-slate-400 font-medium mb-1.5">
        <span>Admin</span>
        <i class="fa-solid fa-chevron-right text-[8px]"></i>
        <span class="text-indigo-600">Analytics &amp; Reports</span>
      </nav>
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-200">
          <i class="fa-solid fa-chart-column text-white text-sm"></i>
        </div>
        <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight italic">Analytics Dashboard</h1>
          <p class="text-xs text-slate-400 font-medium">Comprehensive business intelligence &mdash; Pipeline, Revenue, and Customer insights</p>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <button class="flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:border-indigo-300 transition-all shadow-sm">
        <i class="fa-solid fa-calendar-days text-slate-400"></i>
        <span>Last 30 Days</span>
        <i class="fa-solid fa-chevron-down text-[10px] ml-1"></i>
      </button>
    </div>
  </div>

  <!-- ROW 1: TOP KPI METRIC CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <!-- KPI 1: Total Revenue (MTD) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all group fade-in">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-indigo-600 transition-colors">Total Revenue (MTD)</span>
        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-coins text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-1">&#8369;<?= number_format($revenue_current, 2) ?></div>
        <div class="flex items-center text-xs font-medium <?= $revenue_diff >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
          <i class="fa-solid <?= $revenue_diff >= 0 ? 'fa-arrow-up-right' : 'fa-arrow-down-right' ?> mr-1 text-[10px]"></i>
          <span>&#8369;<?= number_format(abs($revenue_diff), 2) ?> vs last month</span>
        </div>
      </div>
    </div>

    <!-- KPI 2: Total Active Leads -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-purple-300 transition-all group fade-in" style="animation-delay:0.05s">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-purple-600 transition-colors">Total Active Leads</span>
        <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-users text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-1"><?= $total_leads ?></div>
        <div class="flex items-center text-xs font-medium text-purple-600">
          <i class="fa-solid fa-arrow-trend-up mr-1 text-[10px]"></i>
          <span><?= $new_inquiry ?> new inquiries</span>
        </div>
      </div>
    </div>

    <!-- KPI 3: Conversion Rate -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-300 transition-all group fade-in" style="animation-delay:0.1s">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-emerald-600 transition-colors">Conversion Rate</span>
        <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-bullseye text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-1"><?= $conversion_rate ?>%</div>
        <div class="flex items-center text-xs font-medium text-emerald-600">
          <i class="fa-solid fa-trophy mr-1 text-[10px]"></i>
          <span><?= $win_rate ?>% win rate (<?= $closed_won ?>/<?= $total_closed ?>)</span>
        </div>
      </div>
    </div>

    <!-- KPI 4: Active Customers -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-amber-300 transition-all group fade-in" style="animation-delay:0.15s">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-amber-600 transition-colors">Active Customers</span>
        <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-building text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-1"><?= $active_customers ?></div>
        <div class="flex items-center text-xs font-medium text-amber-600">
          <i class="fa-solid fa-crown mr-1 text-[10px]"></i>
          <span><?= $tier_platinum ?> Platinum &middot; <?= $tier_gold ?> Gold</span>
        </div>
      </div>
    </div>
  </div>
  <!-- ROW 2: LEAD GROWTH TREND -->
  <div class="bg-gradient-to-b from-indigo-50/60 to-purple-50/40 p-6 rounded-2xl border border-slate-200/80 shadow-sm mb-8 fade-in" style="animation-delay:0.2s">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-chart-line text-indigo-600 text-sm"></i>
        <div>
          <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Lead Growth Trend</h2>
          <p class="text-[11px] text-slate-400 mt-0.5">Daily new lead acquisitions over the last 30 days</p>
        </div>
      </div>
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span> New Leads</span>
      </div>
    </div>
    <div id="analytics-lead-trend-chart" class="w-full h-[280px]"></div>
  </div>



  <!-- ROW 3: PIPELINE FUNNEL + STAGE DONUT -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Pipeline Funnel -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm fade-in" style="animation-delay:0.25s">
      <div class="flex items-center gap-2 mb-5">
        <i class="fa-solid fa-filter text-purple-600 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Lead Pipeline Funnel</h2>
      </div>
      <p class="text-[11px] text-slate-400 mb-5">Current pipeline distribution across all stages</p>
      <div class="space-y-3">
        <div>
          <div class="flex items-center justify-between mb-1"><span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5"><span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Closed Won</span><span class="text-xs font-bold text-slate-900"><?= $closed_won ?></span></div>
          <div class="w-full bg-slate-100 rounded-full h-2.5"><div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2.5 rounded-full" style="width:<?= $all_leads_for_rate > 0 ? round(($closed_won/$all_leads_for_rate)*100) : 0 ?>%"></div></div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-1"><span class="text-xs font-bold text-amber-700 flex items-center gap-1.5"><span class="w-2 h-2 bg-amber-500 rounded-full"></span> Negotiation</span><span class="text-xs font-bold text-slate-900"><?= $negotiation ?></span></div>
          <div class="w-full bg-slate-100 rounded-full h-2.5"><div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2.5 rounded-full" style="width:<?= $all_leads_for_rate > 0 ? round(($negotiation/$all_leads_for_rate)*100) : 0 ?>%"></div></div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-1"><span class="text-xs font-bold text-indigo-700 flex items-center gap-1.5"><span class="w-2 h-2 bg-indigo-500 rounded-full"></span> Quote Sent</span><span class="text-xs font-bold text-slate-900"><?= $quote_sent ?></span></div>
          <div class="w-full bg-slate-100 rounded-full h-2.5"><div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-2.5 rounded-full" style="width:<?= $all_leads_for_rate > 0 ? round(($quote_sent/$all_leads_for_rate)*100) : 0 ?>%"></div></div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-1"><span class="text-xs font-bold text-sky-700 flex items-center gap-1.5"><span class="w-2 h-2 bg-sky-500 rounded-full"></span> Qualifying</span><span class="text-xs font-bold text-slate-900"><?= $qualifying ?></span></div>
          <div class="w-full bg-slate-100 rounded-full h-2.5"><div class="bg-gradient-to-r from-sky-400 to-sky-600 h-2.5 rounded-full" style="width:<?= $all_leads_for_rate > 0 ? round(($qualifying/$all_leads_for_rate)*100) : 0 ?>%"></div></div>
        </div>
        <div>
          <div class="flex items-center justify-between mb-1"><span class="text-xs font-bold text-violet-700 flex items-center gap-1.5"><span class="w-2 h-2 bg-violet-500 rounded-full"></span> New Inquiry</span><span class="text-xs font-bold text-slate-900"><?= $new_inquiry ?></span></div>
          <div class="w-full bg-slate-100 rounded-full h-2.5"><div class="bg-gradient-to-r from-violet-400 to-violet-600 h-2.5 rounded-full" style="width:<?= $all_leads_for_rate > 0 ? round(($new_inquiry/$all_leads_for_rate)*100) : 0 ?>%"></div></div>
        </div>
      </div>
    </div>

    <!-- Stage Distribution Donut -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm fade-in" style="animation-delay:0.3s">
      <div class="flex items-center gap-2 mb-5">
        <i class="fa-solid fa-chart-pie text-indigo-600 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Stage Distribution</h2>
      </div>
      <p class="text-[11px] text-slate-400 mb-4">Proportion of leads at each pipeline stage</p>
      <div id="analytics-stage-donut" class="w-full h-[260px]"></div>
      <div class="grid grid-cols-2 gap-3 mt-4">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-violet-500"></span><span class="text-xs text-slate-600">New Inquiry</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $new_inquiry ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-sky-500"></span><span class="text-xs text-slate-600">Qualifying</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $qualifying ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-indigo-500"></span><span class="text-xs text-slate-600">Quote Sent</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $quote_sent ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-500"></span><span class="text-xs text-slate-600">Negotiation</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $negotiation ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span><span class="text-xs text-slate-600">Closed Won</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $closed_won ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-500"></span><span class="text-xs text-slate-600">Closed Lost</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $closed_lost ?></span></div>
      </div>
    </div>
  </div>

  <!-- ROW 4: CUSTOMER TIER + AGENT LEADERBOARD -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Customer Tier Distribution -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm fade-in" style="animation-delay:0.35s">
      <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-crown text-amber-500 text-sm"></i>
          <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Customer Tier Distribution</h2>
        </div>
        <a href="customers.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors"><i class="fa-solid fa-arrow-right text-[10px]"></i> View All</a>
      </div>
      <p class="text-[11px] text-slate-400 mb-4">Customer segments by booking volume</p>
      <div id="analytics-tier-chart" class="w-full h-[240px]"></div>
      <div class="grid grid-cols-2 gap-3 mt-4">
        <div class="flex items-center gap-2"><span class="w-3 h-3 bg-purple-500 rounded-full"></span><span class="text-xs text-slate-600">Platinum</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $tier_platinum ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 bg-yellow-400 rounded-full"></span><span class="text-xs text-slate-600">Gold</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $tier_gold ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 bg-slate-400 rounded-full"></span><span class="text-xs text-slate-600">Silver</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $tier_silver ?></span></div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 bg-amber-600 rounded-full"></span><span class="text-xs text-slate-600">Bronze</span><span class="text-xs font-bold text-slate-900 ml-auto"><?= $tier_bronze ?></span></div>
      </div>
      <div class="mt-4 pt-3 border-t border-slate-100">
        <div class="flex items-center justify-between text-xs"><span class="text-slate-500 font-medium">Total Customers</span><span class="font-bold text-slate-900"><?= $total_customers_tier ?></span></div>
      </div>
    </div>

    <!-- Agent Performance Leaderboard -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm fade-in" style="animation-delay:0.4s">
      <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-ranking-star text-rose-500 text-sm"></i>
          <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Agent Leaderboard</h2>
        </div>
        <a href="agents.php" class="text-xs font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1 transition-colors">Manage <i class="fa-solid fa-arrow-right text-[10px]"></i></a>
      </div>
      <p class="text-[11px] text-slate-400 mb-5">Top performing sales agents by total revenue</p>
      <?php if (!empty($top_agents)): ?>
        <div class="space-y-3">
          <?php foreach ($top_agents as $index => $agent):
            $agent_sales = $agent['sales'];
            $pct = $top_agent_sales > 0 ? min(100, round(($agent_sales / $top_agent_sales) * 100)) : 0;
            $rank_color = match ($index) { 0 => 'bg-amber-100 text-amber-700', 1 => 'bg-slate-200 text-slate-600', 2 => 'bg-orange-100 text-orange-700', default => 'bg-slate-100 text-slate-500' };
            $status_class = $agent['status'] === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500';
          ?>
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-indigo-50/50 hover:border-indigo-100 transition">
              <div class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold <?= $rank_color ?>"><?= $index + 1 ?></div>
              <?php $nameParts = explode(' ', trim($agent['name'])); $initials = ''; foreach ($nameParts as $p) { $initials .= strtoupper(substr($p, 0, 1)); } $initials = substr($initials, 0, 2); ?>
              <div class="shrink-0 w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold"><?= htmlspecialchars($initials) ?></div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <h3 class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($agent['name']) ?></h3>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?= $status_class ?> whitespace-nowrap"><?= $agent['status'] ?></span>
                </div>
                <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($agent['email']) ?></p>
                <div class="w-full bg-slate-200 rounded-full h-1.5 mt-1.5"><div class="bg-indigo-500 h-1.5 rounded-full" style="width:<?= $pct ?>%"></div></div>
              </div>
              <div class="shrink-0 text-right">
                <p class="text-sm font-extrabold text-slate-900">&#8369;<?= number_format($agent_sales, 0) ?></p>
                <p class="text-[10px] text-slate-400">revenue</p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-xs text-slate-400 italic py-6 text-center">No agent data available.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- ROW 5: REVENUE SUMMARY + OPERATIONAL INSIGHTS -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Revenue Performance Summary -->
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm fade-in" style="animation-delay:0.45s">
      <div class="flex items-center gap-2 mb-5">
        <i class="fa-solid fa-sack-dollar text-emerald-600 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Revenue Performance</h2>
      </div>
      <p class="text-[11px] text-slate-400 mb-5">Month-over-month revenue analysis from closed deals</p>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-indigo-50/60 border border-indigo-200/50 rounded-xl p-5 text-center">
          <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider mb-2">This Month</p>
          <p class="text-2xl font-black text-indigo-700 mb-1">&#8369;<?= number_format($revenue_current, 0) ?></p>
          <p class="text-[11px] text-indigo-500"><?= $customers_current ?> closed deal<?= $customers_current !== 1 ? 's' : '' ?></p>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center">
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Last Month</p>
          <p class="text-2xl font-black text-slate-700 mb-1">&#8369;<?= number_format($revenue_previous, 0) ?></p>
          <p class="text-[11px] text-slate-500"><?= $customers_previous ?> closed deal<?= $customers_previous !== 1 ? 's' : '' ?></p>
        </div>
        <?php $diff_bg = $revenue_diff >= 0 ? 'bg-emerald-50/60 border-emerald-200/50' : 'bg-rose-50/60 border-rose-200/50'; $diff_text = $revenue_diff >= 0 ? 'text-emerald-700' : 'text-rose-700'; $diff_sub = $revenue_diff >= 0 ? 'text-emerald-500' : 'text-rose-500'; $diff_icon = $revenue_diff >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>
        <div class="<?= $diff_bg ?> border rounded-xl p-5 text-center">
          <p class="text-[10px] font-bold <?= $diff_text ?> uppercase tracking-wider mb-2">Difference</p>
          <p class="text-2xl font-black <?= $diff_text ?> mb-1"><?= $revenue_diff >= 0 ? '+' : '' ?>&#8369;<?= number_format(abs($revenue_diff), 0) ?></p>
          <p class="text-[11px] <?= $diff_sub ?> flex items-center justify-center gap-1"><i class="fa-solid <?= $diff_icon ?> text-[10px]"></i> <?= $revenue_diff >= 0 ? 'Growth' : 'Decline' ?> vs last month</p>
        </div>
      </div>
      <!-- Additional Metrics Row -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100"><p class="text-lg font-black text-slate-800"><?= $closed_won ?></p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Won Deals</p></div>
        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100"><p class="text-lg font-black text-slate-800"><?= $closed_lost ?></p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lost Deals</p></div>
        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100"><p class="text-lg font-black text-slate-800"><?= $win_rate ?>%</p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Win Rate</p></div>
        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100"><p class="text-lg font-black text-slate-800">&#8369;<?= number_format($avg_deal_value, 0) ?></p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Avg Deal Value</p></div>
      </div>
    </div>

    <!-- Operational Insights -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm fade-in" style="animation-delay:0.5s">
      <div class="flex items-center gap-2 mb-5">
        <i class="fa-solid fa-gauge-high text-rose-500 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Operations</h2>
      </div>
      <p class="text-[11px] text-slate-400 mb-5">Account provisioning &amp; pipeline health</p>
      <div class="space-y-4">
        <div class="p-4 rounded-xl <?= $ticket_count > 0 ? 'bg-rose-50/60 border border-rose-200/50' : 'bg-emerald-50/60 border border-emerald-200/50' ?>">
          <div class="flex items-center gap-2 mb-2">
            <i class="fa-solid <?= $ticket_count > 0 ? 'fa-triangle-exclamation text-rose-500' : 'fa-check-circle text-emerald-500' ?> text-sm"></i>
            <span class="text-xs font-bold <?= $ticket_count > 0 ? 'text-rose-700' : 'text-emerald-700' ?>">Account Provisioning</span>
          </div>
          <p class="text-2xl font-black <?= $ticket_count > 0 ? 'text-rose-600' : 'text-emerald-600' ?>"><?= $ticket_count ?></p>
          <p class="text-[11px] <?= $ticket_count > 0 ? 'text-rose-500' : 'text-emerald-500' ?> mt-1"><?= $ticket_count > 0 ? 'Closed-won tickets awaiting account creation' : 'All caught up!' ?></p>
          <?php if ($ticket_count > 0): ?>
            <a href="tickets.php" class="inline-flex items-center gap-1 mt-3 text-[11px] font-bold text-rose-600 hover:text-rose-800 transition-colors">Manage tickets <i class="fa-solid fa-arrow-right text-[9px]"></i></a>
          <?php endif; ?>
        </div>
        <?php if ($revenue_at_stake > 0): ?>
        <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200/50">
          <div class="flex items-center gap-2 mb-2"><i class="fa-solid fa-coins text-amber-500 text-sm"></i><span class="text-xs font-bold text-amber-700">Revenue at Stake</span></div>
          <p class="text-2xl font-black text-amber-600">&#8369;<?= number_format($revenue_at_stake, 0) ?></p>
          <p class="text-[11px] text-amber-500 mt-1">From pending provisioning tickets</p>
        </div>
        <?php endif; ?>
        <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-200/50">
          <div class="flex items-center gap-2 mb-2"><i class="fa-solid fa-heart-pulse text-indigo-500 text-sm"></i><span class="text-xs font-bold text-indigo-700">Pipeline Health</span></div>
          <div class="space-y-2 mt-3">
            <div class="flex justify-between text-xs"><span class="text-slate-500">Active Pipeline</span><span class="font-bold text-slate-800"><?= $total_leads ?> leads</span></div>
            <div class="flex justify-between text-xs"><span class="text-slate-500">Total Leads (All Time)</span><span class="font-bold text-slate-800"><?= $all_leads_for_rate ?></span></div>
            <div class="flex justify-between text-xs"><span class="text-slate-500">Conversion Rate</span><span class="font-bold text-emerald-600"><?= $conversion_rate ?>%</span></div>
            <div class="flex justify-between text-xs"><span class="text-slate-500">Win Rate</span><span class="font-bold text-indigo-600"><?= $win_rate ?>%</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- Admin Analytics JS -->
<script src="../../../assets/js/admin/analytics_admin.js"></script>
