<?php
$pageTitle = 'SwiftFreight - Manage Shipments';
$activePage = 'shipments';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help managing shipments? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Shipment Control</h2>
            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="shipmentSearchInput" onkeyup="searchShipmentsTable()" placeholder="Search a waybill, route, or carrier..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="exportShipmentsCSV()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-slate-200">
                    <i class="fa-solid fa-file-export text-xs"></i> Export
                </button>
                <button onclick="openNewShipmentModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + New Shipment
                </button>
            </div>
        </header>

        <!-- SHIPMENTS CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- FILTER TABS -->
            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <button onclick="filterShipments('all', this)" class="filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm">All</button>
                <button onclick="filterShipments('in-transit', this)" class="filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">In Transit</button>
                <button onclick="filterShipments('customs', this)" class="filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">Customs</button>
                <button onclick="filterShipments('delivered', this)" class="filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">Delivered</button>
                <button onclick="filterShipments('delayed', this)" class="filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">Delayed</button>
            </div>

            <!-- SHIPMENTS TABLE CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">All Shipments</h3>
                        <p class="text-xs text-slate-400">Control what is shown on the customer dashboard</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="pb-3">WAYBILL</th>
                                <th class="pb-3">ROUTE</th>
                                <th class="pb-3">CARRIER</th>
                                <th class="pb-3">STATUS</th>
                                <th class="pb-3 text-right">ETA</th>
                                <th class="pb-3 text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="shipmentsTableBody" class="divide-y divide-slate-100"></tbody>
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
                <button onclick="closeModal('shipmentModal')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fa-solid fa-xmark"></i></button>
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

    <!-- EDIT SHIPMENT MODAL -->
    <div id="editShipmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-8 space-y-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Edit Shipment</h3>
                    <p class="text-xs text-slate-400">Changes are pushed to the customer portal</p>
                </div>
                <button onclick="closeModal('editShipmentModal')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form onsubmit="saveEditedShipment(event)" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Waybill Number</label>
                    <input type="text" id="editWaybill" readonly class="w-full bg-slate-100 border border-slate-200 text-slate-500 rounded-xl px-4 py-2.5 text-xs font-mono cursor-not-allowed">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Container Type</label>
                        <input type="text" id="editShipmentType" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Carrier</label>
                        <input type="text" id="editCarrier" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Route</label>
                    <input type="text" id="editRoute" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select id="editStatus" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                            <option value="In Transit">In Transit</option>
                            <option value="Customs">Customs</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Delayed">Delayed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">ETA</label>
                        <input type="text" id="editEta" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="closeModal('editShipmentModal')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>
</html>