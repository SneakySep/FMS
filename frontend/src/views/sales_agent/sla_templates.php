<?php

require_once '../../controllers/sales_agent/sla_templates.php';

$page_title = "SLA Templates · PRIORITY HANDLING";
$pageTitle  = "SLA Templates";
$activePage = "SLA Templates"; // I-highlight ang "SLA Templates" sa sidebar

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
        <h1 class="text-2xl font-extrabold text-slate-900">SLA Templates</h1>
        <p class="text-xs text-slate-500 mt-1">Reusable service terms for new contracts. Welcome Back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Agent'); ?>!</p>
    </div>

    <?php if (!empty($sla_template_success)): ?>
        <!-- SUCCESS BANNER -->
        <div class="mb-6 flex items-start gap-2.5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <span><?php echo htmlspecialchars($sla_template_success); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($sla_template_error)): ?>
        <!-- ERROR BANNER -->
        <div class="mb-6 flex items-start gap-2.5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <span><?php echo htmlspecialchars($sla_template_error); ?></span>
        </div>
    <?php endif; ?>

    <!-- SLA TEMPLATE CARDS -->
    <?php include_once 'components/sla_templates/template_cards.php'; ?>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
