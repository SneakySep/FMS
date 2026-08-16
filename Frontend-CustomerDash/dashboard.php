<?php
$pageTitle = 'Priority Handling Logistics - Customer Portal';
$activePage = 'dashboard';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need assistance with your manifest or SLA reports? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Dashboard</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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

        <!-- DASHBOARD BODY -->
        <div class="p-8 space-y-8 max-w-7xl w-full mx-auto">
            
            <!-- ROW 1: METRIC KPI CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-600">Active Shipments</span>
                        <div class="text-brand-blue text-sm"><i class="fa-solid fa-box-archive"></i></div>
                    </div>
                    <strong class="text-4xl font-extrabold text-slate-900 block">12</strong>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                        <span>▲</span> 3 new this week
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-600">In Transit</span>
                        <div class="text-emerald-500 text-sm"><i class="fa-solid fa-truck"></i></div>
                    </div>
                    <strong class="text-4xl font-extrabold text-slate-900 block">7</strong>
                    <div class="text-xs font-medium text-slate-500">on schedule</div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-600">Delayed</span>
                        <div class="text-red-400 text-sm"><i class="fa-solid fa-clock"></i></div>
                    </div>
                    <strong class="text-4xl font-extrabold text-slate-900 block">2</strong>
                    <div class="text-xs font-semibold text-red-600 flex items-center gap-1">
                        <span>▲</span> SLA at risk
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-600">Delivered (30d)</span>
                        <div class="text-amber-500 text-sm"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                    <strong class="text-4xl font-extrabold text-slate-900 block">48</strong>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                        <span>▲</span> 12% vs last month
                    </div>
                </div>
            </div>

            <!-- ROW 2: MANIFEST TABLE & SLA / DOCUMENTS -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Shipment Manifest (8 Cols) -->
                <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Shipment Manifest</h3>
                            <p class="text-xs text-slate-400">Live status across all active waybills</p>
                        </div>
                        <a href="shipmentsphp" class="text-xs font-semibold text-brand-blue hover:underline">View all &rarr;</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                    <th class="pb-3">WAYBILL</th>
                                    <th class="pb-3">ROUTE</th>
                                    <th class="pb-3">STATUS</th>
                                    <th class="pb-3 text-right">ETA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4">
                                        <strong class="font-mono text-slate-900 text-xs block">PH-WB-208841</strong>
                                        <span class="text-[10px] text-slate-400">40ft container · Reefer</span>
                                    </td>
                                    <td class="py-4 w-40">
                                        <div class="flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>
                                            <div class="flex-1 h-0.5 bg-brand-blue rounded"></div>
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="bg-blue-100 text-blue-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● In Transit</span>
                                    </td>
                                    <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 29, 14:00</td>
                                </tr>

                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4">
                                        <strong class="font-mono text-slate-900 text-xs block">PH-WB-208835</strong>
                                        <span class="text-[10px] text-slate-400">20ft container · Dry van</span>
                                    </td>
                                    <td class="py-4 w-40">
                                        <div class="flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-blue"></span>
                                            <div class="flex-1 h-0.5 bg-brand-blue rounded"></div>
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="bg-amber-100 text-amber-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● Customs</span>
                                    </td>
                                    <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 30, 09:00</td>
                                </tr>

                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4">
                                        <strong class="font-mono text-slate-900 text-xs block">PH-WB-208790</strong>
                                        <span class="text-[10px] text-slate-400">LCL · Palletized</span>
                                    </td>
                                    <td class="py-4 w-40">
                                        <div class="flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <div class="flex-1 h-0.5 bg-emerald-500 rounded"></div>
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="bg-emerald-100 text-emerald-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● Delivered</span>
                                    </td>
                                    <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 25, 11:20</td>
                                </tr>

                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4">
                                        <strong class="font-mono text-slate-900 text-xs block">PH-WB-208712</strong>
                                        <span class="text-[10px] text-slate-400">40ft container · Dry van</span>
                                    </td>
                                    <td class="py-4 w-40">
                                        <div class="flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            <div class="flex-1 h-0.5 bg-red-400 rounded"></div>
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="bg-red-100 text-red-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● Delayed</span>
                                    </td>
                                    <td class="py-4 text-right font-mono font-medium text-red-500">Jul 27, 18:00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Side Section: SLA Health & Documents (4 Cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- SLA Health Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4" id="sla">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">SLA Health</h3>
                            <p class="text-xs text-slate-400">By service commitment</p>
                        </div>

                        <div class="space-y-3.5 text-xs">
                            <div>
                                <div class="flex justify-between items-center mb-1 font-semibold text-slate-700">
                                    <span>On-time Pickup</span>
                                    <span class="text-emerald-600 font-bold">97%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[97%] rounded-full"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1 font-semibold text-slate-700">
                                    <span>Transit Time</span>
                                    <span class="text-emerald-600 font-bold">92%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[92%] rounded-full"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1 font-semibold text-slate-700">
                                    <span>Customs Clearance</span>
                                    <span class="text-amber-600 font-bold">78%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-full w-[78%] rounded-full"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1 font-semibold text-slate-700">
                                    <span>Damage-free Delivery</span>
                                    <span class="text-emerald-600 font-bold">99%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[99%] rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Documents Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4" id="documents">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Recent Documents</h3>
                                <p class="text-xs text-slate-400">Bills of lading & customs forms</p>
                            </div>
                            <a href="javascript:void(0)" class="text-xs font-semibold text-brand-blue hover:underline">Open &rarr;</a>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/50 transition-colors group cursor-pointer" onclick="alert('Downloading Bill of Lading WB-208841 (PDF)...')">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue transition-colors">Bill of Lading — WB-208841</h4>
                                        <span class="text-[10px] text-slate-400">PDF · Uploaded Jul 26</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-arrow-down-long text-slate-400 text-xs"></i>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/50 transition-colors group cursor-pointer" onclick="alert('Downloading Customs Declaration WB-208835 (PDF)...')">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue transition-colors">Customs Declaration — WB-208835</h4>
                                        <span class="text-[10px] text-slate-400">PDF · Uploaded Jul 25</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-arrow-down-long text-slate-400 text-xs"></i>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/50 transition-colors group cursor-pointer" onclick="alert('Downloading Proof of Delivery WB-208790 (PDF)...')">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue transition-colors">Proof of Delivery — WB-208790</h4>
                                        <span class="text-[10px] text-slate-400">PDF · Uploaded Jul 25</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-arrow-down-long text-slate-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script src="js/store-bridge.js"></script>
</body>
</html>
