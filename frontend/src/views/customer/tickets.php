<?php
$page_title = "Support Tickets · Priority Handling Logistics";
$activePage = 'tickets';
require_once __DIR__ . '/../../includes/header.php';
include_once __DIR__ . '/../../includes/sidebar.php';
?>

<style>
  @keyframes tkFadeUp { from { opacity:0; transform: translateY(10px);} to {opacity:1; transform:none;} }
  .tk-anim { animation: tkFadeUp .5s ease both; }
  .tk-donut { transition: transform .6s cubic-bezier(.2,.8,.2,1); }
  .tk-donut-wrap:hover .tk-donut { transform: rotate(-8deg) scale(1.03); }
</style>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

  <!-- TOP HEADER BAR -->
  <?php
  $pageTitle    = 'Support Tickets';
  $pageSubtitle = 'Requests routed through the Notification Hub';
  $headerSearch = [
      'id'          => 'ticketSearchInput',
      'onkeyup'     => 'searchTicketsList()',
      'placeholder' => 'Search ticket ID, subject, or waybill...',
  ];
  ob_start(); ?>
  <button onclick="toggleChat()" class="crm-btn crm-btn-ghost !h-9 !px-3.5 !text-xs">
      <span class="hidden sm:inline">Help Desk</span>
      <i class="fa-solid fa-headset text-xs"></i>
  </button>
  <button onclick="createNewTicket()" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
      <i class="fa-solid fa-plus text-[10px]"></i>
      <span class="hidden sm:inline">New Ticket</span>
  </button>
  <?php $headerActions = ob_get_clean();
  include_once __DIR__ . '/../../components/customer_header.php';
  ?>

  <!-- TICKETS CONTENT BODY -->
  <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">

    <!-- STATUS STRIP / SUPPORT BANNER -->
    <section class="bg-gradient-to-r from-brand-blue to-brand-darkblue rounded-2xl p-6 lg:p-7 text-white shadow-lg shadow-blue-600/10 flex flex-col sm:flex-row sm:items-center justify-between gap-6 overflow-hidden relative tk-anim">
      <div class="relative z-10">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-blue-100 bg-white/10 border border-white/20 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Support Hub · Online</span>
        <h1 class="text-xl lg:text-2xl font-black italic text-white tracking-tight mt-3">How can we help? 🤝</h1>
        <p class="text-sm text-blue-100 mt-1.5 max-w-md">Open a ticket and our team will route it through the Notification Hub. Average first response time: <span class="font-semibold text-white">2h 14m</span>.</p>
        <div class="flex flex-wrap gap-3 mt-5">
          <button onclick="createNewTicket()" class="bg-white text-brand-blue hover:bg-blue-50 font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors shadow-sm flex items-center gap-2"><i class="fa-solid fa-plus text-xs"></i> New Ticket</button>
          <a href="/documents" class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl border border-white/20 transition-colors flex items-center gap-2"><i class="fa-solid fa-book-open text-xs"></i> Knowledge Base</a>
        </div>
      </div>
      <div class="hidden sm:block absolute -right-8 -top-8 opacity-20 pointer-events-none select-none"><i class="fa-solid fa-headset text-[150px]"></i></div>
    </section>


    <!-- ROW 1: SUPPORT KPI METRICS -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start"><span class="text-xs font-medium text-slate-500">Total Tickets</span><div class="p-2 rounded-xl bg-blue-50 text-brand-blue"><i class="fa-solid fa-tickets text-sm"></i></div></div>
        <div class="mt-4"><p class="text-3xl font-extrabold text-slate-900">6</p><p class="text-xs text-slate-500 font-medium mt-2">All-time requests</p></div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start"><span class="text-xs font-medium text-slate-500">In Progress</span><div class="p-2 rounded-xl bg-blue-50 text-brand-blue"><i class="fa-solid fa-spinner text-sm"></i></div></div>
        <div class="mt-4"><p class="text-3xl font-extrabold text-slate-900">2</p><p class="text-xs text-blue-600 font-semibold mt-2 flex items-center gap-1"><i class="fa-solid fa-circle-notch text-[10px]"></i> Being handled</p></div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start"><span class="text-xs font-medium text-slate-500">Awaiting Reply</span><div class="p-2 rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-clock text-sm"></i></div></div>
        <div class="mt-4"><p class="text-3xl font-extrabold text-slate-900">2</p><p class="text-xs text-amber-600 font-semibold mt-2 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation text-[10px]"></i> Your input needed</p></div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-start"><span class="text-xs font-medium text-slate-500">Resolved</span><div class="p-2 rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-circle-check text-sm"></i></div></div>
        <div class="mt-4"><p class="text-3xl font-extrabold text-slate-900">2</p><p class="text-xs text-emerald-600 font-semibold mt-2 flex items-center gap-1"><i class="fa-solid fa-circle-check text-[10px]"></i> Closed this period</p></div>
      </div>
    </section>

    <!-- ROW 2: MAIN DASHBOARD GRID -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- LEFT: TICKET LIST -->
      <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <h2 class="text-base font-bold text-slate-900">Your Tickets</h2>
            <p class="text-xs text-slate-400 mt-0.5">Track and manage every support request</p>
          </div>
          <button onclick="createNewTicket()" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue flex items-center gap-1"><i class="fa-solid fa-plus text-[10px]"></i> New ticket</button>
        </div>

        <!-- FILTER TABS -->
        <div class="flex flex-wrap gap-1.5">
          <button onclick="filterTickets('all', this)" class="filter-tab crm-pill is-active">All</button>
          <button onclick="filterTickets('in-progress', this)" class="filter-tab crm-pill">In Progress</button>
          <button onclick="filterTickets('awaiting', this)" class="filter-tab crm-pill">Awaiting Reply</button>
          <button onclick="filterTickets('resolved', this)" class="filter-tab crm-pill">Resolved</button>
        </div>

        <!-- TICKETS LIST -->
        <div class="space-y-3" id="ticketsContainer">

          <!-- Ticket: In Progress / High -->
          <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" data-status="in-progress" onclick="alert('Opening ticket TCK-1042 details...')">
            <div class="flex items-start gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation text-sm"></i></div>
              <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[10px] font-mono text-slate-400 font-bold">TCK-1042</span>
                  <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-rose-100 text-rose-600">High</span>
                  <span class="text-[10px] text-slate-400">· PH-WB-208712</span>
                </div>
                <h4 class="text-xs font-extrabold text-slate-900 truncate">Delay on PH-WB-208712 — need updated ETA</h4>
                <p class="text-[10px] text-slate-400">Updated 2h ago · Agent assigned</p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded-full text-[10px]">● In Progress</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </div>
          </div>

          <!-- Ticket: In Progress / Medium -->
          <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" data-status="in-progress" onclick="alert('Opening ticket TCK-1040 details...')">
            <div class="flex items-start gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center shrink-0"><i class="fa-solid fa-box-open text-sm"></i></div>
              <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[10px] font-mono text-slate-400 font-bold">TCK-1040</span>
                  <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-amber-100 text-amber-600">Medium</span>
                  <span class="text-[10px] text-slate-400">· INV-2026-0801</span>
                </div>
                <h4 class="text-xs font-extrabold text-slate-900 truncate">Missing items on consolidated shipment</h4>
                <p class="text-[10px] text-slate-400">Updated 5h ago · Investigating</p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded-full text-[10px]">● In Progress</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </div>
          </div>

          <!-- Ticket: Awaiting Reply / High -->
          <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" data-status="awaiting" onclick="alert('Opening ticket TCK-1039 details...')">
            <div class="flex items-start gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation text-sm"></i></div>
              <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[10px] font-mono text-slate-400 font-bold">TCK-1039</span>
                  <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-rose-100 text-rose-600">High</span>
                  <span class="text-[10px] text-slate-400">· BOL</span>
                </div>
                <h4 class="text-xs font-extrabold text-slate-900 truncate">Request for duplicate Bill of Lading</h4>
                <p class="text-[10px] text-slate-400">Awaiting your reply · 1d ago</p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Awaiting Reply</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </div>
          </div>

          <!-- Ticket: Awaiting Reply / Low -->
          <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" data-status="awaiting" onclick="alert('Opening ticket TCK-1031 details...')">
            <div class="flex items-start gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-tag text-sm"></i></div>
              <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[10px] font-mono text-slate-400 font-bold">TCK-1031</span>
                  <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">Low</span>
                  <span class="text-[10px] text-slate-400">· Account</span>
                </div>
                <h4 class="text-xs font-extrabold text-slate-900 truncate">Update company billing address</h4>
                <p class="text-[10px] text-slate-400">Awaiting your reply · 2d ago</p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Awaiting Reply</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </div>
          </div>

          <!-- Ticket: Resolved / Medium -->
          <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" data-status="resolved" onclick="alert('Opening ticket TCK-1021 details...')">
            <div class="flex items-start gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-circle-check text-sm"></i></div>
              <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[10px] font-mono text-slate-400 font-bold">TCK-1021</span>
                  <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-amber-100 text-amber-600">Medium</span>
                  <span class="text-[10px] text-slate-400">· INV-2026-0790</span>
                </div>
                <h4 class="text-xs font-extrabold text-slate-900 truncate">Billing discrepancy on INV-2026-0790</h4>
                <p class="text-[10px] text-slate-400">Resolved 3d ago · Credit applied</p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Resolved</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </div>
          </div>

          <!-- Ticket: Resolved / Low -->
          <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" data-status="resolved" onclick="alert('Opening ticket TCK-1015 details...')">
            <div class="flex items-start gap-3 min-w-0">
              <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-circle-check text-sm"></i></div>
              <div class="min-w-0 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[10px] font-mono text-slate-400 font-bold">TCK-1015</span>
                  <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">Low</span>
                  <span class="text-[10px] text-slate-400">· Portal</span>
                </div>
                <h4 class="text-xs font-extrabold text-slate-900 truncate">Unable to download waybill PDF</h4>
                <p class="text-[10px] text-slate-400">Resolved 6d ago · Fixed</p>
              </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
              <span class="bg-emerald-100 text-emerald-700 font-semibold px-3 py-1 rounded-full text-[10px]">● Resolved</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </div>
          </div>

          <!-- EMPTY STATE -->
          <div id="ticketEmpty" class="hidden text-center py-10">
            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3"><i class="fa-solid fa-inbox text-lg"></i></div>
            <p class="text-sm font-semibold text-slate-700">No tickets found</p>
            <p class="text-xs text-slate-400 mt-1">Try a different search term or filter.</p>
          </div>

        </div>
      </div>

      <!-- RIGHT: SUPPORT HEALTH + QUICK HELP -->
      <div class="lg:col-span-4 space-y-6">

        <!-- Support Health donut -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
          <div>
            <h3 class="text-base font-extrabold text-slate-900">Support Health</h3>
            <p class="text-xs text-slate-400">Resolution rate this period</p>
          </div>
          <div class="flex items-center justify-center tk-donut-wrap my-5">
            <div class="tk-donut w-36 h-36 rounded-full flex items-center justify-center" style="background: conic-gradient(#10b981 0% 33%, #0066ff 33% 66%, #f59e0b 66% 100%);">
              <div class="w-24 h-24 rounded-full bg-white flex flex-col items-center justify-center text-center shadow-inner">
                <span class="text-2xl font-black text-slate-900 leading-none">33%</span>
                <span class="text-[9px] font-semibold uppercase tracking-wider text-slate-400 mt-1">Resolved</span>
              </div>
            </div>
          </div>
          <div class="space-y-2.5 text-xs">
            <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Resolved</span><span class="font-bold text-slate-800">2</span></div>
            <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-brand-blue"></span>In Progress</span><span class="font-bold text-slate-800">2</span></div>
            <div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-600 font-semibold"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Awaiting Reply</span><span class="font-bold text-slate-800">2</span></div>
          </div>
        </div>

        <!-- Quick Help FAQ -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-base font-extrabold text-slate-900">Quick Help</h3>
              <p class="text-xs text-slate-400">Top asked topics</p>
            </div>
            <i class="fa-solid fa-circle-question text-slate-300"></i>
          </div>
          <div class="space-y-2">
            <button onclick="toggleChat()" class="w-full text-left flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-100 transition group">
              <span class="flex items-center gap-2.5 text-xs font-semibold text-slate-700"><i class="fa-solid fa-truck-fast text-brand-blue"></i> Track a delayed shipment</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] group-hover:text-brand-blue"></i>
            </button>
            <button onclick="toggleChat()" class="w-full text-left flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-100 transition group">
              <span class="flex items-center gap-2.5 text-xs font-semibold text-slate-700"><i class="fa-solid fa-file-invoice text-brand-blue"></i> Dispute an invoice</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] group-hover:text-brand-blue"></i>
            </button>
            <button onclick="toggleChat()" class="w-full text-left flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-100 transition group">
              <span class="flex items-center gap-2.5 text-xs font-semibold text-slate-700"><i class="fa-solid fa-file-lines text-brand-blue"></i> Request shipping docs</span>
              <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] group-hover:text-brand-blue"></i>
            </button>
          </div>
        </div>

        <!-- Live chat CTA -->
        <div class="bg-gradient-to-br from-brand-navy to-brand-sidebar p-6 rounded-2xl border border-slate-800 shadow-sm">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-blue/20 text-brand-blue flex items-center justify-center shrink-0"><i class="fa-solid fa-headset text-sm"></i></div>
            <div>
              <h3 class="text-sm font-bold text-white">Need urgent help?</h3>
              <p class="text-[11px] text-slate-400">Avg response 2h 14m</p>
            </div>
          </div>
          <button onclick="toggleChat()" class="mt-4 w-full text-center bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
            <i class="fa-solid fa-comments text-xs"></i> Start Live Chat
          </button>
        </div>

      </div>
    </section>

  </div>
</main>

    <?php include_once __DIR__ . '/../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
