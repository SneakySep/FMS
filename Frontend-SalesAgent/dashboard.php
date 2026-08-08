<?php
$pageTitle = 'SwiftFreight - Agent Dashboard';
$activePage = 'dashboard';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal SwiftFreight support channel — how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Agent Overview</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Search waybill, customer, or ticket..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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
                <button onclick="openNewShipmentModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + New Shipment
                </button>
            </div>
        </header>

        <!-- DASHBOARD CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- KPI CARDS GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Card 1: Active Shipments -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 block">Active Shipments</span>
                        <div class="w-8 h-8 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-boxes-stacked"></i></div>
                    </div>
                    <strong id="dashboardActiveShipments" class="text-4xl font-black text-slate-900 block">0</strong>
                    <a href="shipmentsphp" class="text-xs font-semibold text-brand-blue hover:underline inline-flex items-center gap-1">Manage shipments <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <!-- Card 2: In Transit -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 block">In Transit</span>
                        <div class="w-8 h-8 bg-sky-100 text-sky-600 rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-route"></i></div>
                    </div>
                    <strong id="dashboardInTransit" class="text-4xl font-black text-slate-900 block">0</strong>
                    <a href="trackingphp" class="text-xs font-semibold text-brand-blue hover:underline inline-flex items-center gap-1">Track fleet <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <!-- Card 3: Delayed / At Risk -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 block">Delayed / At Risk</span>
                        <div class="w-8 h-8 bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <strong id="dashboardDelayed" class="text-4xl font-black text-slate-900 block">0</strong>
                    <a href="sla-monitoringphp" class="text-xs font-semibold text-red-500 hover:underline inline-flex items-center gap-1">Review SLA <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <!-- Card 4: Open Breaches -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 block">Open SLA Breaches</span>
                        <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-clock"></i></div>
                    </div>
                    <strong id="dashboardOpenBreachesKpi" class="text-4xl font-black text-slate-900 block">0</strong>
                    <a href="sla-monitoringphp" class="text-xs font-semibold text-amber-600 hover:underline inline-flex items-center gap-1">Resolve breaches <i class="fa-solid fa-arrow-right"></i></a>
                </div>

            </div>

            <!-- QUICK ACTIONS + RECENT TICKETS ROW -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- QUICK ACTIONS PANEL (5 Cols) -->
                <div class="lg:col-span-5 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 reveal">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Quick Actions</h3>
                        <p class="text-xs text-slate-400">Control center for the customer portal</p>
                    </div>

                    <div class="space-y-2.5">
                        <button onclick="openNewShipmentModal()" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-xl transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-brand-blue text-white rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-plus"></i></div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900">Create Shipment</h4>
                                    <p class="text-[10px] text-slate-400">Push a new waybill to the customer portal</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-300"></i>
                        </button>

                        <button onclick="location.href='customersphp'; openNewCustomerModal()" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-xl transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-user-plus"></i></div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900">New Customer Account</h4>
                                    <p class="text-[10px] text-slate-400">Provision portal access for a client</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-300"></i>
                        </button>

                        <button onclick="location.href='sla-monitoringphp'" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-xl transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-amber-500 text-white rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-clock"></i></div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900">Resolve SLA Breaches</h4>
                                    <p class="text-[10px] text-slate-400">Handle open commitments at risk</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-300"></i>
                        </button>

                        <button onclick="location.href='ticketsphp'" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-xl transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-sky-500 text-white rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-headset"></i></div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900">Respond to Tickets</h4>
                                    <p class="text-[10px] text-slate-400">Reply to customer support requests</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-300"></i>
                        </button>

                        <button onclick="location.href='documentsphp'" class="w-full flex items-center justify-between p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-100 rounded-xl transition-colors text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-violet-500 text-white rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-file-circle-plus"></i></div>
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900">Upload Document</h4>
                                    <p class="text-[10px] text-slate-400">Publish a document to the customer vault</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-slate-300"></i>
                        </button>
                    </div>
                </div>

                <!-- RECENT TICKETS (7 Cols) -->
                <div class="lg:col-span-7 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5 reveal">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Recent Support Tickets</h3>
                            <p class="text-xs text-slate-400">Live from the Notification Hub</p>
                        </div>
                        <a href="ticketsphp" class="text-xs font-semibold text-brand-blue hover:underline">View all</a>
                    </div>

                    <div id="recentTicketsContainer" class="space-y-3"></div>
                </div>

            </div>

            <!-- RECENT MANIFEST TABLE -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5 reveal">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Recent Shipment Manifest</h3>
                        <p class="text-xs text-slate-400">Controlled by the Sales Agent, shown to the customer</p>
                    </div>
                    <a href="shipmentsphp" class="text-xs font-semibold text-brand-blue hover:underline inline-flex items-center gap-1">Manage all <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="pb-3">WAYBILL</th>
                                <th class="pb-3">JOURNEY</th>
                                <th class="pb-3">STATUS</th>
                                <th class="pb-3 text-right">ETA</th>
                            </tr>
                        </thead>
                        <tbody id="dashboardManifestBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- NEW SHIPMENT MODAL -->
    <div id="shipmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-8 space-y-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Create New Shipment</h3>
                    <p class="text-xs text-slate-400">This will be pushed to the customer portal instantly</p>
                </div>
                <button onclick="closeModal('shipmentModal')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form onsubmit="createNewShipment(event)" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Waybill Number</label>
                        <input type="text" id="newWaybill" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Container Type</label>
                        <input type="text" id="newShipmentType" required placeholder="e.g. 40ft container · Reefer" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Route</label>
                    <input type="text" id="newRoute" required placeholder="Manila → Cebu" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Carrier</label>
                        <input type="text" id="newCarrier" required placeholder="Trans-Pacific Lines" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select id="newStatus" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                            <option value="In Transit">In Transit</option>
                            <option value="Customs">Customs</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Delayed">Delayed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ETA</label>
                    <input type="text" id="newEta" required placeholder="Jul 29, 14:00" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="closeModal('shipmentModal')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Create Shipment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>
</html>