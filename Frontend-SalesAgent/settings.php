<?php
$pageTitle = 'SwiftFreight - Settings';
$activePage = 'settings';
$chatMessage = trim(<<<'MSG'
Mabuhay! Settings and preferences sync here. How can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Portal Settings</h2>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors"><i class="fa-solid fa-bell text-xs"></i><span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span></button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">Help Desk <i class="fa-solid fa-headset text-xs"></i></button>
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- SLA DEFAULT TARGETS -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div><h3 class="text-base font-extrabold text-slate-900">SLA Target Settings</h3><p class="text-xs text-slate-400">Defaults applied to the customer SLA monitoring view</p></div>
                <div class="space-y-5">
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">On-time Pickup</span><span id="pickupSliderVal" class="text-xs font-bold text-slate-500">97%</span></div><input type="range" id="pickupSlider" min="50" max="100" value="97" oninput="adjustSlaCompliance('pickup', this.value); document.getElementById('pickupSliderVal').innerText = this.value + '%'" class="w-full accent-brand-blue cursor-pointer"></div>
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">Transit Time</span><span id="transitSliderVal" class="text-xs font-bold text-slate-500">92%</span></div><input type="range" id="transitSlider" min="50" max="100" value="92" oninput="adjustSlaCompliance('transit', this.value); document.getElementById('transitSliderVal').innerText = this.value + '%'" class="w-full accent-brand-blue cursor-pointer"></div>
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">Customs Clearance</span><span id="customsSliderVal" class="text-xs font-bold text-slate-500">78%</span></div><input type="range" id="customsSlider" min="50" max="100" value="78" oninput="adjustSlaCompliance('customs', this.value); document.getElementById('customsSliderVal').innerText = this.value + '%'" class="w-full accent-brand-blue cursor-pointer"></div>
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700">Damage-Free Delivery</span><span id="damageSliderVal" class="text-xs font-bold text-slate-500">99%</span></div><input type="range" id="damageSlider" min="50" max="100" value="99" oninput="adjustSlaCompliance('damageFree', this.value); document.getElementById('damageSliderVal').innerText = this.value + '%'" class="w-full accent-brand-blue cursor-pointer"></div>
                </div>
            </div>

            <!-- NOTIFICATION PREFERENCES -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div><h3 class="text-base font-extrabold text-slate-900">Notification Preferences</h3><p class="text-xs text-slate-400">Choose which updates are pushed to the customer portal</p></div>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700">Shipment status changes</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700">New invoice issued</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700">Document published to vault</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <label class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700">SLA breach events</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                </div>
            </div>

            <!-- AGENT ACCOUNT -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div><h3 class="text-base font-extrabold text-slate-900">Agent Account</h3><p class="text-xs text-slate-400">Update your profile details</p></div>
                <form onsubmit="saveAgentDetails(event)" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Full Name</label><input type="text" value="Maria Santos" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none"></div>
                        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Work Email</label><input type="email" value="agent@swiftfreight.ph" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none"></div>
                    </div>
                    <button type="submit" id="saveAccountBtn" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Save changes</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>