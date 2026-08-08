<?php
$pageTitle = 'Dashboard - SwiftFreight Agent Portal';
$activePage = 'dashboard';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal SwiftFreight support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-2xl font-bold italic text-slate-900 tracking-wide">Dashboard</h2>

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

        <!-- DASHBOARD BODY CONTAINER -->
        <div class="p-8 space-y-6 max-w-[1400px]">

            <!-- TOP 4 STAT CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Active Leads -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between relative">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-semibold text-gray-500">Active Leads</span>
                        <div class="w-7 h-7 rounded-md bg-purple-100 text-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-user w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-4xl font-bold text-slate-900">14</div>
                        <div class="flex items-center gap-1 text-xs text-emerald-600 font-medium mt-2">
                            <i class="fa-solid fa-arrow-trend-up w-2.5 h-2.5 fill-emerald-600"></i>
                            <span>3 new this week</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: AI Escalations Pending -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between relative">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-semibold text-gray-500">AI Escalations Pending</span>
                        <div class="w-7 h-7 rounded-md bg-rose-100 text-rose-500 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-4xl font-bold text-slate-900">3</div>
                        <div class="text-xs text-rose-600 font-semibold mt-2">
                            needs response &lt; 4h
                        </div>
                    </div>
                </div>

                <!-- Card 3: Meetings Today -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between relative">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-semibold text-gray-500">Meetings Today</span>
                        <div class="w-7 h-7 rounded-md bg-sky-100 text-sky-500 flex items-center justify-center">
                            <i class="fa-solid fa-calendar-days w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-4xl font-bold text-slate-900">2</div>
                        <div class="text-xs text-gray-500 font-medium mt-2">
                            Next: 2:30 PM
                        </div>
                    </div>
                </div>

                <!-- Card 4: Contracts Closed (MTD) -->
                <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between relative">
                    <div class="flex justify-between items-start">
                        <span class="text-xs font-semibold text-gray-500">Contracts Closed (MTD)</span>
                        <div class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-check w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-4xl font-bold text-slate-900">6</div>
                        <div class="flex items-center gap-1 text-xs text-emerald-600 font-medium mt-2">
                            <i class="fa-solid fa-arrow-trend-up w-2.5 h-2.5 fill-emerald-600"></i>
                            <span>₱482,000 value</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOWER GRID SECTION (2 Columns) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT COLUMN (Pipeline Snapshot) - 7 cols -->
                <div class="lg:col-span-7 bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="text-2xl font-bold text-slate-900">Pipeline Snapshot</h3>
                            <a href="pipelines.php" class="text-xs font-semibold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                                Open board <i class="fa-solid fa-arrow-right w-3.5 h-3.5"></i>
                            </a>
                        </div>
                        <p class="text-xs text-gray-400 mb-6">Leads by stage, this month</p>

                        <!-- STAGES BARS -->
                        <div class="space-y-5">
                            <!-- Stage 1 -->
                            <div>
                                <div class="flex justify-between text-sm font-semibold text-slate-800 mb-1.5">
                                    <span>New Inquiry</span>
                                    <span class="text-gray-400 text-xs font-normal">4</span>
                                </div>
                                <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div class="bg-purple-400 h-full rounded-full" style="width: 40%"></div>
                                </div>
                            </div>

                            <!-- Stage 2 -->
                            <div>
                                <div class="flex justify-between text-sm font-semibold text-slate-800 mb-1.5">
                                    <span>Qualifying</span>
                                    <span class="text-gray-400 text-xs font-normal">3</span>
                                </div>
                                <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div class="bg-amber-400 h-full rounded-full" style="width: 25%"></div>
                                </div>
                            </div>

                            <!-- Stage 3 -->
                            <div>
                                <div class="flex justify-between text-sm font-semibold text-slate-800 mb-1.5">
                                    <span>Quote Sent</span>
                                    <span class="text-gray-400 text-xs font-normal">5</span>
                                </div>
                                <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div class="bg-purple-400 h-full rounded-full" style="width: 45%"></div>
                                </div>
                            </div>

                            <!-- Stage 4 -->
                            <div>
                                <div class="flex justify-between text-sm font-semibold text-slate-800 mb-1.5">
                                    <span>Negotiation</span>
                                    <span class="text-gray-400 text-xs font-normal">2</span>
                                </div>
                                <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div class="bg-amber-400 h-full rounded-full" style="width: 18%"></div>
                                </div>
                            </div>

                            <!-- Stage 5 -->
                            <div>
                                <div class="flex justify-between text-sm font-semibold text-slate-800 mb-1.5">
                                    <span>Won (MTD)</span>
                                    <span class="text-gray-400 text-xs font-normal">6</span>
                                </div>
                                <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full rounded-full" style="width: 60%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN (AI Escalation Queue & My Contracts) - 5 cols -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- AI Escalation Queue -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
                        <div class="flex justify-between items-start mb-0.5">
                            <h3 class="text-base font-bold text-slate-900">AI Escalation Queue</h3>
                            <a href="ai-escalations.php" class="text-xs font-semibold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                                View All <i class="fa-solid fa-arrow-right w-3.5 h-3.5"></i>
                            </a>
                        </div>
                        <p class="text-[11px] text-gray-400 mb-4">Below-threshold inquiries needing you</p>

                        <div class="space-y-3.5">
                            <!-- Queue Item 1 -->
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-b-0 last:pb-0">
                                <div class="p-2 bg-rose-100 text-rose-500 rounded-md mt-0.5">
                                    <i class="fa-solid fa-triangle-exclamation w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-xs font-bold text-slate-900">Meridian Textiles Co.</h4>
                                        <span class="bg-rose-100 text-rose-600 text-[11px] font-semibold px-1.5 py-0.5 rounded">0.58</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-tight mt-0.5">
                                        Asking about reefer container availability for a rush Cebu shipment.
                                    </p>
                                    <span class="text-[10px] text-gray-400 mt-1 block">TCK-3381 · escalated 22m ago</span>
                                </div>
                            </div>

                            <!-- Queue Item 2 -->
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-b-0 last:pb-0">
                                <div class="p-2 bg-rose-100 text-rose-500 rounded-md mt-0.5">
                                    <i class="fa-solid fa-triangle-exclamation w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-xs font-bold text-slate-900">Robles Cargo Corp.</h4>
                                        <span class="bg-amber-100 text-amber-700 text-[11px] font-semibold px-1.5 py-0.5 rounded">0.69</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-tight mt-0.5">
                                        Wants a custom SLA clause for customs clearance on future bookings.
                                    </p>
                                    <span class="text-[10px] text-gray-400 mt-1 block">TCK-3378 · escalated 1h ago</span>
                                </div>
                            </div>

                            <!-- Queue Item 3 -->
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-rose-100 text-rose-500 rounded-md mt-0.5">
                                    <i class="fa-solid fa-triangle-exclamation w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-xs font-bold text-slate-900">Iloilo AgriTrade</h4>
                                        <span class="bg-rose-100 text-rose-600 text-[11px] font-semibold px-1.5 py-0.5 rounded">0.44</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 leading-tight mt-0.5">
                                        Message flagged sensitive — mentions a damaged shipment and possible claim.
                                    </p>
                                    <span class="text-[10px] text-gray-400 mt-1 block">TCK-3372 · escalated 3h ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Contracts Card -->
                    <div class="bg-white p-5 rounded-xl border border-gray-200/80 shadow-sm">
                        <h3 class="text-base font-bold text-slate-900 mb-4">My Contracts</h3>

                        <div class="space-y-3.5">
                            <!-- Contract 1 -->
                            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">Tan Bros. Distribution</h4>
                                    <span class="text-[11px] text-gray-400">12-month · Pending Approval</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-900">₱182,000</div>
                                    <span class="bg-amber-100 text-amber-700 text-[10px] font-semibold px-1.5 py-0.5 rounded uppercase tracking-wider">PENDING</span>
                                </div>
                            </div>

                            <!-- Contract 2 -->
                            <div class="flex justify-between items-center pb-2.5 border-b border-gray-100">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">Northbay Logistics</h4>
                                    <span class="text-[11px] text-gray-400">6-month · Draft</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-900">₱96,500</div>
                                    <span class="bg-sky-100 text-sky-600 text-[10px] font-semibold px-1.5 py-0.5 rounded uppercase tracking-wider">DRAFT</span>
                                </div>
                            </div>

                            <!-- Contract 3 -->
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">Ocean Peak Traders</h4>
                                    <span class="text-[11px] text-gray-400">12-month · Active</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-slate-900">₱245,000</div>
                                    <span class="bg-emerald-100 text-emerald-600 text-[10px] font-semibold px-1.5 py-0.5 rounded uppercase tracking-wider">ACTIVE</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    
</main>

<?php include 'includes/chat-widget.php'; ?>



<script src="js/main.js"></script>
</body>
</html>
