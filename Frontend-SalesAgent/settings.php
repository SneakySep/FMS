<?php
$pageTitle = 'Priority Handling Logistics - Settings';
$activePage = 'settings';
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white dark:bg-[#0e1b33] border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 dark:text-white tracking-tight">Portal Settings</h2>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors"><i class="fa-solid fa-bell text-xs"></i><span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span></button>
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto pb-32">


            <!-- APPEARANCE -->
            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Appearance</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Toggle dark mode for the portal. Changes apply when you click <span class="font-semibold text-brand-blue dark:text-slate-300">Apply Changes</span> below.</p>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h4 class="font-extrabold text-slate-900 dark:text-white">Dark mode</h4>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Use a darker colour palette across the dashboard</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="appearanceDarkToggle" onchange="stageAppearanceDark(this.checked)" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                    </label>
                </div>
            </div>

            <!-- SLA DEFAULT TARGETS -->
            <!-- SLA DEFAULT TARGETS -->
            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 shadow-sm space-y-6">
                <div><h3 class="text-base font-extrabold text-slate-900 dark:text-white">SLA Target Settings</h3><p class="text-xs text-slate-400 dark:text-slate-500">Defaults applied to the customer SLA monitoring view</p></div>
                <div class="space-y-5">
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">On-time Pickup</span><span id="pickupSliderVal" class="text-xs font-bold text-slate-500 dark:text-slate-400">97%</span></div><input type="range" id="pickupSlider" min="50" max="100" value="97" oninput="document.getElementById('pickupSliderVal').innerText = this.value + '%'; stageSlaTargets();" class="w-full accent-brand-blue cursor-pointer"></div>
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Transit Time</span><span id="transitSliderVal" class="text-xs font-bold text-slate-500 dark:text-slate-400">92%</span></div><input type="range" id="transitSlider" min="50" max="100" value="92" oninput="document.getElementById('transitSliderVal').innerText = this.value + '%'; stageSlaTargets();" class="w-full accent-brand-blue cursor-pointer"></div>
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Customs Clearance</span><span id="customsSliderVal" class="text-xs font-bold text-slate-500 dark:text-slate-400">78%</span></div><input type="range" id="customsSlider" min="50" max="100" value="78" oninput="document.getElementById('customsSliderVal').innerText = this.value + '%'; stageSlaTargets();" class="w-full accent-brand-blue cursor-pointer"></div>
                    <div><div class="flex justify-between mb-1.5"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Damage-Free Delivery</span><span id="damageSliderVal" class="text-xs font-bold text-slate-500 dark:text-slate-400">99%</span></div><input type="range" id="damageSlider" min="50" max="100" value="99" oninput="document.getElementById('damageSliderVal').innerText = this.value + '%'; stageSlaTargets();" class="w-full accent-brand-blue cursor-pointer"></div>
                </div>
            </div>

            <!-- NOTIFICATION PREFERENCES -->
            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 shadow-sm space-y-4">
                <div><h3 class="text-base font-extrabold text-slate-900 dark:text-white">Notification Preferences</h3><p class="text-xs text-slate-400 dark:text-slate-500">Choose which updates are pushed to the customer portal</p></div>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/60 dark:border-slate-700 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Shipment status changes</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <label class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/60 dark:border-slate-700 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">New invoice issued</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <label class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/60 dark:border-slate-700 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Document published to vault</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <label class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/60 dark:border-slate-700 rounded-xl border border-slate-100 cursor-pointer"><span class="text-xs font-semibold text-slate-700 dark:text-slate-300">SLA breach events</span><input type="checkbox" checked class="accent-brand-blue w-4 h-4"></label>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 block">Notification sound</span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Sound played when notifications or chat replies arrive</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <select id="notifSoundSelect" onchange="stageNotificationSound(this.value)" class="bg-slate-50 dark:bg-slate-900 dark:border-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-brand-blue cursor-pointer">
                                <option value="notification-1.mp3">Notification 1</option>
                                <option value="notification-2.mp3">Notification 2</option>
                                <option value="notification-3.mp3">Notification 3</option>
                                <option value="notification-4.mp3">Notification 4</option>
                            </select>

        <!-- STICKY APPLY BAR -->
        <div id="applyBar" class="fixed bottom-0 left-64 right-0 z-30 hidden bg-white/95 dark:bg-[#0e1b33]/95 backdrop-blur border-t border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
            <span id="applyHint" class="text-xs font-semibold text-slate-500 dark:text-slate-400">You have unsaved changes.</span>
            <div class="flex items-center gap-3">
                <button type="button" onclick="discardSettings()" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">
                    Discard
                </button>
                <button type="button" onclick="applySettings()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i> Apply Changes
                </button>
            </div>
        </div>


                            <button type="button" onclick="previewNotificationSound()" title="Preview sound" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-volume-high text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AGENT ACCOUNT -->
            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 shadow-sm space-y-4">
                <div><h3 class="text-base font-extrabold text-slate-900 dark:text-white">Agent Account</h3><p class="text-xs text-slate-400 dark:text-slate-500">Update your profile details</p></div>
                <form onsubmit="stageAgentDetails(event)" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Full Name</label><input type="text" id="agentName" value="Maria Santos" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none"></div>
                        <div><label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Work Email</label><input type="email" id="agentEmail" value="agent@priority-ph.com" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none"></div>
                    </div>
                    <button type="submit" id="saveAccountBtn" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Save changes</button>
                </form>
            </div>
        </div>
    </main>


    <script src="js/main.js"></script>
</body>
</html>
