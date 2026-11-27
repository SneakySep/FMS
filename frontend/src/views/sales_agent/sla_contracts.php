<?php

require_once '../../controllers/sales_agent/sla_route.php';

$page_title = "SLA Contracts · PRIORITY HANDLING";
$pageTitle  = "SLA Contracts";
$activePage = "SLA Contracts"; // I-highlight ang "SLA Contracts" sa sidebar

include_once '../../includes/header.php';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

    <!-- TOP HEADER & NAVBAR -->
    <?php include_once '../../components/top_header.php'; ?>

    <!-- PAGE HEADING -->
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">SLA Contracts</h1>
        <p class="text-xs text-slate-500 mt-1">I-check ang Service Level Agreement status ng isang inquiry. Welcome Back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Agent'); ?>!</p>
    </div>

    <!-- SLA STAT CARDS -->
    <?php include_once 'components/sla/sla_stats.php'; ?>

    <!-- SLA CHECK TABLE -->
    <?php include_once 'components/sla/sla_check_table.php'; ?>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- SALES AGENT CUSTOM JS -->
<script src="../../../assets/js/sales_agent/sla_contracts.js"></script>
