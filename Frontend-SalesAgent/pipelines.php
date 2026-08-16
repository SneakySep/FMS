<?php
$pageTitle = 'Kanban Pipelines - Priority Handling Logistics';
$activePage = 'pipelines';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal Priority Handling Logistics support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f4f7fa] dark:bg-[#0a1628]">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-2xl font-bold italic text-slate-900 tracking-wide">Kanban Pipeline</h2>

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
        <div class="p-8 space-y-6">

            <!-- PIPELINE HEADER CARD -->
            <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900">Sales Pipeline</h3>
                <p class="text-xs text-gray-400 mt-0.5">Drag a card to move it to the next stage</p>
            </div>

            <!-- KANBAN COLUMNS CONTAINER (5 COLUMNS) -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-start">

                <!-- COLUMN 1: NEW INQUIRY -->
                <div class="bg-slate-100/70 p-3 rounded-xl min-h-[500px]">
                    <div class="flex justify-between items-center mb-3 px-1">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">NEW INQUIRY</span>
                        <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">4</span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Card 1 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Zambales Marine Co.</h4>
                            <div class="text-xs text-gray-400 mt-0.5">P. Ilagan</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱22,400</div>
                        </div>

                        <!-- Card 2 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Baguio Craft Exports</h4>
                            <div class="text-xs text-gray-400 mt-0.5">N. Cariño</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱15,900</div>
                        </div>

                        <!-- Card 3 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Palawan Seafoods Inc.</h4>
                            <div class="text-xs text-gray-400 mt-0.5">D. Aquino</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱67,000</div>
                        </div>

                        <!-- Card 4 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80 relative">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-bold text-slate-900">Meridian Textiles</h4>
                                <span class="bg-rose-100 text-rose-600 text-[10px] font-bold px-1.5 py-0.5 rounded">0.58</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">A. Santos</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱58,000</div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: QUALIFYING -->
                <div class="bg-slate-100/70 p-3 rounded-xl min-h-[500px]">
                    <div class="flex justify-between items-center mb-3 px-1">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">QUALIFYING</span>
                        <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">3</span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Card 1 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-bold text-slate-900">Robles Cargo Corp.</h4>
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded">0.69</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">R. Robles</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱120,000</div>
                        </div>

                        <!-- Card 2 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Tarlac Grain Millers</h4>
                            <div class="text-xs text-gray-400 mt-0.5">E. Domingo</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱48,000</div>
                        </div>

                        <!-- Card 3 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Batangas Steelworks</h4>
                            <div class="text-xs text-gray-400 mt-0.5">V. Tan</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱210,000</div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: QUOTE SENT -->
                <div class="bg-slate-100/70 p-3 rounded-xl min-h-[500px]">
                    <div class="flex justify-between items-center mb-3 px-1">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">QUOTE SENT</span>
                        <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">2</span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Card 1 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Bacolod Sugar Traders</h4>
                            <div class="text-xs text-gray-400 mt-0.5">L. Ong</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱76,200</div>
                        </div>

                        <!-- Card 2 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Davao Coco Exports</h4>
                            <div class="text-xs text-gray-400 mt-0.5">F. Reyes</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱95,300</div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 4: NEGOTIATION -->
                <div class="bg-slate-100/70 p-3 rounded-xl min-h-[500px]">
                    <div class="flex justify-between items-center mb-3 px-1">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">NEGOTIATION</span>
                        <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">1</span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Card 1 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Cagayan Fresh Produce</h4>
                            <div class="text-xs text-gray-400 mt-0.5">J. Villar</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱41,000</div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 5: WON -->
                <div class="bg-slate-100/70 p-3 rounded-xl min-h-[500px]">
                    <div class="flex justify-between items-center mb-3 px-1">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">WON</span>
                        <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">2</span>
                    </div>

                    <div class="space-y-2.5">
                        <!-- Card 1 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Iloilo AgriTrade</h4>
                            <div class="text-xs text-gray-400 mt-0.5">M. Dizon</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱34,500</div>
                        </div>

                        <!-- Card 2 -->
                        <div class="kanban-card p-4 rounded-xl shadow-xs border border-gray-200/80">
                            <h4 class="text-sm font-bold text-slate-900">Subic Freeport Traders</h4>
                            <div class="text-xs text-gray-400 mt-0.5">C. Reyes</div>
                            <div class="text-xs font-bold text-slate-800 mt-3">₱88,000</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    
</main>




<script src="js/main.js"></script>
</body>
</html>
