<?php
$page_title = "Invoices & Billing · Priority Handling Logistics";
$activePage = 'invoices';
require_once __DIR__ . '/../../includes/header.php';
include_once __DIR__ . '/../../includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER BAR -->
        <?php
        $pageTitle    = 'Invoices & Billing';
        $pageSubtitle = 'Charlie Hub Inc. · Acct #8B41 · Billing dashboard';
        $headerSearch = [
            'id'          => 'invoiceSearchInput',
            'onkeyup'     => 'applyInvoiceFilters()',
            'placeholder' => 'Search invoice, waybill, or amount...',
        ];
        include_once __DIR__ . '/../../components/customer_header.php';
        ?>

        <!-- INVOICES & BILLING CONTENT BODY -->
        <div class="p-6 lg:p-8 space-y-8 w-full">

            <!-- ROW 1: BILLING KPI CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

                <!-- Outstanding Balance -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-50 opacity-60"></div>
                    <div class="flex items-center justify-between relative">
                        <span class="text-xs font-semibold text-slate-500">Outstanding Balance</span>
                        <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar text-sm"></i></span>
                    </div>
                    <strong class="text-3xl lg:text-4xl font-black text-slate-900 block">₱48,200</strong>
                    <div class="text-xs font-semibold text-amber-600 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation"></i> 2 invoices due soon
                    </div>
                </div>

                <!-- Paid This Quarter -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-emerald-50 opacity-60"></div>
                    <div class="flex items-center justify-between relative">
                        <span class="text-xs font-semibold text-slate-500">Paid (This Quarter)</span>
                        <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-circle-check text-sm"></i></span>
                    </div>
                    <strong class="text-3xl lg:text-4xl font-black text-slate-900 block">₱312,500</strong>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-trend-up"></i> 9 invoices settled
                    </div>
                </div>

                <!-- Overdue -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-slate-100 opacity-60"></div>
                    <div class="flex items-center justify-between relative">
                        <span class="text-xs font-semibold text-slate-500">Overdue</span>
                        <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center"><i class="fa-solid fa-clock-rotate-left text-sm"></i></span>
                    </div>
                    <strong class="text-3xl lg:text-4xl font-black text-slate-900 block">₱0</strong>
                    <div class="text-xs font-medium text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved"></i> No overdue items
                    </div>
                </div>

                <!-- Available Credit (new) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-blue-50 opacity-60"></div>
                    <div class="flex items-center justify-between relative">
                        <span class="text-xs font-semibold text-slate-500">Available Credit</span>
                        <span class="w-9 h-9 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center"><i class="fa-solid fa-wallet text-sm"></i></span>
                    </div>
                    <strong class="text-3xl lg:text-4xl font-black text-slate-900 block">₱151,800</strong>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[10px] font-medium text-slate-400">
                            <span>Used ₱48,200</span><span>Limit ₱200,000</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-brand-blue h-full rounded-full" style="width:24%"></div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- ROW 2: MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT COLUMN -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Billing Trend Bar Chart -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Billing Trend</h3>
                                <p class="text-xs text-slate-400">Monthly invoiced amount &middot; last 6 months</p>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">+18% vs prev.</span>
                        </div>
                        <div class="pt-6 pb-2">
                            <div class="h-56 flex items-end justify-between gap-3 sm:gap-6 px-2 border-b border-slate-100">
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <div class="w-full max-w-[46px] bg-brand-blue/80 rounded-t-lg transition-all duration-500 h-[62%] group-hover:bg-brand-blue relative">
                                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">₱52,000</div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400">Jan</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <div class="w-full max-w-[46px] bg-brand-blue/80 rounded-t-lg transition-all duration-500 h-[73%] group-hover:bg-brand-blue relative">
                                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">₱61,000</div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400">Feb</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <div class="w-full max-w-[46px] bg-brand-blue/80 rounded-t-lg transition-all duration-500 h-[57%] group-hover:bg-brand-blue relative">
                                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">₱48,000</div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400">Mar</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <div class="w-full max-w-[46px] bg-brand-blue/80 rounded-t-lg transition-all duration-500 h-[87%] group-hover:bg-brand-blue relative">
                                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">₱73,000</div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400">Apr</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <div class="w-full max-w-[46px] bg-brand-blue/80 rounded-t-lg transition-all duration-500 h-[79%] group-hover:bg-brand-blue relative">
                                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">₱66,000</div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-400">May</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                                    <div class="w-full max-w-[46px] bg-brand-blue rounded-t-lg transition-all duration-500 h-[100%] group-hover:bg-brand-darkblue relative">
                                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">₱84,000</div>
                                    </div>
                                    <span class="text-xs font-semibold text-brand-blue">Jun</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INVOICES TABLE CARD -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="flex flex-wrap justify-between items-start gap-4">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Invoices</h3>
                                <p class="text-xs text-slate-400">Billing history for Charlie Hub Inc.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5">
                                    <button onclick="setInvoiceStatus('all', this)" class="inv-filter-tab crm-pill is-active">All</button>
                                    <button onclick="setInvoiceStatus('pending', this)" class="inv-filter-tab crm-pill">Pending</button>
                                    <button onclick="setInvoiceStatus('paid', this)" class="inv-filter-tab crm-pill">Paid</button>
                                    <button onclick="setInvoiceStatus('overdue', this)" class="inv-filter-tab crm-pill">Overdue</button>
                                </div>
                                <button onclick="exportInvoicesCSV()" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-file-csv"></i> Export
                                </button>
                                <button onclick="downloadAllInvoices()" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-file-arrow-down"></i> Download all
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs" id="invoicesTable">
                                <thead>
                                    <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                        <th class="pb-3">INVOICE</th>
                                        <th class="pb-3">WAYBILL</th>
                                        <th class="pb-3">AMOUNT</th>
                                        <th class="pb-3">STATUS</th>
                                        <th class="pb-3 text-right">DUE</th>
                                        <th class="pb-3 text-right">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" data-status="pending" onclick="openInvoice('INV-2026-0841')">
                                        <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">INV-2026-0841</td>
                                        <td class="py-4 font-mono text-slate-600">PH-WB-208841</td>
                                        <td class="py-4 font-extrabold text-slate-900">₱24,000</td>
                                        <td class="py-4"><span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Pending</span></td>
                                        <td class="py-4 text-right font-mono font-medium text-slate-600">Aug 5, 2026</td>
                                        <td class="py-4 text-right"><button onclick="event.stopPropagation(); payInvoice('INV-2026-0841')" class="text-[11px] font-semibold text-white bg-brand-blue hover:bg-brand-darkblue px-3 py-1.5 rounded-lg transition-colors">Pay</button></td>
                                    </tr>
                                    <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" data-status="pending" onclick="openInvoice('INV-2026-0835')">
                                        <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">INV-2026-0835</td>
                                        <td class="py-4 font-mono text-slate-600">PH-WB-208835</td>
                                        <td class="py-4 font-extrabold text-slate-900">₱24,200</td>
                                        <td class="py-4"><span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Pending</span></td>
                                        <td class="py-4 text-right font-mono font-medium text-slate-600">Aug 6, 2026</td>
                                        <td class="py-4 text-right"><button onclick="event.stopPropagation(); payInvoice('INV-2026-0835')" class="text-[11px] font-semibold text-white bg-brand-blue hover:bg-brand-darkblue px-3 py-1.5 rounded-lg transition-colors">Pay</button></td>
                                    </tr>
                                    <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" data-status="paid" onclick="openInvoice('INV-2026-0790')">
                                        <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">INV-2026-0790</td>
                                        <td class="py-4 font-mono text-slate-600">PH-WB-208790</td>
                                        <td class="py-4 font-extrabold text-slate-900">₱18,750</td>
                                        <td class="py-4"><span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Paid</span></td>
                                        <td class="py-4 text-right font-mono font-medium text-slate-600">Jul 20, 2026</td>
                                        <td class="py-4 text-right"><button onclick="event.stopPropagation(); downloadInvoice('INV-2026-0790')" class="text-[11px] font-semibold text-slate-500 hover:text-brand-blue px-3 py-1.5 rounded-lg transition-colors"><i class="fa-solid fa-download"></i></button></td>
                                    </tr>
                                    <tr class="invoice-row hover:bg-slate-50 transition-colors cursor-pointer" data-status="paid" onclick="openInvoice('INV-2026-0712')">
                                        <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">INV-2026-0712</td>
                                        <td class="py-4 font-mono text-slate-600">PH-WB-208712</td>
                                        <td class="py-4 font-extrabold text-slate-900">₱31,000</td>
                                        <td class="py-4"><span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Paid</span></td>
                                        <td class="py-4 text-right font-mono font-medium text-slate-600">Jul 15, 2026</td>
                                        <td class="py-4 text-right"><button onclick="event.stopPropagation(); downloadInvoice('INV-2026-0712')" class="text-[11px] font-semibold text-slate-500 hover:text-brand-blue px-3 py-1.5 rounded-lg transition-colors"><i class="fa-solid fa-download"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- RIGHT COLUMN -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- Payment Status Donut -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Payment Status</h3>
                            <p class="text-xs text-slate-400">Across 11 invoices</p>
                        </div>
                        <div class="flex items-center gap-5">
                            <div class="relative w-28 h-28 rounded-full shrink-0" style="background: conic-gradient(#10b981 0% 81.8%, #f59e0b 81.8% 100%);">
                                <div class="absolute inset-[14px] bg-white rounded-full flex flex-col items-center justify-center">
                                    <span class="text-lg font-black text-slate-900">11</span>
                                    <span class="text-[9px] font-semibold text-slate-400 uppercase">Total</span>
                                </div>
                            </div>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-slate-500">Paid</span><span class="font-bold text-slate-800 ml-auto">9</span></div>
                                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span class="text-slate-500">Pending</span><span class="font-bold text-slate-800 ml-auto">2</span></div>
                                <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span><span class="text-slate-500">Overdue</span><span class="font-bold text-slate-800 ml-auto">0</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding / Due Soon Callout -->
                    <div class="bg-gradient-to-br from-brand-blue to-brand-darkblue rounded-2xl p-6 shadow-lg shadow-blue-600/10 text-white relative overflow-hidden">
                        <i class="fa-solid fa-file-invoice absolute -right-4 -bottom-4 text-[90px] opacity-10"></i>
                        <div class="relative z-10">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-100">Due soon</span>
                            <h3 class="text-2xl font-black text-white mt-1">₱48,200</h3>
                            <p class="text-xs text-blue-100 mt-1">2 invoices due by Aug 6, 2026</p>
                            <button onclick="payInvoice('INV-2026-0841')" class="mt-4 w-full bg-white text-brand-blue hover:bg-blue-50 font-semibold text-xs py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                                <i class="fa-solid fa-credit-card"></i> Pay Now
                            </button>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-1">
                        <h3 class="text-base font-extrabold text-slate-900 mb-2">Quick Actions</h3>
                        <button onclick="downloadAllInvoices()" class="w-full flex items-center gap-3 text-left px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center"><i class="fa-solid fa-file-arrow-down text-xs"></i></span>
                            <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Download all invoices</span>
                        </button>
                        <button onclick="alert('Requesting official statement... (Demo)')" class="w-full flex items-center gap-3 text-left px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-receipt text-xs"></i></span>
                            <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Request statement</span>
                        </button>
                        <button onclick="alert('Opening payment methods... (Demo)')" class="w-full flex items-center gap-3 text-left px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-credit-card text-xs"></i></span>
                            <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Manage payment methods</span>
                        </button>
                        <button onclick="alert('Opening dispute form... (Demo)')" class="w-full flex items-center gap-3 text-left px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                            <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center"><i class="fa-solid fa-circle-exclamation text-xs"></i></span>
                            <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Dispute an invoice</span>
                        </button>
                    </div>

                    <!-- Recent Payment Activity -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-extrabold text-slate-900">Recent Activity</h3>
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Last 30 days</span>
                        </div>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-xs"></i></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800">Payment received &middot; INV-2026-0790</p>
                                    <p class="text-[10px] text-slate-400">Jul 20, 2026 &middot; ₱18,750</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-xs"></i></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800">Payment received &middot; INV-2026-0712</p>
                                    <p class="text-[10px] text-slate-400">Jul 15, 2026 &middot; ₱31,000</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-xs"></i></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800">Payment received &middot; INV-2026-0655</p>
                                    <p class="text-[10px] text-slate-400">Jun 28, 2026 &middot; ₱22,400</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-check text-xs"></i></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-800">Payment received &middot; INV-2026-0588</p>
                                    <p class="text-[10px] text-slate-400">Jun 10, 2026 &middot; ₱19,900</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </main>
    <?php include_once __DIR__ . '/../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>
    <script>
        (function () {
            'use strict';
            var currentStatus = 'all';

            function byId(id) { return document.getElementById(id); }
            function textOf(el) { return (el.textContent || el.innerText || '').toLowerCase(); }

            window.applyInvoiceFilters = function () {
                var input = byId('invoiceSearchInput');
                var q = input ? input.value.toLowerCase() : '';
                document.querySelectorAll('#invoicesTable tbody tr.invoice-row').forEach(function (row) {
                    var matchStatus = (currentStatus === 'all' || row.getAttribute('data-status') === currentStatus);
                    var matchText = !q || textOf(row).indexOf(q) > -1;
                    row.style.display = (matchStatus && matchText) ? '' : 'none';
                });
            };

            window.setInvoiceStatus = function (status, btn) {
                currentStatus = status || 'all';
                // Reuse the shared tab helper from customer_dashboard.js so the
                // active state stays a single `.is-active` class (styled by .crm-pill).
                if (window.setActiveTab) {
                    window.setActiveTab('.inv-filter-tab', btn);
                }
                window.applyInvoiceFilters();
            };

            window.downloadAllInvoices = function () { alert('Preparing ZIP of all invoices... (Demo)'); };
            window.downloadInvoice = function (inv) { alert('Downloading ' + inv + '.pdf... (Demo)'); };
            window.openInvoice = function (inv) { alert('Opening Invoice ' + inv + ' details... (Demo)'); };
            window.payInvoice = function (inv) { alert('Redirecting to secure payment for ' + inv + '... (Demo)'); };

            window.exportInvoicesCSV = function () {
                var table = byId('invoicesTable');
                if (!table) return;
                var rows = [['INVOICE', 'WAYBILL', 'AMOUNT', 'STATUS', 'DUE']];
                table.querySelectorAll('tbody tr.invoice-row').forEach(function (row) {
                    if (row.style.display === 'none') return;
                    var cells = row.querySelectorAll('td');
                    rows.push([
                        cells[0].textContent.trim(), cells[1].textContent.trim(),
                        cells[2].textContent.trim(), cells[3].textContent.trim(),
                        cells[4].textContent.trim()
                    ]);
                });
                var csv = rows.map(function (r) {
                    return r.map(function (v) {
                        var s = String(v).replace(/"/g, '""');
                        return /[",
]/.test(s) ? '"' + s + '"' : s;
                    }).join(',');
                }).join('
');
                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url; a.download = 'invoices_export_' + new Date().toISOString().slice(0, 10) + '.csv';
                document.body.appendChild(a); a.click(); document.body.removeChild(a);
                URL.revokeObjectURL(url);
            };
        })();
    </script>

<!-- FOOTER INCLUDE -->
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

