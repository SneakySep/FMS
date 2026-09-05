<?php
$page_title = "Rate Search - PRIORITY HANDLING";
include_once '../../includes/header.php';
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

    <?php 
    $header_title = "Rate Search";
    $header_subtitle = "Find, compare, and book the best freight rates instantly.";
    include_once 'components/dashboard_header.php'; 
    ?>

    <div class="p-6 lg:p-8">

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="dashboard-card p-6 flex items-center space-x-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Total Searches</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">1,248</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="dashboard-card p-6 flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Avg. Savings</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">14.2%</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="dashboard-card p-6 flex items-center space-x-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Active Bookings</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">45</p>
            </div>
        </div>
    </div>

    <!-- RATE SEARCH FORM -->
    <div class="dashboard-card p-6 mb-8">
        <h2 class="text-lg font-semibold text-slate-800 mb-6">New Search</h2>
        <form id="rateSearchForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Origin -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Origin</label>
                <input type="text" placeholder="City, Port, or ZIP" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <!-- Destination -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Destination</label>
                <input type="text" placeholder="City, Port, or ZIP" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <!-- Cargo Type -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Cargo Type</label>
                <select class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all bg-white">
                    <option>General Cargo</option>
                    <option>Refrigerated</option>
                    <option>Hazardous</option>
                </select>
            </div>
            <!-- Search Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:bg-blue-700 shadow-md transition-all active:scale-95">
                    Search Rates
                </button>
            </div>
        </form>
    </div>

    <!-- RESULTS TABLE -->
    <div class="dashboard-card overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Search Results</h2>
            <span class="text-sm text-slate-500">Found 2 results</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-xs">

<!-- Initialize Lucide Icons -->
<script>
    lucide.createIcons();
</script>

                    <tr>
                        <th class="px-6 py-4 font-semibold">Carrier</th>
                        <th class="px-6 py-4 font-semibold">Service</th>
                        <th class="px-6 py-4 font-semibold">Est. Transit</th>
                        <th class="px-6 py-4 font-semibold">Rate</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">OceanLine Express</td>
                        <td class="px-6 py-4">FCL - Standard</td>

<!-- Page Specific JS -->
<script src="/assets/js/sales_agent/rates.js"></script>

                        <td class="px-6 py-4">12-14 Days</td>
                        <td class="px-6 py-4 font-bold text-blue-600">$2,450</td>
                        <td class="px-6 py-4 text-right">
                            <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium py-1.5 px-3 rounded-md transition-colors">
                                Book Now
                            </button>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">AirSpeed Cargo</td>
                        <td class="px-6 py-4">Air Freight - Express</td>
                        <td class="px-6 py-4">2-3 Days</td>
                        <td class="px-6 py-4 font-bold text-blue-600">$5,800</td>
                        <td class="px-6 py-4 text-right">
                            <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium py-1.5 px-3 rounded-md transition-colors">
                                Book Now
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</main>

</div>
<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
