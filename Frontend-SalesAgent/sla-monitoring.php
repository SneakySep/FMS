<?php
$pageTitle = 'SwiftFreight - SLA Monitoring';
$activePage = 'sla-monitoring';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help with SLA compliance? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">SLA Monitoring</h2>
            <div class="flex-1 max-w-md mx-8"><div class="relative"><i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i><input type="text" placeholder="Search waybill or commitment..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all"></div></div>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors"><i class="fa-solid fa-bell text-xs"></i><span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span></button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">Help Desk <i class="fa-solid fa-headset text-xs"></i></button>
            </div>
        </header>

        <!-- SLA CONTENT -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- KPI CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">Overall SLA Compliance</span>
                    <strong id="overallCompliance" class="text-4xl font-black text-slate-900 block">94%</strong>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width:94%"></div></div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">Open Breaches</span>
                    <strong id="openBreaches" class="text-4xl font-black text-red-500 block">1</strong>
                    <div id="openBreachesDetail" class="text-xs font-semibold text-slate-500">Transit time, SF-WB-208712</div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">Breach Escalation</span>
                    <strong class="text-4xl font-black text-slate-900 block">24h</strong>
                    <div class="text-xs font-medium text-slate-400">automatic after unresolved SLA</div>
                </div>
            </div>

            <!-- COMPLIANCE BARS + CONTROL -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6 reveal">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Compliance by Metric</h3>
                        <p class="text-xs text-slate-400">Drag the sliders to adjust target compliance and push updates to the customer portal</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">On-time Pickup</span><span id="pickupValue" class="text-xs font-bold text-slate-500">97%</span></div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div id="pickupBar" class="h-full bg-emerald-500 rounded-full" style="width:97%"></div></div>
                            <input type="range" min="50" max="100" value="97" oninput="adjustSlaCompliance('pickup', this.value)" class="w-40 accent-brand-blue cursor-pointer">
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">Transit Time</span><span id="transitValue" class="text-xs font-bold text-slate-500">92%</span></div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div id="transitBar" class="h-full bg-blue-500 rounded-full" style="width:92%"></div></div>
                            <input type="range" min="50" max="100" value="92" oninput="adjustSlaCompliance('transit', this.value)" class="w-40 accent-brand-blue cursor-pointer">
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">Customs Clearance</span><span id="customsValue" class="text-xs font-bold text-slate-500">78%</span></div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div id="customsBar" class="h-full bg-amber-500 rounded-full" style="width:78%"></div></div>
                            <input type="range" min="50" max="100" value="78" oninput="adjustSlaCompliance('customs', this.value)" class="w-40 accent-brand-blue cursor-pointer">
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">Damage-Free Delivery</span><span id="damageValue" class="text-xs font-bold text-slate-500">99%</span></div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden"><div id="damageBar" class="h-full bg-emerald-500 rounded-full" style="width:99%"></div></div>
                            <input type="range" min="50" max="100" value="99" oninput="adjustSlaCompliance('damageFree', this.value)" class="w-40 accent-brand-blue cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- BREACH LOG -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6 reveal">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Breach Log</h3>
                    <p class="text-xs text-slate-400">Resolve open breaches to clear SLA commitments</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="pb-3">WAYBILL</th>
                                <th class="pb-3">COMMITMENT</th>
                                <th class="pb-3">FLAGGED</th>
                                <th class="pb-3 text-right">STATUS</th>
                                <th class="pb-3 text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="breachLogBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>