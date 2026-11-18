<?php

require_once '../../controllers/sales_agent/dashboard_controller.php';

$page_title = "Sales Agent Dashboard · PRIORITY HANDLING";

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
        <h1 class="text-2xl font-extrabold text-slate-900">Sales Dashboard & Pipeline</h1>
        <p class="text-xs text-slate-500 mt-1">Welcome Back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Agent'); ?>! Here are your live performance and inquiries..</p>
    </div>

    <!-- INCLUDE KPI CARDS COMPONENT -->
    <?php include_once 'components/dashboard/kpi_cards.php'; ?>

    <!-- INCLUDE KANBAN BOARD COMPONENT -->
    <?php include_once 'components/dashboard/kanban_board.php'; ?>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- SALES AGENT CUSTOM JS -->
<script src="../../../assets/js/sales_agent/kanban.js"></script>