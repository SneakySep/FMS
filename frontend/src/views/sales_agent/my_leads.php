<?php

require_once '../../controllers/sales_agent/leads.php';

$page_title = "My Leads · PRIORITY HANDLING";
$pageTitle  = "My Leads";
$activePage = "leads"; // I-highlight ang "My Leads" sa sidebar

// Helper: buuin ang URL habang pinapanatili ang ibang filter
function leadsFilterUrl(array $overrides = [])
{
    $params = array_merge([
        'status'       => $_GET['status'] ?? 'all',
        'search'       => $_GET['search'] ?? '',
        'service_type' => $_GET['service_type'] ?? '',
    ], $overrides);

    $params = array_filter($params, function ($v) {
        return $v !== '' && $v !== null && $v !== 'all';
    });

    return 'my_leads.php?' . http_build_query($params);
}

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
        <h1 class="text-2xl font-extrabold text-slate-900">My Leads</h1>
        <p class="text-xs text-slate-500 mt-1">Manage, qualify and close your inquiries. Welcome Back, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Agent'); ?>!</p>
    </div>

    <!-- FILTER BAR -->
    <?php include_once 'components/leads/filter_bar.php'; ?>

    <!-- INQUIRY LEADS DIRECTORY -->
    <?php include_once 'components/leads/leads_table.php'; ?>

    <!-- MODALS -->
    <?php include_once 'components/leads/view_lead_drawer.php'; ?>
    <?php include_once 'components/leads/new_lead_modal.php'; ?>
    <?php include_once 'components/leads/quotation_modal.php'; ?>
    <?php include_once 'components/leads/contact_modal.php'; ?>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- SALES AGENT CUSTOM JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../../assets/js/components/alert.js"></script>
<script src="../../../assets/js/sales_agent/myleads.js"></script>
<script src="../../../assets/js/sales_agent/new_leads.js"></script>
