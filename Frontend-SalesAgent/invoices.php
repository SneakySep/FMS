<?php
$pageTitle = 'SwiftFreight - Invoice Control';
$activePage = 'invoices';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help with invoices? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Invoice Control</h2>
            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="invoiceSearchInput" onkeyup="searchInvoicesTable()" placeholder="Search invoice, waybill..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="downloadAllInvoices()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-slate-200">
                    <i class="fa-solid fa-file-export text-xs"></i> Download All
                </button>
                <button onclick="openNewInvoiceModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">+ New Invoice</button>
            </div>
        </header>

        <!-- INVOICES CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- INVOICES TABLE CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">All Invoices</h3>
                    <p class="text-xs text-slate-400">Issue and manage invoices visible on the customer portal</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                <th class="pb-3">INVOICE</th>
                                <th class="pb-3">WAYBILL</th>
                                <th class="pb-3">AMOUNT</th>
                                <th class="pb-3">STATUS</th>
                                <th class="pb-3 text-right">DUE DATE</th>
                                <th class="pb-3 text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="invoicesTableBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- NEW INVOICE MODAL -->
    <div id="newInvoiceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 space-y-6">
            <div class="flex justify-between items-start"><div><h3 class="text-base font-extrabold text-slate-900">Issue New Invoice</h3><p class="text-xs text-slate-400">Published instantly to the customer portal</p></div><button onclick="closeModal('newInvoiceModal')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fa-solid fa-xmark"></i></button></div>
            <form onsubmit="createNewInvoice(event)" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Invoice Number</label><input type="text" id="newInvoiceId" required placeholder="INV-2026-0001" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors font-mono"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Waybill</label>
                        <select id="newInvoiceWaybill" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer"></select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Amount (₱)</label><input type="number" id="newInvoiceAmount" required min="0" step="0.01" placeholder="24000" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Due Date</label><input type="text" id="newInvoiceDue" required placeholder="Aug 5, 2026" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors"></div>
                </div>
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="closeModal('newInvoiceModal')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Issue Invoice</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>