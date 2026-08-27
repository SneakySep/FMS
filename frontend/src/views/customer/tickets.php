<?php
$page_title = "Support Tickets · Priority Handling Logistics";
$activePage = 'tickets';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Support Tickets</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="ticketSearchInput" onkeyup="searchTicketsList()" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
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

        <!-- SUPPORT TICKETS CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <!-- SUPPORT TICKETS CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Support Tickets</h3>
                        <p class="text-xs text-slate-400">Requests routed through the Notification Hub</p>
                    </div>
                    <button onclick="createNewTicket()" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1">
                        + New ticket
                    </button>
                </div>

                <!-- TICKETS LIST -->
                <div class="space-y-3" id="ticketsContainer">
                    
                    <!-- Ticket 1 -->
                    <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ticket TCK-1042 details...')">
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono text-slate-400 font-bold block">TCK-1042</span>
                            <h4 class="text-xs font-extrabold text-slate-900 hover:text-brand-blue transition-colors">Delay on PH-WB-208712 — need updated ETA</h4>
                        </div>
                        <span class="bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded-full text-[10px]">● In Progress</span>
                    </div>

                    <!-- Ticket 2 -->
                    <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ticket TCK-1039 details...')">
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono text-slate-400 font-bold block">TCK-1039</span>
                            <h4 class="text-xs font-extrabold text-slate-900 hover:text-brand-blue transition-colors">Request for duplicate Bill of Lading</h4>
                        </div>
                        <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Awaiting Reply</span>
                    </div>

                    <!-- Ticket 3 -->
                    <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ticket TCK-1021 details...')">
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono text-slate-400 font-bold block">TCK-1021</span>
                            <h4 class="text-xs font-extrabold text-slate-900 hover:text-brand-blue transition-colors">Billing discrepancy on INV-2026-0790</h4>
                        </div>
                        <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Resolved</span>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
