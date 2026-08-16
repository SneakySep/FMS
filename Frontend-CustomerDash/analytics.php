<?php
$pageTitle = 'Priority Handling Logistics - BI Analytics';
$activePage = 'analytics';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need assistance with route analytics or shipment volume reports? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">BI Analytics</h2>

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

        <!-- BI ANALYTICS CONTENT BODY -->
        <div class="p-8 space-y-8 max-w-7xl w-full mx-auto">
            
            <!-- SPLIT MAIN AREA (BAR CHART + TOP ROUTES) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Shipments per Month Bar Chart Card (8 Cols) -->
                <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Shipments per Month</h3>
                        <p class="text-xs text-slate-400">Last 6 months</p>
                    </div>

                    <!-- Custom HTML/CSS Bar Chart -->
                    <div class="pt-8 pb-4">
                        <div class="h-64 flex items-end justify-between gap-4 sm:gap-8 px-4 border-b border-slate-100">
                            
                            <!-- Feb Bar -->
                            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[62%] group-hover:bg-brand-darkblue relative">
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">28 Shipments</div>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 mt-2">Feb</span>
                            </div>

                            <!-- Mar Bar -->
                            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[72%] group-hover:bg-brand-darkblue relative">
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">34 Shipments</div>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 mt-2">Mar</span>
                            </div>

                            <!-- Apr Bar -->
                            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[58%] group-hover:bg-brand-darkblue relative">
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">26 Shipments</div>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 mt-2">Apr</span>
                            </div>

                            <!-- May Bar -->
                            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[84%] group-hover:bg-brand-darkblue relative">
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">42 Shipments</div>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 mt-2">May</span>
                            </div>

                            <!-- Jun Bar -->
                            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[76%] group-hover:bg-brand-darkblue relative">
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">38 Shipments</div>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 mt-2">Jun</span>
                            </div>

                            <!-- Jul Bar -->
                            <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                <div class="w-full max-w-[42px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[92%] group-hover:bg-brand-darkblue relative">
                                    <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">48 Shipments</div>
                                </div>
                                <span class="text-xs font-semibold text-slate-400 mt-2">Jul</span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Column: Top Routes Card (4 Cols) -->
                <div class="lg:col-span-4 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Top Routes</h3>
                        <p class="text-xs text-slate-400">By shipment volume</p>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        <!-- Route 1 -->
                        <div class="py-3.5 flex justify-between items-center">
                            <strong class="text-slate-900 font-extrabold">Manila &rarr; Cebu</strong>
                            <span class="font-mono text-slate-500 font-bold">18</span>
                        </div>

                        <!-- Route 2 -->
                        <div class="py-3.5 flex justify-between items-center">
                            <strong class="text-slate-900 font-extrabold">Manila &rarr; Davao</strong>
                            <span class="font-mono text-slate-500 font-bold">12</span>
                        </div>

                        <!-- Route 3 -->
                        <div class="py-3.5 flex justify-between items-center">
                            <strong class="text-slate-900 font-extrabold">Cebu &rarr; Manila</strong>
                            <span class="font-mono text-slate-500 font-bold">9</span>
                        </div>

                        <!-- Route 4 -->
                        <div class="py-3.5 flex justify-between items-center">
                            <strong class="text-slate-900 font-extrabold">Manila &rarr; Iloilo</strong>
                            <span class="font-mono text-slate-500 font-bold">7</span>
                        </div>

                        <!-- Route 5 -->
                        <div class="py-3.5 flex justify-between items-center">
                            <strong class="text-slate-900 font-extrabold">Manila &rarr; Cagayan de Oro</strong>
                            <span class="font-mono text-slate-500 font-bold">5</span>
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