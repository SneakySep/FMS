<?php
$activePage = $activePage ?? 'dashboard';
$nav = [
    'dashboard' => ['fa-border-all', 'Dashboard'],
    'shipments' => ['fa-box', 'Shipments'],
    'tracking' => ['fa-location-dot', 'Live Tracking'],
    'sla-monitoring' => ['fa-clock-rotate-left', 'SLA Monitoring'],
    'documents' => ['fa-file-lines', 'Documents'],
    'invoices' => ['fa-file-invoice-dollar', 'Invoices & Billing'],
    'analytics' => ['fa-chart-column', 'BI Analytics'],
    'tickets' => ['fa-comments', 'Support Tickets'],
    'settings' => ['fa-gear', 'Settings'],
];
?>
    <!-- SIDEBAR NAVIGATION -->
    <aside class="w-64 bg-[#0a1628] text-slate-300 min-h-screen flex flex-col justify-between p-4 border-r border-slate-800 shrink-0">
        <div class="space-y-6">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3 px-2 py-2">
                <?php include 'includes/logo.php'; ?>
                <div class="leading-none">
                    <h1 class="text-base font-black text-white tracking-wider uppercase">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest font-bold">Logistics Inc. • Since 2005</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-6 text-xs font-medium">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">OVERVIEW</span>
                    <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'dashboard' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                        <i class="fa-solid fa-border-all text-sm <?php echo $activePage === 'dashboard' ? 'text-brand-blue' : ''; ?>"></i> Dashboard
                    </a>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Freight</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="shipments.php" class="flex items-center justify-between px-3 py-2 <?php echo $activePage === 'shipments' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-box text-sm <?php echo $activePage === 'shipments' ? 'text-brand-blue' : ''; ?>"></i> Shipments</span>
                                <span class="bg-amber-500/20 text-amber-400 font-bold px-1.5 py-0.5 rounded text-[10px]">12</span>
                            </a>
                        </li>
                        <li>
                            <a href="tracking.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'tracking' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <i class="fa-solid fa-location-dot text-sm <?php echo $activePage === 'tracking' ? 'text-brand-blue' : ''; ?>"></i> Live Tracking
                            </a>
                        </li>
                        <li>
                            <a href="sla-monitoring.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'sla-monitoring' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <i class="fa-solid fa-clock-rotate-left text-sm <?php echo $activePage === 'sla-monitoring' ? 'text-brand-blue' : ''; ?>"></i> SLA Monitoring
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Records</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="documents.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'documents' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <i class="fa-solid fa-file-lines text-sm <?php echo $activePage === 'documents' ? 'text-brand-blue' : ''; ?>"></i> Documents
                            </a>
                        </li>
                        <li>
                            <a href="invoices.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'invoices' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <i class="fa-solid fa-file-invoice-dollar text-sm <?php echo $activePage === 'invoices' ? 'text-brand-blue' : ''; ?>"></i> Invoices & Billing
                            </a>
                        </li>
                        <li>
                            <a href="analytics.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'analytics' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <i class="fa-solid fa-chart-column text-sm <?php echo $activePage === 'analytics' ? 'text-brand-blue' : ''; ?>"></i> BI Analytics
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 block mb-2">Support</span>
                    <ul class="space-y-1">
                        <li>
                            <a href="tickets.php" class="flex items-center justify-between px-3 py-2 <?php echo $activePage === 'tickets' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-comments text-sm <?php echo $activePage === 'tickets' ? 'text-brand-blue' : ''; ?>"></i> Support Tickets</span>
                                <span class="bg-amber-500/20 text-amber-400 font-bold px-1.5 py-0.5 rounded text-[10px]">2</span>
                            </a>
                        </li>
                        <li>
                            <a href="settings.php" class="flex items-center gap-3 px-3 py-2 <?php echo $activePage === 'settings' ? 'bg-brand-blue/20 text-white rounded-xl font-semibold border border-brand-blue/30' : 'text-slate-400 hover:text-white hover:bg-white/5 rounded-lg'; ?> transition-colors">
                                <i class="fa-solid fa-gear text-sm <?php echo $activePage === 'settings' ? 'text-brand-blue' : ''; ?>"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="space-y-4 pt-4 border-t border-slate-800/80">
            <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 text-xs">
                <div class="flex justify-between items-center text-[10px] font-semibold text-slate-400 mb-1.5">
                    <span>SLA compliance</span>
                    <span class="text-emerald-400 font-bold">94%</span>
                </div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full w-[94%] rounded-full"></div>
                </div>
            </div>

            <div class="flex items-center justify-between p-2 bg-slate-900/80 border border-slate-800 rounded-xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-brand-blue/20 text-brand-blue font-bold rounded-lg flex items-center justify-center text-xs border border-brand-blue/30">CH</div>
                    <div class="leading-tight">
                        <h4 class="text-xs font-bold text-white">Charlie Hub.Inc</h4>
                        <span class="text-[10px] text-slate-400">J. Sison • Acct #8841</span>
                    </div>
                </div>
                <button onclick="handleLogout()" title="Logout" class="text-slate-400 hover:text-red-400 p-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                </button>
            </div>
        </div>
    </aside>