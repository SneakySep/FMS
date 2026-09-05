<?php
$page_title = "Admin Control Center - Priority Handling";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch Dashboard KPIs (Revenue & Customers Closed MTD) mula sa FastAPI
$kpi_res = make_api_request('/api/v1/leads/dashboard-kpis', 'GET');
$kpi_data = $kpi_res['data']['data'] ?? $kpi_res['data'] ?? [];

$revenue_current   = (float)($kpi_data['revenue']['current'] ?? 0);
$revenue_diff      = (float)($kpi_data['revenue']['diff'] ?? 0);
$customers_mtd     = (int)($kpi_data['customers_closed']['current'] ?? 0);
$customers_mtd_diff = (int)($kpi_data['customers_closed']['diff'] ?? 0);

// 2. Fetch Lead Stats (Pipeline Funnel) mula sa FastAPI
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

$total_leads    = (int)($stats_data['all'] ?? 0);
$new_inquiry    = (int)($stats_data['new_inquiry'] ?? 0);
$qualifying     = (int)($stats_data['qualifying'] ?? 0);
$quote_sent     = (int)($stats_data['quote_sent'] ?? 0);
$negotiation    = (int)($stats_data['negotiation'] ?? 0);
$closed_won     = (int)($stats_data['closed_won'] ?? 0);
$closed_lost    = (int)($stats_data['closed_lost'] ?? 0);

// 3. Fetch Customer Tier Stats mula sa FastAPI
$cust_stats_res = make_api_request('/api/v1/customers/stats', 'GET');
$cust_stats     = $cust_stats_res['data'] ?? [];

$tier_bronze   = (int)($cust_stats['bronze'] ?? 0);
$tier_silver   = (int)($cust_stats['silver'] ?? 0);
$tier_gold     = (int)($cust_stats['gold'] ?? 0);
$tier_platinum = (int)($cust_stats['platinum'] ?? 0);
$total_customers_tier = $tier_bronze + $tier_silver + $tier_gold + $tier_platinum;

// 4. Fetch Lead Trend (Last 7 days) mula sa FastAPI
$trend_res = make_api_request('/api/v1/leads/trend?range=7', 'GET');
$trend_data = $trend_res['data'] ?? [];
$trend_dates = $trend_data['dates'] ?? [];
$trend_counts = $trend_data['counts'] ?? [];

// Prepare trend chart data (JSON-encoded para sa JS)
$trend_chart_data = json_encode([
    'dates' => $trend_dates,
    'counts' => $trend_counts
]);

// 5. Fetch Closed Won Tickets na 'for account' pa lang mula sa FastAPI
$tickets_res = make_api_request('/api/v1/admin/close-won-tickets', 'GET');
$all_tickets = $tickets_res['data'] ?? [];

// FILTER LOGIC: kunin lang ang mga wala pang customer_id (need provisioning)
$pending_tickets = array_filter($all_tickets, function($ticket) {
    return !isset($ticket['customer_id']) || is_null($ticket['customer_id']) || trim((string)$ticket['customer_id']) === '';
});
$ticket_count = count($pending_tickets);

// 6. Fetch Active Customer Accounts (para sa recent activity)
$customers_res    = make_api_request('/api/v1/admin/customer-accounts', 'GET');
$customers_list   = $customers_res['data'] ?? [];
$active_customers = count($customers_list);

// Kunin ang 5 pinak-recent na customer accounts
usort($customers_list, function($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});
$recent_customers = array_slice($customers_list, 0, 5);

// 7. Expose data to JS
$admin_dashboard_js = json_encode([
    'api_base_url' => rtrim(API_BASE_URL, '/'),
    'trend' => [
        'dates' => $trend_dates,
        'counts' => $trend_counts
    ],
    'tiers' => [
        'bronze'   => $tier_bronze,
        'silver'   => $tier_silver,
        'gold'     => $tier_gold,
        'platinum' => $tier_platinum,
        'total'    => $total_customers_tier
    ]
]);
echo '<script>window.ADMIN_DASHBOARD_DATA = ' . $admin_dashboard_js . ';</script>';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <?php include_once 'components/top_header.php'; ?>

    <!-- ROW 1: TOP 4 KPI METRICS -->
  <?php include_once 'components/kpi_cards.php'; ?>

  <!-- ROW 2: SALES PERFORMANCE SECTION -->
  <?php include_once 'sales_performance.php'; ?>

  <!-- ROW 3: CUSTOMER MONITORING SECTION -->
  <?php include_once 'customer_monitoring.php'; ?>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- Admin Dashboard JS -->
<script src="../../../assets/js/admin/dashboard_admin.js"></script>