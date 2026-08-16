<?php
$pageTitle = 'Priority Handling Logistics - Invoices & Billing';
$activePage = 'invoices';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need assistance with invoice statements, payments, or official receipts? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Invoices & Billing</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="invoiceSearchInput" onkeyup="searchInvoicesTable()" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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

        <!-- INVOICES & BILLING CONTENT BODY -->
        <div class="p-8 space-y-8 max-w-7xl w-full mx-auto">
            
            <!-- ROW 1: BILLING METRIC KPI CARDS (3 CARDS GRID) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                
                <!-- Card 1: Outstanding Balance -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-semibold text-slate-500 block">Outstanding Balance</span>
                    <strong class="text-4xl font-black text-slate-900 block">₱48,200</strong>
                    <div class="text-xs font-semibold text-red-500">
                        2 invoices due
                    </div>
                </div>

                <!-- Card 2: Paid (This Quarter) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-semibold text-slate-500 block">Paid (This Quarter)</span>
                    <strong class="text-4xl font-black text-slate-900 block">₱312,500</strong>
                    <div class="text-xs font-semibold text-emerald-600">
                        9 invoices settled
                    </div>
                </div>

                <!-- Card 3: Overdue -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                    <span class="text-xs font-semibold text-slate-500 block">Overdue</span>
                    <strong class="text-4xl font-black text-slate-900 block">₱0</strong>
                    <div class="text-xs font-medium text-slate-400">
                        no overdue items
                    </div>
                </div>

            </div>

            <!-- ROW 2: INVOICES TABLE CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Invoices</h3>
                        <p class="text-xs text-slate-400">Billing history for Charlie Hub.Inc</p>
                    </div>
                    <button onclick="downloadAllInvoices()" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-file-arrow-down"></i> Download all
                    </button>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="invoicesTable">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="pb-3">INVOICE</th>
                                <th class="pb-3">WAYBILL</th>
                                <th class="pb-3">AMOUNT</th>
                                <th class="pb-3">STATUS</th>
                                <th class="pb-3 text-right">DUE</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Row 1 -->
                            <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" onclick="alert('Opening Invoice INV-2026-0841 details...')">
                                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                    INV-2026-0841
                                </td>
                                <td class="py-4 font-mono text-slate-600">
                                    PH-WB-208841
                                </td>
                                <td class="py-4 font-extrabold text-slate-900">
                                    ₱24,000
                                </td>
                                <td class="py-4">
                                    <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Pending</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-600">
                                    Aug 5, 2026
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" onclick="alert('Opening Invoice INV-2026-0835 details...')">
                                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                    INV-2026-0835
                                </td>
                                <td class="py-4 font-mono text-slate-600">
                                    PH-WB-208835
                                </td>
                                <td class="py-4 font-extrabold text-slate-900">
                                    ₱24,200
                                </td>
                                <td class="py-4">
                                    <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Pending</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-600">
                                    Aug 6, 2026
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" onclick="alert('Opening Invoice INV-2026-0790 details...')">
                                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                    INV-2026-0790
                                </td>
                                <td class="py-4 font-mono text-slate-600">
                                    PH-WB-208790
                                </td>
                                <td class="py-4 font-extrabold text-slate-900">
                                    ₱18,750
                                </td>
                                <td class="py-4">
                                    <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Paid</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-600">
                                    Jul 20, 2026
                                </td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" onclick="alert('Opening Invoice INV-2026-0712 details...')">
                                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">
                                    INV-2026-0712
                                </td>
                                <td class="py-4 font-mono text-slate-600">
                                    PH-WB-208712
                                </td>
                                <td class="py-4 font-extrabold text-slate-900">
                                    ₱31,000
                                </td>
                                <td class="py-4">
                                    <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Paid</span>
                                </td>
                                <td class="py-4 text-right font-mono font-medium text-slate-600">
                                    Jul 15, 2026
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script src="js/store-bridge.js"></script>
</body>
</html>
