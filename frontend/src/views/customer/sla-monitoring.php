<?php
$page_title = "SLA Monitoring · Priority Handling Logistics";
$activePage = 'sla-monitoring';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">SLA Monitoring</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="slaSearchInput" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">
                    Help Desk <i class="fa-solid fa-headset text-xs"></i>
                </button>
                <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + Book Shipment
                </button>
            </div>
        </header>

        <!-- SLA MONITORING CONTENT BODY -->
        <div class="p-8 space-y-8 max-w-7xl w-full mx-auto">
            
            <!-- ROW 1: SLA METRIC CARDS (3 CARDS GRID) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                
                <!-- Card 1: Overall Compliance -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-semibold text-slate-500 block">Overall Compliance</span>
                    <strong class="text-4xl font-black text-slate-900 block">94%</strong>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                        <span>▲</span> 2pts vs last month
                    </div>
                </div>

                <!-- Card 2: Open Breaches -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-semibold text-slate-500 block">Open Breaches</span>
                    <strong class="text-4xl font-black text-slate-900 block">1</strong>
                    <div class="text-xs font-semibold text-red-500 truncate">
                        Customs clearance, WB-208712
                    </div>
                </div>

                <!-- Card 3: Avg. Resolution Time -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-semibold text-slate-500 block">Avg. Resolution Time</span>
                    <strong class="text-4xl font-black text-slate-900 block">6.2h</strong>
                    <div class="text-xs font-medium text-slate-500">
                        within target
                    </div>
                </div>

            </div>

            <!-- ROW 2: SPLIT MAIN AREA (BREACH LOG + SLA HEALTH) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Section: Breach Log Table (8 Cols) -->
                <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Breach Log</h3>
                        <p class="text-xs text-slate-400">Flagged tickets from the SLA engine</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                    <th class="pb-3">WAYBILL</th>
                                    <th class="pb-3">COMMITMENT</th>
                                    <th class="pb-3">FLAGGED</th>
                                    <th class="pb-3 text-right">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <!-- Row 1 -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                        PH-WB-208712
                                    </td>
                                    <td class="py-4 font-semibold text-slate-800">
                                        Transit time
                                    </td>
                                    <td class="py-4 font-mono text-slate-600">
                                        Jul 27, 06:00
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="bg-red-100 text-red-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Open</span>
                                    </td>
                                </tr>

                                <!-- Row 2 -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                        PH-WB-208601
                                    </td>
                                    <td class="py-4 font-semibold text-slate-800">
                                        Customs clearance
                                    </td>
                                    <td class="py-4 font-mono text-slate-600">
                                        Jul 20, 15:30
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Resolved</span>
                                    </td>
                                </tr>

                                <!-- Row 3 -->
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                        PH-WB-208588
                                    </td>
                                    <td class="py-4 font-semibold text-slate-800">
                                        On-time pickup
                                    </td>
                                    <td class="py-4 font-mono text-slate-600">
                                        Jul 18, 08:00
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Resolved</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Section: SLA Health Progress Card (4 Cols) -->
                <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">SLA Health</h3>
                        <p class="text-xs text-slate-400">By service commitment</p>
                    </div>

                    <div class="space-y-4 text-xs">
                        <!-- Metric 1 -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
                                <span>On-time Pickup</span>
                                <span class="text-emerald-600">97%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full w-[97%] rounded-full"></div>
                            </div>
                        </div>

                        <!-- Metric 2 -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
                                <span>Transit Time</span>
                                <span class="text-emerald-600">92%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full w-[92%] rounded-full"></div>
                            </div>
                        </div>

                        <!-- Metric 3 -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
                                <span>Customs Clearance</span>
                                <span class="text-amber-600">78%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full w-[78%] rounded-full"></div>
                            </div>
                        </div>

                        <!-- Metric 4 -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5 font-bold text-slate-800">
                                <span>Damage-free Delivery</span>
                                <span class="text-emerald-600">99%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full w-[99%] rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
