<?php
$page_title = "Quotes Dashboard - PRIORITY HANDLING";
$activePage = 'quotes';

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Quote Stats from the backend API
$stats_res  = make_api_request('/api/v1/quotes/stats', 'GET');
$stats_raw  = $stats_res['data']['data'] ?? $stats_res['data'] ?? null;

if (!empty($stats_raw) && is_array($stats_raw)) {
    $stats_data = [
        'total'    => (int)($stats_raw['total'] ?? 0),
        'pending'  => (int)($stats_raw['pending'] ?? 0),
        'approved' => (int)($stats_raw['approved'] ?? 0),
        'rejected' => (int)($stats_raw['rejected'] ?? 0),
    ];
} else {
    // Demo fallback
    $stats_data = ['total' => 120, 'pending' => 45, 'approved' => 60, 'rejected' => 15];
}

// Fetch recent quotes list
$quotes_res  = make_api_request('/api/v1/quotes/?limit=5', 'GET');
$quotes_raw  = $quotes_res['data']['data'] ?? $quotes_res['data'] ?? [];
if (!is_array($quotes_raw)) $quotes_raw = [];
$quotes_list = $quotes_raw;
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC]">

    <?php 
    $header_title = "Quotes Management";
    $header_subtitle = "Manage, track, and send customer quotes efficiently.";
    $header_actions = '<button id="newQuoteBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2"><i class="fa-solid fa-plus text-xs"></i> New Quote</button>';
    include_once 'components/dashboard_header.php'; 
    ?>

    <div class="p-6 lg:p-8">

    <!-- ROW 1: QUICK KPI STATS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?php 
        $icons = ['total' => 'fa-file-invoice', 'pending' => 'fa-clock', 'approved' => 'fa-check-circle', 'rejected' => 'fa-times-circle'];
        $colors = ['total' => 'blue', 'pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose'];
        
        foreach ($stats_data as $label => $value): 
            $color = $colors[$label] ?? 'slate';
        ?>
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="p-3 rounded-lg bg-<?= $color ?>-50 text-<?= $color ?>-600">
                    <i class="fa-solid <?= $icons[$label] ?? 'fa-file-invoice-dollar' ?> text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold"><?= $label ?></p>
                    <h3 class="text-2xl font-bold text-slate-900"><?= $value ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ROW 2: QUOTES TABLE / LIST -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-900">Recent Quotes</h2>
            <div class="text-sm text-slate-500">Showing last 5 quotes</div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Quote ID</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($quotes_list)): ?>
                    <?php foreach ($quotes_list as $quote): ?>
                    <?php
                        $q_id     = $quote['quote_id'] ?? $quote['id'] ?? '#';
                        $q_cust   = $quote['company_name'] ?? $quote['customer_name'] ?? 'N/A';
                        $q_amount = (float)($quote['amount'] ?? $quote['total_amount'] ?? 0);
                        $q_status = strtoupper(trim($quote['status'] ?? 'pending'));
                        $q_date   = $quote['created_at'] ?? $quote['date'] ?? '';
                        $status_colors = [
                            'PENDING'  => 'bg-amber-100 text-amber-700',
                            'APPROVED' => 'bg-emerald-100 text-emerald-700',
                            'REJECTED' => 'bg-rose-100 text-rose-700',
                            'DRAFT'    => 'bg-blue-100 text-blue-700',
                        ];
                        $badge = $status_colors[$q_status] ?? 'bg-slate-100 text-slate-700';
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">#<?= htmlspecialchars($q_id) ?></td>
                        <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($q_cust) ?></td>
                        <td class="px-6 py-4 text-slate-900 font-semibold">$<?= number_format($q_amount, 2) ?></td>
                        <td class="px-6 py-4">
                            <span class="<?= $badge ?> px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"><?= htmlspecialchars($q_status) ?></span>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($q_date) ?></td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-slate-400 hover:text-blue-600 transition-colors p-2"><i class="fa-solid fa-eye"></i></button>
                            <button class="text-slate-400 hover:text-blue-600 transition-colors p-2"><i class="fa-solid fa-edit"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400">No quotes found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-200 bg-slate-50 text-center">
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View All Quotes</a>
        </div>
    </div>

    <?php include_once '../../components/chat_widget.php' ?>
    <?php include_once '../../components/quote_modal.php'; ?>
    <script>
        document.getElementById('newQuoteBtn').addEventListener('click', () => {
            const modal = document.getElementById('quoteModal');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.querySelector('div').classList.remove('scale-95');
        });
    </script>


</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
