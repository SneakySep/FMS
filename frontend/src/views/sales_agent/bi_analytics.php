<?php 

$page_title = "Sales Agent Analytics · PRIORITY HANDLING";
// components/top_header.php reads $pageTitle (not $page_title) for the bar label.
$pageTitle  = "BI Analytics";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// ---------------------------------------------------------------------------
// PIPELINE SNAPSHOT DATA
// components/pipeline_snapshot.php expects $pipeline and $closed_won to already
// exist in scope (dashboard.php normally defines them). Without them the
// include dies on array_column(null, 'count') in PHP 8, so build them here.
// ---------------------------------------------------------------------------
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

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

// Stage config (status => label + color) for the multi-line pipeline chart, so
// the chart series match the legend rendered below the graph.
$chart_stages = [
    'new_inquiry' => ['status' => 'new_inquiry', 'label' => 'New Inquiry', 'color' => '#a78bfa'],
    'qualifying'  => ['status' => 'qualifying',  'label' => 'Qualifying',  'color' => '#fbbf24'],
    'quote_sent'  => ['status' => 'quote_sent',  'label' => 'Quote Sent',  'color' => '#c084fc'],
    'negotiation' => ['status' => 'negotiation', 'label' => 'Negotiation', 'color' => '#f59e0b'],
    'won_mtd'     => ['status' => 'closed_won',  'label' => 'Won (MTD)',   'color' => '#10b981'],
];
echo '<script>window.CHART_STAGES = ' . json_encode(array_values($chart_stages)) . ';</script>' . "\n";

?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php
  // top_header.php keys its role-specific action button off $userRole. sidebar.php
  // already set it from $_SESSION['role'], but header.php has the broader lookup
  // ($_SESSION['user_role'] ?? $_SESSION['role']), so prefer that when present.
  if (!empty($currentUserRole)) {
      $userRole = $currentUserRole;
  }
  include_once '../../components/top_header.php';
  ?>

  <!-- PAGE HEADER -->
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Business Intelligence & Sales Analytics</h1>
    <p class="text-xs text-slate-400">Real-time performance metrics and predictive analytics</p>
  </div>

  <!-- MAIN WRAPPER (2-COLUMN LAYOUT) -->
  <div class="flex flex-col xl:flex-row gap-6 items-start mb-8 w-full">
    
    <!-- LEFT SIDE: 2x2 CARDS GRID (FIXED 660px MAX WIDTH) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 shrink-0 w-full xl:w-[660px]">
      <?php include_once 'components/analytics/gross_revenue_card.php'; ?>
      <?php include_once 'components/analytics/service_types_card.php'; ?>
      <?php include_once 'components/analytics/top_routes_card.php'; ?>
      <?php include_once 'components/analytics/shipments_closed_card.php'; ?>
    </div>

    <!-- RIGHT SIDE: PIPELINE SNAPSHOT CONTAINER -->
    <div class="w-full xl:flex-1 min-w-0">
      <?php include_once 'components/pipeline_snapshot.php'; ?>
    </div>

  </div>

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full mb-8">
  
  <!-- LEFT: WIN / LOSS BY SERVICE TYPE -->
  <div class="w-full">
    <?php include_once 'components/analytics/win_loss_card.php'; ?>
  </div>

  <!-- MIDDLE: SERVICE WON DONUT CHART -->
  <div class="w-full">
    <?php include_once 'components/analytics/service_distribution_card.php'; ?>
  </div>

  <!-- RIGHT: CARGO WEIGHT CLASS BAR CHART -->
  <div class="w-full">
    <?php include_once 'components/analytics/weight_class_card.php'; ?>
  </div>

</div>

  <?php include_once 'components/lead_modal.php'; ?>

</main>

<?php include_once 'components/alert.php'; ?>

<!-- pipiline.js intentionally NOT loaded: it targets #chart-total-leads,
     #pipelineActivityChart and #top-customers-list, none of which exist on this
     page (the snapshot renders its own #line-chart), so it only fired a dead
     request to /api/v1/analytics/sales-dashboard. -->
<script src="../../../assets/js/sales_agent/new_leads.js"></script>
<script src="../../../assets/js/sales_agent/win_or_lost.js"></script>
<script src="../../../assets/js/sales_agent/service_won_donut.js"></script>
<script src="../../../assets/js/sales_agent/weight_class_chart.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>