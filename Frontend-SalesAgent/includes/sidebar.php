<?php
$activePage = $activePage ?? 'dashboard';
?>
    <!-- SIDEBAR NAVIGATION -->
    <aside class="w-64 bg-[#0a1628] text-slate-300 min-h-screen flex flex-col justify-between p-4 border-r border-slate-800 shrink-0">
        <div class="space-y-6">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3 px-2 py-2">
                <?php include 'includes/logo.php'; ?>
                <div class="leading-none">
                    <h1 class="text-sm font-black tracking-wider text-white uppercase">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Logistics Inc. • Since 2005</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-5 text-xs font-semibold">
                <!-- Overview -->
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Overview</span>
                    <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'dashboard' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                        <i class="fa-solid fa-gauge-high w-4"></i> Dashboard
                    </a>
                </div>

                <!-- Pipeline -->
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Pipeline</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="leads.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'leads' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-user-tie w-4"></i> My Leads
                            </a>
                        </li>
                        <li>
                            <a href="pipelines.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'pipelines' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-square-kanban w-4"></i> Kanban Pipelines
                            </a>
                        </li>
                        <li>
                            <a href="ai-escalations.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'ai-escalations' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-robot w-4"></i> Ai Escalations
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Deals -->
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Deals</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="quotes.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'quotes' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-file-invoice-dollar w-4"></i> Quotes
                            </a>
                        </li>
                        <li>
                            <a href="contracts.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'contracts' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-file-contract w-4"></i> Contract
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Accounts -->
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Accounts</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="customers.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'customers' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-users w-4"></i> Customer
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Socials -->
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Socials</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="meetings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'meetings' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-calendar-check w-4"></i> Meetings
                            </a>
                        </li>
                        <li>
                            <a href="chat.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'chat' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-comments w-4"></i> Chat
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Support</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="settings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?php echo $activePage === 'settings' ? 'bg-brand-blue text-white shadow-md shadow-blue-500/20' : 'hover:bg-slate-800/60'; ?> transition-colors">
                                <i class="fa-solid fa-gear w-4"></i> Setting
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Sidebar Footer: Agent Profile -->
        <div class="space-y-3">
            <div class="bg-slate-800/50 rounded-xl p-3">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">SLA Compliance</span>
                    <span id="dashboardOpenBreaches" class="text-[10px] font-extrabold text-emerald-400">0</span>
                </div>
                <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width:94%"></div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-2">
                <div class="w-9 h-9 bg-slate-700 text-white rounded-full flex items-center justify-center text-xs font-black relative">
                    MS
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#0a1628]"></span>
                </div>
                <div class="leading-tight flex-1">
                    <p class="text-xs font-bold text-white">Maria Santos</p>
                    <p class="text-[10px] text-slate-400">Senior Sales Agent</p>
                </div>
                <button onclick="handleLogout()" title="Logout" class="text-slate-500 hover:text-red-400 transition-colors">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                </button>
            </div>
        </div>
    </aside>