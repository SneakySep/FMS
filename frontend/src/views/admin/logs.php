<?php
$page_title = "System Logs & Audit Trail · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

    <!-- TOP HEADER -->
    <?php include_once 'components/top_header.php'; ?>

    <!-- PAGE TITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">System Logs & Audit Trail</h1>
        <p class="text-slate-500 text-sm">Monitor system access, data modifications, and administrative events.</p>
    </div>

    <!-- LOG STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Failed Logins (24h)</h3>
            <p class="text-3xl font-black text-red-600 mt-2">12</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Sensitive Data Exports</h3>
            <p class="text-3xl font-black text-amber-600 mt-2">3</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Admin Actions</h3>
            <p class="text-3xl font-black text-blue-600 mt-2">45</p>
        </div>
    </div>

    <!-- LOG TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Recent Events</h2>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                Export Audit Report
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold">
                    <tr>
                        <th class="p-4">Timestamp</th>
                        <th class="p-4">User</th>
                        <th class="p-4">Action</th>
                        <th class="p-4">IP Address</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <!-- Data will be populated via JS -->
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- Logs JS -->
<script src="../../../assets/js/admin/logs.js"></script>
