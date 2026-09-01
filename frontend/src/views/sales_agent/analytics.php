<?php
$page_title = "Analytics Dashboard - PRIORITY HANDLING";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Data (similar to dashboard.php)
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

$kpi_res     = make_api_request('/api/v1/leads/dashboard-kpis', 'GET');
$kpi_data    = $kpi_res['data']['data'] ?? $kpi_res['data'] ?? [];

$revenue_current   = (float)($kpi_data['revenue']['current'] ?? 0);
$revenue_diff      = (float)($kpi_data['revenue']['diff'] ?? 0);
$customers_current = (int)($kpi_data['customers_closed']['current'] ?? 0);
$customers_diff    = (int)($kpi_data['customers_closed']['diff'] ?? 0);

// Extract Live Counts
$total_leads = (int)($stats_data['all'] ?? 0);
$new_inquiry = (int)($stats_data['new_inquiry'] ?? 0);

?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC]">

  <!-- CUSTOM ANALYTICS HEADER -->
  <?php 
  $header_title = "Analytics Dashboard";
  $header_subtitle = "Monitor your pipeline performance and revenue growth.";
  $header_actions = '<button type="button" onclick="openLeadModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-sm hover:shadow-indigo-200 flex items-center gap-2"><i class="fa-solid fa-plus text-[10px]"></i><span>New Lead</span></button>';
  include_once 'components/dashboard_header.php'; 
  ?>
  
  <div class="p-6 lg:p-8">
    <!-- ROW 1: TOP KPI METRICS -->
    <?php include_once 'components/kpi_cards.php'; ?>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Lead Growth Chart -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Lead Growth Trend (Last 7 Days)</h3>
        <div id="leadTrendChart"></div>
      </div>
      
      <!-- Stage Distribution Chart -->
      <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Leads by Stage</h3>
        <div id="stageDistributionChart"></div>
      </div>
    </div>
  </div>

</main>

<?php include_once 'components/lead_modal.php'; ?>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- Chart Scripts -->
<script>
    // Initialize ApexCharts here
    
    // Lead Trend Chart
    var optionsTrend = {
      chart: { type: 'area', height: 350, toolbar: { show: false } },
      series: [{ name: 'New Leads', data: [10, 15, 8, 20, 12, 25, 30] }],
      xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] },
      colors: ['#8b5cf6'],
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth' }
    };
    new ApexCharts(document.querySelector("#leadTrendChart"), optionsTrend).render();
    
    // Stage Distribution Chart
    var optionsStage = {
      chart: { type: 'donut', height: 350 },
      series: [44, 55, 13, 33, 22],
      labels: ['New', 'Qualifying', 'Quote', 'Negotiation', 'Won'],
      colors: ['#a78bfa', '#fbbf24', '#c084fc', '#f59e0b', '#10b981']
    };
    new ApexCharts(document.querySelector("#stageDistributionChart"), optionsStage).render();
</script>
