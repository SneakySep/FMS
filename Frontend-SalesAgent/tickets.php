<?php
$pageTitle = 'SwiftFreight - Support Tickets Control';
$activePage = 'tickets';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help managing tickets? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0"><header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center"><h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Ticket Control</h2><div class="flex-1 max-w-md mx-8"><div class="relative"><i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i><input type="text" id="ticketSearchInput" onkeyup="searchTicketsList()" placeholder="Search tickets..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs placeholder-slate-400 focus:outline-none focus:border-[#0066ff]"></div></div><div class="flex items-center gap-3"><button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-[#0066ff] font-semibold text-xs px-4 py-2 rounded-xl flex items-center gap-2 border border-blue-100">Help Desk <i class="fa-solid fa-headset text-xs"></i></button><button onclick="createNewTicket()" class="bg-[#0066ff] hover:bg-[#0052cc] text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-md shadow-blue-500/20">+ New Ticket</button></div></header>
<div class="p-8 space-y-6 max-w-7xl w-full mx-auto"><div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6"><div><h3 class="text-base font-extrabold text-slate-900">Support Tickets</h3><p class="text-xs text-slate-400">Ticket status changes sync to the customer portal</p></div><div id="ticketsContainer" class="space-y-3"></div></div></div></main>
<?php include 'includes/chat-widget.php'; ?>
<script src="js/main.js"></script></body></html>