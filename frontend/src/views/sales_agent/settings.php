<?php
$page_title = "Settings · Sales Agent · Priority Handling Logistics";
$activePage = 'settings';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER BAR -->
        <header class="bg-white dark:bg-[#0e1b33] border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black italic text-slate-900 dark:text-white tracking-tight">Sales Agent Settings</h2>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium mt-0.5">Manage your profile, territory, and notification preferences</p>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100 dark:border-blue-500/20">
                    Help Desk <i class="fa-solid fa-headset text-xs"></i>
                </button>
                <button onclick="window.location.href='my_leads.php'" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + New Lead
                </button>
            </div>
        </header>

        <!-- SETTINGS DASHBOARD BODY -->
        <div class="p-6 lg:p-8 2xl:px-10 w-full pb-36">

            <!-- INTRO BANNER -->
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0b1528] via-[#0e1b33] to-[#103a8a] p-6 mb-8 shadow-lg">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-blue-200 mb-2">
                        <i class="fa-solid fa-user-tie text-brand-blue"></i> Sales Agent Profile
                    </div>
                    <h3 class="text-xl font-black italic text-white tracking-tight">Sales Agent Dashboard · Profile Settings</h3>
                    <p class="text-sm text-blue-100/80 mt-1">Configure your professional settings to optimize lead management and performance tracking.</p>
                </div>
            </section>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT COLUMN: Profile & Territory -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Basic Info -->
                    <section class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Personal Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Full Name</label>
                                <input type="text" value="Sales Agent Name" class="w-full mt-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Email Address</label>
                                <input type="email" value="agent@logistics.com" class="w-full mt-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white">
                            </div>
                        </div>
                    </section>

                    <!-- Territory -->
                    <section class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Assigned Territories</h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">North America</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Southeast Asia</span>
                        </div>
                    </section>
                </div>

                <!-- RIGHT COLUMN: Preferences & Danger Zone -->
                <div class="space-y-8">
                    <!-- Notification Prefs -->
                    <section class="bg-white dark:bg-[#112240] border border-slate-200 dark:border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Notifications</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-slate-300">New Lead Alerts</span>
                                <input type="checkbox" checked class="accent-brand-blue">
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600 dark:text-slate-300">Quote Approvals</span>
                                <input type="checkbox" checked class="accent-brand-blue">
                            </div>
                        </div>
                    </section>

                    <!-- Danger Zone -->
                    <section class="bg-white dark:bg-[#112240] border border-rose-200 dark:border-rose-500/30 rounded-2xl p-6 sm:p-8 shadow-sm">
                        <h3 class="text-base font-extrabold text-rose-600 dark:text-rose-400">Danger zone</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">These actions are irreversible.</p>
                        <div class="mt-4">
                            <button onclick="if(confirm('Deactivate account?')) alert('Action requested.')" class="w-full border border-rose-200 dark:border-rose-500/40 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 font-semibold text-xs px-5 py-2.5 rounded-xl transition-colors">Deactivate Profile</button>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <!-- STICKY APPLY BAR -->
        <div id="applyBar" class="fixed bottom-0 left-64 right-0 z-30 hidden bg-white/95 dark:bg-[#0e1b33]/95 backdrop-blur border-t border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">You have unsaved changes.</span>
            <div class="flex items-center gap-3">
                <button type="button" onclick="location.reload()" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Discard</button>
                <button type="button" onclick="alert('Settings saved!')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i> Apply Changes
                </button>
            </div>
        </div>
    </main>

    <?php include_once '../../components/chat_widget.php'; ?>
    <?php include_once '../../includes/footer.php'; ?>
