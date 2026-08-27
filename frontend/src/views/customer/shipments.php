<?php
$page_title = "Shipments · Priority Handling Logistics";
$activePage = 'shipments';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Shipments</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="shipmentSearchInput" onkeyup="searchShipmentsTable()" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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

        <!-- SHIPMENTS CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <!-- STATUS FILTER TABS -->
            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-200/50 rounded-2xl w-fit text-xs font-semibold">
                <button onclick="filterShipments('all', this)" class="filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm transition-all">All (12)</button>
                <button onclick="filterShipments('in-transit', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">In Transit (7)</button>
                <button onclick="filterShipments('customs', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Customs (1)</button>
                <button onclick="filterShipments('delayed', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Delayed (2)</button>
                <button onclick="filterShipments('delivered', this)" class="filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Delivered (48)</button>
            </div>

            <!-- MAIN SHIPMENTS TABLE CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">All Shipments</h3>
                        <p class="text-xs text-slate-400">Full waybill history for Robles Cargo Corp.</p>
                    </div>
                    <button onclick="exportShipmentsCSV()" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-file-export"></i> Export CSV
                    </button>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="shipmentsTable">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="pb-3">WAYBILL</th>
                                <th class="pb-3">ROUTE</th>
                                <th class="pb-3">CARRIER</th>
                                <th class="pb-3">STATUS</th>
                                <th class="pb-3 text-right">ETA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Row 1 -->
                            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="in-transit">
                                <td class="py-4">
                                    <strong class="font-mono text-slate-900 text-xs block">PH-WB-208841</strong>
                                    <span class="text-[10px] text-slate-400">40ft container · Reefer</span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">Manila &rarr; Cebu</td>
                                <td class="py-4 text-slate-700 font-medium">Trans-Pacific Lines</td>
                                <td class="py-4">
                                    <span class="bg-blue-100 text-blue-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● In Transit</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 29, 14:00</td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="customs">
                                <td class="py-4">
                                    <strong class="font-mono text-slate-900 text-xs block">PH-WB-208835</strong>
                                    <span class="text-[10px] text-slate-400">20ft container · Dry van</span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">Cebu &rarr; Manila</td>
                                <td class="py-4 text-slate-700 font-medium">2GO Freight</td>
                                <td class="py-4">
                                    <span class="bg-amber-100 text-amber-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● Customs</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 30, 09:00</td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="delivered">
                                <td class="py-4">
                                    <strong class="font-mono text-slate-900 text-xs block">PH-WB-208790</strong>
                                    <span class="text-[10px] text-slate-400">LCL · Palletized</span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">Davao &rarr; Manila</td>
                                <td class="py-4 text-slate-700 font-medium">Trans-Pacific Lines</td>
                                <td class="py-4">
                                    <span class="bg-emerald-100 text-emerald-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● Delivered</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 25, 11:20</td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="delayed">
                                <td class="py-4">
                                    <strong class="font-mono text-slate-900 text-xs block">PH-WB-208712</strong>
                                    <span class="text-[10px] text-slate-400">40ft container · Dry van</span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">Manila &rarr; Iloilo</td>
                                <td class="py-4 text-slate-700 font-medium">Sulpicio Lines</td>
                                <td class="py-4">
                                    <span class="bg-red-100 text-red-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● Delayed</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-red-500">Jul 27, 18:00</td>
                            </tr>

                            <!-- Row 5 -->
                            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="in-transit">
                                <td class="py-4">
                                    <strong class="font-mono text-slate-900 text-xs block">PH-WB-208699</strong>
                                    <span class="text-[10px] text-slate-400">20ft container · Reefer</span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">Manila &rarr; Cagayan de Oro</td>
                                <td class="py-4 text-slate-700 font-medium">2GO Freight</td>
                                <td class="py-4">
                                    <span class="bg-blue-100 text-blue-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● In Transit</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-700">Aug 2, 07:30</td>
                            </tr>

                            <!-- Row 6 -->
                            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="in-transit">
                                <td class="py-4">
                                    <strong class="font-mono text-slate-900 text-xs block">PH-WB-208650</strong>
                                    <span class="text-[10px] text-slate-400">FCL · Dry van</span>
                                </td>
                                <td class="py-4 font-semibold text-slate-800">Manila &rarr; Bacolod</td>
                                <td class="py-4 text-slate-700 font-medium">Trans-Pacific Lines</td>
                                <td class="py-4">
                                    <span class="bg-blue-100 text-blue-700 font-semibold px-2.5 py-1 rounded-full text-[10px]">● In Transit</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-700">Jul 31, 10:00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>

    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
