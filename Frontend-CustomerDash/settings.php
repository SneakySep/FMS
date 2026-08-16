<?php
$pageTitle = 'Priority Handling Logistics - Settings';
$activePage = 'settings';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need assistance updating your account details or warehouse preferences? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white dark:bg-[#0e1b33] border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 dark:text-white tracking-tight">Settings</h2>

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

        <!-- SETTINGS CONTENT BODY -->
        <div class="p-8 space-y-8 max-w-7xl w-full mx-auto pb-32">
            
            <!-- SECTION 0: APPEARANCE -->
            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
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

            <!-- SECTION 1: ACCOUNT DETAILS -->
            <div class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-700/60 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Account Details</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Robles Cargo Corp. · Acct #8841</p>
                </div>

                <form onsubmit="stageAccountDetails(event)" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Company name</label>
                            <input type="text" id="settingCompany" value="Robles Cargo Corp." required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Contact email</label>
                            <input type="email" id="settingEmail" value="ops@roblescargo.ph" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Contact number</label>
                            <input type="tel" id="settingPhone" value="+63 917 000 1234" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Default warehouse</label>
                            <select id="settingWarehouse" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                                <option value="Caloocan Hub" selected>Caloocan Hub</option>
                                <option value="Manila South Harbor Hub">Manila South Harbor Hub</option>
                                <option value="Cebu Logistics Center">Cebu Logistics Center</option>
                                <option value="Davao Regional Hub">Davao Regional Hub</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Billing address</label>
                        <input type="text" id="settingAddress" value="12 Rizal Ave, Caloocan City, Metro Manila" required class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white dark:focus:bg-slate-900 focus:border-brand-blue focus:outline-none transition-colors">
                    </div>

                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" onclick="location.reload()" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="saveAccountBtn" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- SECTION 2: NOTIFICATION PREFERENCES -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Notification Preferences</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Routed via the Notification Hub</p>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs">
                    
                    <!-- Preference 1 -->
                    <div class="py-4 flex items-center justify-between gap-4">
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white">Shipment status updates</h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Email + SMS when a waybill changes status</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                        </label>
                    </div>

                    <!-- Preference 2 -->
                    <div class="py-4 flex items-center justify-between gap-4">
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white">SLA breach alerts</h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Immediate notice when a commitment is at risk</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                        </label>
                    </div>

                    <!-- Preference 3 -->
                    <div class="py-4 flex items-center justify-between gap-4">
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white">Invoice reminders</h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">3 days before an invoice is due</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-blue dark:bg-slate-600"></div>
                        </label>
                    </div>

                    <!-- Notification sound -->
                    <div class="py-4 flex items-center justify-between gap-4">
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white">Notification sound</h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Sound played for portal notifications and chat replies</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <select id="notifSoundSelect" onchange="stageNotificationSound(this.value)" class="bg-slate-50 dark:bg-slate-900 dark:border-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-brand-blue cursor-pointer">
                                <option value="notification-1.mp3">Notification 1</option>
                                <option value="notification-2.mp3">Notification 2</option>
                                <option value="notification-3.mp3">Notification 3</option>
                                <option value="notification-4.mp3">Notification 4</option>
                            </select>
                            <button type="button" onclick="previewNotificationSound()" title="Preview sound" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-volume-high text-xs"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

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


    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script src="js/store-bridge.js"></script>
</body>
</html>
