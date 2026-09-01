<?php
$page_title = "Sales Agent Dashboard - Priority Handling";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch Dynamic Leads Stats mula sa FastAPI (/api/v1/leads/stats)
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

// 2. Fetch Dashboard KPIs (Revenue & Customers Closed month vs last month)
$kpi_res     = make_api_request('/api/v1/leads/dashboard-kpis', 'GET');

// INAYOS DITO: Kinuha ang inner 'data' object mula sa FastAPI Response 
$kpi_data    = $kpi_res['data']['data'] ?? $kpi_res['data'] ?? [];

$revenue_current   = (float)($kpi_data['revenue']['current'] ?? 0);
$revenue_diff      = (float)($kpi_data['revenue']['diff'] ?? 0);
$customers_current = (int)($kpi_data['customers_closed']['current'] ?? 0);
$customers_diff    = (int)($kpi_data['customers_closed']['diff'] ?? 0);

// Extract Live Counts
$total_leads = (int)($stats_data['all'] ?? 0);
$new_inquiry = (int)($stats_data['new_inquiry'] ?? 0);
$qualifying  = (int)($stats_data['qualifying'] ?? 0);
$quote_sent  = (int)($stats_data['quote_sent'] ?? 0);
$negotiation = (int)($stats_data['negotiation'] ?? 0);
$closed_won  = (int)($stats_data['closed_won'] ?? 0);

// Compute Percentage para sa Pipeline Progress Bars
$max_count = max(1, $total_leads); 
$pipeline  = [
    'new_inquiry' => ['label' => 'New Inquiry', 'color' => '#a78bfa', 'count' => $new_inquiry, 'percentage' => min(100, round(($new_inquiry / $max_count) * 100))],
    'qualifying'  => ['label' => 'Qualifying',  'color' => '#fbbf24', 'count' => $qualifying,  'percentage' => min(100, round(($qualifying / $max_count) * 100))],
    'quote_sent'  => ['label' => 'Quote Sent',  'color' => '#c084fc', 'count' => $quote_sent,  'percentage' => min(100, round(($quote_sent / $max_count) * 100))],
    'negotiation' => ['label' => 'Negotiation', 'color' => '#f59e0b', 'count' => $negotiation, 'percentage' => min(100, round(($negotiation / $max_count) * 100))],
    'won_mtd'     => ['label' => 'Won (MTD)',   'color' => '#10b981', 'count' => $closed_won,  'percentage' => min(100, round(($closed_won / $max_count) * 100))],
];
// Stage config for the multi-line pipeline chart (status => label + color).
// Colors mirror the pipeline legend rendered below the chart.
$chart_stages = [
    'new_inquiry' => ['status' => 'new_inquiry', 'label' => 'New Inquiry', 'color' => '#a78bfa'],
    'qualifying'  => ['status' => 'qualifying',  'label' => 'Qualifying',  'color' => '#fbbf24'],
    'quote_sent'  => ['status' => 'quote_sent',  'label' => 'Quote Sent',  'color' => '#c084fc'],
    'negotiation' => ['status' => 'negotiation', 'label' => 'Negotiation', 'color' => '#f59e0b'],
    'won_mtd'     => ['status' => 'closed_won',  'label' => 'Won (MTD)',   'color' => '#10b981'],
];
// Expose stage config (ordered) to the pipeline chart script.
$chart_stages_json = json_encode(array_values($chart_stages));
echo '<script>window.CHART_STAGES = ' . $chart_stages_json . ';</script>' . "\n";


// 3. Fetch Customers para sa Top Customers Widget (sorted by total_bookings desc)
$all_customers_res = make_api_request('/api/v1/customers?tier=all&limit=100', 'GET');
$all_customers     = $all_customers_res['data']['data'] ?? $all_customers_res['data'] ?? [];

// Sort by total_bookings descending at kunin ang Top 5
usort($all_customers, function ($a, $b) {
    return (int)($b['total_bookings'] ?? 0) - (int)($a['total_bookings'] ?? 0);
});
$top_customers = array_slice($all_customers, 0, 5);
$top_max       = !empty($top_customers) ? (int)($top_customers[0]['total_bookings'] ?? 0) : 0;

// Helper function para sa Status Badges
function getContractStatusBadge($status) {
    switch (strtoupper(trim($status))) {
        case 'ACTIVE':
            return 'bg-emerald-100 text-emerald-600';
        case 'PENDING':
        case 'PENDING APPROVAL':
            return 'bg-amber-100 text-amber-600';
        case 'DRAFT':
            return 'bg-blue-100 text-blue-600';
        default:
            return 'bg-slate-100 text-slate-600';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php 
  $header_title = "Priority Handling";
  $header_subtitle = "Welcome back, agent. Overview of your activities.";
  include_once 'components/dashboard_header.php'; 
  ?>

  <div class="p-6 lg:p-8">
    <!-- ROW 1: TOP KPI METRICS -->
    <?php include_once 'components/kpi_cards.php'; ?>

  <!-- ROW 2: MAIN DASHBOARD GRID -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <!-- LEFT COLUMN: PIPELINE SNAPSHOT -->
    <div class="lg:col-span-2">
      <?php include_once 'components/pipeline_snapshot.php'; ?>
    </div>

    <!-- RIGHT COLUMN WIDGETS -->
    <div class="lg:col-span-1">
      <?php include_once 'components/top_customers.php'; ?>
    </div>

  </div>
  </div>

  <?php include_once 'components/lead_modal.php'; ?>
  <?php include_once '../../components/chat_widget.php' ?>
  

</main>


<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>




