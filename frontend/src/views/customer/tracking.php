$page_title = "Live Tracking · Priority Handling Logistics";
<?php
$activePage = 'tracking';
$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Live Tracking</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="liveTrackInput" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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

        <!-- LIVE TRACKING CONTENT BODY -->
        <div class="p-8 max-w-7xl w-full mx-auto space-y-6">
            
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-8">
                
                <!-- Card Header with Waybill Dropdown -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Live Tracking</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Real-time status, milestone timeline, and GPS map tracking</p>
                    </div>

                    <!-- Waybill Selector Dropdown -->
                    <div class="flex items-center gap-2">
                        <select id="waybillSelect" onchange="switchTrackWaybill(this.value)" class="bg-slate-50 border border-slate-200 text-slate-900 font-mono text-xs font-bold px-3 py-2 rounded-xl focus:outline-none focus:border-brand-blue cursor-pointer">
                            <option value="PH-WB-208841">PH-WB-208841 (Manila → Cebu)</option>
                            <option value="PH-WB-208835">PH-WB-208835 (Cebu → Manila)</option>
                            <option value="PH-WB-208790">PH-WB-208790 (Davao → Manila)</option>
                        </select>
                    </div>
                </div>

                <!-- MAIN SPLIT GRID: TIMELINE (LEFT) + LEAFLET MAP (RIGHT) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    
                    <!-- Left Side: Vertical Stepper Milestone Timeline (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <div id="timelineContainer" class="relative pl-6 space-y-8 border-l-2 border-slate-200">
                            <!-- Dynamic Milestones Rendered by JS -->
                        </div>
                    </div>

                    <!-- Right Side: Dark Leaflet Route Map (5 Cols) -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-extrabold text-slate-900">GPS Route Map</span>
                            <span id="routeMapBadge" class="bg-blue-100 text-brand-blue font-bold px-2.5 py-0.5 rounded-full text-[10px]">● Vessel In Transit</span>
                        </div>

                        <!-- Map Canvas Container -->
                        <div class="relative h-[360px] rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-950">
                            <div id="trackingMap" class="w-full h-full z-0"></div>
                        </div>

                        <!-- Checkpoint Box -->
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-600 flex justify-between items-center">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Next Checkpoint</span>
                                <strong id="nextCheckpointText" class="text-slate-900 text-xs font-bold">Cebu Port Terminal (Jul 29)</strong>
                            </div>
                            <button onclick="alert('Downloading Bill of Lading PDF...')" class="bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-file-pdf text-red-500"></i> e-BOL PDF
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/assets/js/customer/customer_dashboard.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
