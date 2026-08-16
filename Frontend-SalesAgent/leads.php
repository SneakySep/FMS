<?php
$pageTitle = 'My Leads - Priority Handling Logistics';
$activePage = 'leads';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal Priority Handling Logistics support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f4f7fa] dark:bg-[#0a1628]">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-2xl font-bold italic text-slate-900 tracking-wide">My Leads</h2>

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
        <div class="p-8 space-y-6 w-full">

            <!-- FILTER PILLS ROW -->
            <div class="flex items-center gap-3">
                <button class="bg-brand-blue text-white font-semibold text-sm px-4 py-1.5 rounded-full shadow-sm">
                    All (14)
                </button>
                <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm px-4 py-1.5 rounded-full transition">
                    New (4)
                </button>
                <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm px-4 py-1.5 rounded-full transition">
                    Qualifying (3)
                </button>
                <button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm px-4 py-1.5 rounded-full transition">
                    Negotiation (2)
                </button>
            </div>

            <!-- TABLE CARD CONTAINER -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold italic text-slate-900 mb-0.5">My Leads</h3>
                        <p class="text-xs text-gray-400 mb-3">Inquiries assigned to you</p>

                        <!-- Legend Items -->
                        <div class="flex flex-wrap items-center gap-4 text-[11px] text-gray-500">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                <span>Schedule - AI Book a Meeting</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400 inline-block"></span>
                                <span>Logged - No Meeting yet</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                                <span>Quote Sent - initial offer made</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>
                                <span>Escalated - sensitives need you</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-800 inline-block"></span>
                                <span>Referral - manual added</span>
                            </div>
                        </div>
                    </div>

                    <!-- Export Link -->
                    <a href="#" onclick="return exportLeadsCsv();" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue flex items-center gap-1">
                        Export CSV <i class="fa-solid fa-arrow-right w-3.5 h-3.5"></i>
                    </a>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-6">LEAD</th>
                                <th class="py-3 px-4">CATEGORY</th>
                                <th class="py-3 px-4">Volume/Mode</th>
                                <th class="py-3 px-4">Stage</th>
                                <th class="py-3 px-4">CONFIDENCE</th>
                                <th class="py-3 px-4 text-center">Action</th>
                                <th class="py-3 px-6 text-right">Last Activity</th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody" class="divide-y divide-gray-100 text-sm text-slate-800">
                            <!-- Row 1 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                                        <div>
                                            <div class="font-bold text-slate-900">Meridian Textiles Co.</div>
                                            <div class="text-[11px] text-gray-400">Contact: A. Santos</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-rose-100 text-rose-600 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Pricing
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">5 Tons (Sea)</td>
                                <td class="py-4 px-4">
                                    <span class="bg-brand-blue/15 text-brand-darkblue text-xs font-semibold px-2.5 py-0.5 rounded-md">New</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-rose-500 h-full rounded-full" style="width: 58%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 font-medium">0.58</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium px-3 py-1.5 rounded-md transition shadow-sm">
                                        Open Escalation
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-gray-500 font-medium">22m ago</td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                                        <div>
                                            <div class="font-bold text-slate-900">Robles Cargo Corp.</div>
                                            <div class="text-[11px] text-gray-400">Contact: R. Robles</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-rose-100 text-rose-600 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Contract
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">20ft FCL (Sea)</td>
                                <td class="py-4 px-4">
                                    <span class="bg-brand-blue/15 text-brand-darkblue text-xs font-semibold px-2.5 py-0.5 rounded-md">New</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-rose-500 h-full rounded-full" style="width: 41%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 font-medium">0.41</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium px-3 py-1.5 rounded-md transition shadow-sm">
                                        Open Escalation
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-gray-500 font-medium">1h ago</td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                        <div>
                                            <div class="font-bold text-slate-900">Iloilo AgriTrade</div>
                                            <div class="text-[11px] text-gray-400">Contact: M. Dizon</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> General
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">LCL (Sea)</td>
                                <td class="py-4 px-4">
                                    <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded-md">Qualifying</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" style="width: 81%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 font-medium">0.81</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button class="bg-brand-blue/40 hover:bg-brand-blue text-brand-navy text-xs font-semibold px-3 py-1.5 rounded-md transition">
                                        View Meeting Details
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-gray-500 font-medium">3h ago</td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                                        <div>
                                            <div class="font-bold text-slate-900">Bacolod Sugar Traders</div>
                                            <div class="text-[11px] text-gray-400">Contact: L. Ong</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-sky-100 text-sky-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Contract
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">40ft FCL (Sea)</td>
                                <td class="py-4 px-4">
                                    <span class="bg-sky-100 text-sky-700 text-xs font-semibold px-2.5 py-0.5 rounded-md">Negotiation</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-stone-200 h-2 rounded-full"></div>
                                        <span class="text-xs text-gray-400 font-medium">-</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button class="bg-white border border-gray-300 hover:bg-gray-50 text-slate-800 text-xs font-semibold px-4 py-1.5 rounded-md transition shadow-xs">
                                        Follow up
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-gray-500 font-medium">2 days ago</td>
                            </tr>

                            <!-- Row 5 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                                        <div>
                                            <div class="font-bold text-slate-900">Cagayan Fresh Produce</div>
                                            <div class="text-[11px] text-gray-400">Contact: J. Villar</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="bg-rose-100 text-rose-600 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Pricing
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">LCL (AIR)</td>
                                <td class="py-4 px-4">
                                    <span class="bg-brand-blue/15 text-brand-darkblue text-xs font-semibold px-2.5 py-0.5 rounded-md">New</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-rose-500 h-full rounded-full" style="width: 58%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 font-medium">0.58</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-medium px-3 py-1.5 rounded-md transition shadow-sm">
                                        Open Escalation
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-gray-500 font-medium">22m ago</td>
                            </tr>

                            <!-- Row 6 -->
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                                        <div>
                                            <div class="font-bold text-slate-900">Meridian Textiles Co.</div>
                                            <div class="text-[11px] text-gray-400">Contact: A. Santos</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="border border-gray-300 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                        Pricing
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-medium text-slate-700">1 Ton (AIR)</td>
                                <td class="py-4 px-4">
                                    <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded-md">Qualifying</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-stone-200 h-2 rounded-full"></div>
                                        <span class="text-xs text-gray-400 font-medium">-</span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-md transition shadow-sm leading-tight">
                                        Schedule<br>meeting
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-right text-xs text-gray-500 font-medium">22m ago</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bottom spacing area inside card -->
                <div class="p-4"></div>
            </div>

        </div>
    
</main>




<script src="js/main.js"></script>
</body>
</html>
