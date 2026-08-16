<?php
$pageTitle = 'Quotes - Priority Handling Logistics';
$activePage = 'quotes';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal Priority Handling Logistics support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f4f7fa] dark:bg-[#0a1628]">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-2xl font-bold italic text-slate-900 tracking-wide">Quotes</h2>

            <div class="flex items-center gap-4">
                <!-- Search Input -->
                <div class="relative w-80">
                    <i class="fa-solid fa-magnifying-glass w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        placeholder="Search leads, customer, quotes..." 
                        class="w-full pl-9 pr-4 py-1.5 text-sm bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue text-slate-700"
                    >
                </div>

                <!-- Notification Bell -->
                <button class="p-2 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 relative">
                    <i class="fa-solid fa-bell w-4 h-4"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- New Quote Button -->
                <a href="quotes.php" class="bg-brand-blue hover:bg-brand-darkblue text-white font-medium text-sm px-4 py-2 rounded-md transition inline-flex items-center gap-1.5 shadow-sm">
<i class="fa-solid fa-plus w-4"></i>
<span>New Quote</span>
</a>
            </div>
        </header>

        <!-- CONTENT BODY CONTAINER -->
        <div class="p-8 w-full">

            <!-- MAIN QUOTES CARD -->
            <div class="bg-white rounded-xl border border-gray-300/80 shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-200 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-0.5">Quotes</h3>
                        <p class="text-xs text-gray-400">Sent, pending, and accepted quotations</p>
                    </div>
                    <button class="text-xs font-bold text-brand-blue hover:text-brand-darkblue flex items-center gap-1">
                        + New quote
                    </button>
                </div>

                <!-- QUOTES TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold text-gray-400 tracking-wider">
                                <th class="py-3.5 px-6">Quote</th>
                                <th class="py-3.5 px-6">Customer</th>
                                <th class="py-3.5 px-6">Amount</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Sent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-slate-800">
                            <!-- Row 1 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-medium text-slate-900">QT-2026-0158</td>
                                <td class="py-4 px-6 font-semibold text-slate-800">Bacolod Sugar Traders</td>
                                <td class="py-4 px-6 font-bold text-slate-900">₱76,200</td>
                                <td class="py-4 px-6">
                                    <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Awaiting Reply
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-slate-600 font-medium">Jul 30, 2026</td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-medium text-slate-900">QT-2026-0157</td>
                                <td class="py-4 px-6 font-semibold text-slate-800">Davao Coco Exports</td>
                                <td class="py-4 px-6 font-bold text-slate-900">₱95,300</td>
                                <td class="py-4 px-6">
                                    <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Awaiting Reply
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-slate-600 font-medium">Jul 30, 2026</td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-medium text-slate-900">QT-2026-0151</td>
                                <td class="py-4 px-6 font-semibold text-slate-800">Iloilo AgriTrade</td>
                                <td class="py-4 px-6 font-bold text-slate-900">₱34,500</td>
                                <td class="py-4 px-6">
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Accepted
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-slate-600 font-medium">Jul 26, 2026</td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-medium text-slate-900">QT-2026-0144</td>
                                <td class="py-4 px-6 font-semibold text-slate-800">Tarlac Grain Millers</td>
                                <td class="py-4 px-6 font-bold text-slate-900">₱76,200</td>
                                <td class="py-4 px-6">
                                    <span class="bg-slate-200 text-slate-600 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Draft
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-slate-600 font-medium">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bottom empty area inside card -->
                <div class="p-12"></div>
            </div>

        </div>
    
</main>




<script src="js/main.js"></script>
</body>
</html>
