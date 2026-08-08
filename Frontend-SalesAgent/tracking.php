<?php
$pageTitle = 'SwiftFreight - Shipment Tracking';
$activePage = 'tracking';
$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help with tracking waybills? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Live Tracking</h2>
            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="trackingSearchInput" placeholder="Search a waybill..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all" onkeyup="searchTrackingWaybills(this.value)">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">Help Desk <i class="fa-solid fa-headset text-xs"></i></button>
                <button onclick="openNewShipmentModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">+ New Shipment</button>
            </div>
        </header>

        <!-- TRACKING CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- WAYBILL SELECTOR -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 reveal">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Select a Waybill</h3>
                        <p class="text-xs text-slate-400">Choose a shipment to manage its tracking timeline</p>
                    </div>
                    <select id="waybillSelect" onchange="switchTrackWaybill(this.value)" class="bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs font-mono focus:outline-none focus:border-brand-blue transition-colors cursor-pointer min-w-[280px]">
                    </select>
                </div>
            </div>

            <!-- MAP + TIMELINE SPLIT -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT: MAP (7 Cols) -->
                <div class="lg:col-span-7 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 reveal">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Route Map</h3>
                            <p class="text-xs text-slate-400">Live positioning data</p>
                        </div>
                        <span id="routeMapBadge" class="bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded-full text-[10px]">● In Transit</span>
                    </div>
                    <div id="trackingMap" class="w-full h-[420px] rounded-xl overflow-hidden border border-slate-200 bg-slate-900"></div>
                </div>

                <!-- RIGHT: TIMELINE (5 Cols) -->
                <div class="lg:col-span-5 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6 reveal">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Tracking Timeline</h3>
                            <p class="text-xs text-slate-400">Status events visible to the customer</p>
                        </div>
                        <button onclick="alert('Milestone updates are synced with shipment status changes on the Shipments page.')" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1"><i class="fa-solid fa-pen"></i> Edit events</button>
                    </div>

                    <div class="space-y-0">
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Next Checkpoint</p>
                            <p id="nextCheckpointText" class="text-sm font-extrabold text-slate-900">Cebu (Jul 29, 14:00)</p>
                        </div>

                        <div id="timelineContainer" class="relative pl-8 py-4 space-y-8 border-l-2 border-slate-100 ml-4"></div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- NEW SHIPMENT MODAL (Compact) -->
    <div id="shipmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-8 space-y-6">
            <div class="flex justify-between items-start"><div><h3 class="text-base font-extrabold text-slate-900">Create New Shipment</h3><p class="text-xs text-slate-400">This will be pushed to the customer portal instantly</p></div><button onclick="closeModal('shipmentModal')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fa-solid fa-xmark"></i></button></div>
            <form onsubmit="createNewShipment(event)" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5"><div><label class="block text-xs font-semibold text-slate-600 mb-1">Waybill Number</label><input type="text" id="newWaybill" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors font-mono"></div><div><label class="block text-xs font-semibold text-slate-600 mb-1">Container Type</label><input type="text" id="newShipmentType" required placeholder="e.g. 40ft container · Reefer" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors"></div></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Route</label><input type="text" id="newRoute" required placeholder="Manila → Cebu" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5"><div><label class="block text-xs font-semibold text-slate-600 mb-1">Carrier</label><input type="text" id="newCarrier" required placeholder="Trans-Pacific Lines" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors"></div><div><label class="block text-xs font-semibold text-slate-600 mb-1">Status</label><select id="newStatus" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer"><option value="In Transit">In Transit</option><option value="Customs">Customs</option><option value="Delivered">Delivered</option><option value="Delayed">Delayed</option></select></div></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">ETA</label><input type="text" id="newEta" required placeholder="Jul 29, 14:00" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors"></div>
                <div class="flex justify-end gap-3 pt-3"><button type="button" onclick="closeModal('shipmentModal')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button><button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Create Shipment</button></div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>
</html>