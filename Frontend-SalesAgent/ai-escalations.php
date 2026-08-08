<?php
$pageTitle = 'AI Escalations - SwiftFreight Agent Portal';
$activePage = 'ai-escalations';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal SwiftFreight support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-2xl font-bold italic text-slate-900 tracking-wide">AI Escalations</h2>

            <div class="flex items-center gap-4">
                <!-- Search Input -->
                <div class="relative w-80">
                    <i class="fa-solid fa-magnifying-glass w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        placeholder="Search leads, customer, quotes..." 
                        class="w-full pl-9 pr-4 py-1.5 text-sm bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-700"
                    >
                </div>

                <!-- Notification Bell -->
                <button class="p-2 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 relative">
                    <i class="fa-solid fa-bell w-4 h-4"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- New Quote Button -->
                <a href="quotes.php" class="bg-purple-600 hover:bg-purple-700 text-white font-medium text-sm px-4 py-2 rounded-md transition inline-flex items-center gap-1.5 shadow-sm">
<i class="fa-solid fa-plus w-4"></i>
<span>New Quote</span>
</a>
            </div>
        </header>

        <!-- CONTENT BODY CONTAINER -->
        <div class="p-8 max-w-[1200px]">

            <!-- MAIN ESCALATION CARD -->
            <div class="bg-white rounded-xl border border-gray-300/80 shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-slate-900 mb-0.5">AI Escalation Queue</h3>
                    <p class="text-xs text-gray-400">Inquiries routed to you — confidence &lt; 0.75 or sensitivity flagged</p>
                </div>

                <!-- ESCALATION LIST ITEMS -->
                <div class="divide-y divide-gray-200">

                    <!-- ITEM 1 -->
                    <div class="p-6 flex items-start gap-5 hover:bg-slate-50/50 transition">
                        <!-- Warning Icon Box -->
                        <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-triangle-exclamation w-5 h-5"></i>
                        </div>

                        <!-- Details & Actions -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-base font-bold text-slate-900">Meridian Textiles Co.</h4>
                                <span class="bg-rose-100 text-rose-600 text-xs font-semibold px-2 py-0.5 rounded">0.58</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                                Asking about reefer container availability for a rush Cebu shipment. AI reply was inconclusive on lead-time.
                            </p>
                            <span class="text-xs text-gray-400 mt-1 block">TCK-3381 · escalated 22m ago</span>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3 mt-4">
                                <button class="bg-purple-700 hover:bg-purple-800 text-white text-xs font-medium px-4 py-2 rounded-md transition shadow-xs">
                                    Accept & respond
                                </button>
                                <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium px-3.5 py-2 rounded-md transition shadow-xs">
                                    Reassign
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 2 -->
                    <div class="p-6 flex items-start gap-5 hover:bg-slate-50/50 transition">
                        <!-- Warning Icon Box -->
                        <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-triangle-exclamation w-5 h-5"></i>
                        </div>

                        <!-- Details & Actions -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-base font-bold text-slate-900">Robles Cargo Corp</h4>
                                <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded">0.69</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                                Wants a custom SLA clause for customs clearance on future bookings — outside standard templates.
                            </p>
                            <span class="text-xs text-gray-400 mt-1 block">TCK-3378 · escalated 1h ago</span>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3 mt-4">
                                <button class="bg-purple-700 hover:bg-purple-800 text-white text-xs font-medium px-4 py-2 rounded-md transition shadow-xs">
                                    Accept & respond
                                </button>
                                <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium px-3.5 py-2 rounded-md transition shadow-xs">
                                    Reassign
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 3 -->
                    <div class="p-6 flex items-start gap-5 hover:bg-slate-50/50 transition">
                        <!-- Warning Icon Box -->
                        <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-triangle-exclamation w-5 h-5"></i>
                        </div>

                        <!-- Details & Actions -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-base font-bold text-slate-900">Iloilo AgriTrade</h4>
                                <span class="bg-rose-100 text-rose-600 text-xs font-semibold px-2 py-0.5 rounded">0.44</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                                Message flagged sensitive — mentions a damaged shipment and possible claim. Requires human handling.
                            </p>
                            <span class="text-xs text-gray-400 mt-1 block">TCK-3372 · escalated 3h ago</span>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3 mt-4">
                                <button class="bg-purple-700 hover:bg-purple-800 text-white text-xs font-medium px-4 py-2 rounded-md transition shadow-xs">
                                    Accept & respond
                                </button>
                                <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium px-3.5 py-2 rounded-md transition shadow-xs">
                                    Reassign
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom empty padding inside card -->
                <div class="p-8"></div>
            </div>

        </div>
    
</main>

<?php include 'includes/chat-widget.php'; ?>



<script src="js/main.js"></script>
</body>
</html>
