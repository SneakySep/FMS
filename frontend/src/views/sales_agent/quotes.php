<?php
$page_title = "Quotes Dashboard - PRIORITY HANDLING";
$activePage = 'quotes';

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Quote Stats (Placeholder for actual API endpoint)
// $stats_res  = make_api_request('/api/v1/quotes/stats', 'GET');
// $stats_data = $stats_res['data'] ?? [];
$stats_data = ['total' => 120, 'pending' => 45, 'approved' => 60, 'rejected' => 15]; // Simulated Data
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
                    <!-- Simulated Row -->
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">#Q-2026-001</td>
                        <td class="px-6 py-4 text-slate-700">Acme Corp</td>
                        <td class="px-6 py-4 text-slate-900 font-semibold">$12,500.00</td>
                        <td class="px-6 py-4">
                            <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Pending</span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">Aug 28, 2026</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-slate-400 hover:text-blue-600 transition-colors p-2"><i class="fa-solid fa-eye"></i></button>
                            <button class="text-slate-400 hover:text-blue-600 transition-colors p-2"><i class="fa-solid fa-edit"></i></button>
                        </td>
                    </tr>
                    <!-- End Simulated Row -->
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
