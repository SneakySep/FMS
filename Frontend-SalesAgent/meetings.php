<?php
$pageTitle = 'Meetings - SwiftFreight Agent Portal';
$activePage = 'meetings';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal SwiftFreight support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">
<header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between flex-shrink-0">
      <!-- Title -->
      <h2 class="text-2xl font-bold italic tracking-tight text-slate-900">Meetings</h2>

      <!-- Search Bar -->
      <div class="w-96">
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input type="text" placeholder="Search leads, customer, quotes..." class="w-full text-xs pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition">
        </div>
      </div>

      <!-- Right Action Items -->
      <div class="flex items-center space-x-4">
        <!-- Notification Bell -->
        <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
        </button>

        <!-- + New Quote Button -->
        <a href="quotes.php" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all inline-flex items-center gap-1.5">
<span>+ New Quote</span>
</a>
      </div>
    </header>
<div class="flex-1 overflow-y-auto p-8 bg-[#F8FAFC]">
      
      <!-- Meetings Card Container -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 max-w-6xl">
        
        <!-- Card Header -->
        <div class="mb-6">
          <h3 class="text-base font-bold text-slate-900">Meetings</h3>
          <p class="text-xs text-slate-500 mt-0.5">Negotiation meetings tied to your assigned tickets</p>
        </div>

        <!-- Meetings Table / List -->
        <div class="divide-y divide-slate-100">

          <!-- Item 1 -->
          <div class="py-4 flex items-center justify-between hover:bg-slate-50/50 rounded-lg px-2 transition">
            <div class="flex items-start space-x-12">
              <!-- Date & Time -->
              <div class="w-24">
                <div class="text-xs font-semibold text-slate-800">Today</div>
                <div class="text-xs font-bold text-slate-900 mt-0.5">2:30 PM</div>
              </div>

              <!-- Meeting Details -->
              <div>
                <h4 class="text-xs font-bold text-slate-800">
                  Northbay Logistics <span class="font-normal text-slate-400">—</span> SLA & pricing discussion
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Ticket #TQ-3370 · Scheduled by Sales Agent
                </p>
              </div>
            </div>

            <!-- Status Pill -->
            <div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                Confirmed
              </span>
            </div>
          </div>

          <!-- Item 2 -->
          <div class="py-4 flex items-center justify-between hover:bg-slate-50/50 rounded-lg px-2 transition">
            <div class="flex items-start space-x-12">
              <!-- Date & Time -->
              <div class="w-24">
                <div class="text-xs font-semibold text-slate-800">Today</div>
                <div class="text-xs font-bold text-slate-900 mt-0.5">4:00 PM</div>
              </div>

              <!-- Meeting Details -->
              <div>
                <h4 class="text-xs font-bold text-slate-800">
                  Tan Bros. Distribution <span class="font-normal text-slate-400">—</span> contract walkthrough
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Ticket #TQ-3364 · Scheduled by AI Agent
                </p>
              </div>
            </div>

            <!-- Status Pill -->
            <div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                Confirmed
              </span>
            </div>
          </div>

          <!-- Item 3 -->
          <div class="py-4 flex items-center justify-between hover:bg-slate-50/50 rounded-lg px-2 transition">
            <div class="flex items-start space-x-12">
              <!-- Date & Time -->
              <div class="w-24">
                <div class="text-xs font-semibold text-slate-800">Aug 5</div>
                <div class="text-xs font-bold text-slate-900 mt-0.5">10:00 AM</div>
              </div>

              <!-- Meeting Details -->
              <div>
                <h4 class="text-xs font-bold text-slate-800">
                  Del Rosario Trading <span class="font-normal text-slate-400">—</span> intro call
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Ticket #TQ-3379 · Scheduled by AI Agent
                </p>
              </div>
            </div>

            <!-- Status Pill -->
            <div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-200/60">
                Pending confirm
              </span>
            </div>
          </div>

          <!-- Item 4 -->
          <div class="py-4 flex items-center justify-between hover:bg-slate-50/50 rounded-lg px-2 transition">
            <div class="flex items-start space-x-12">
              <!-- Date & Time -->
              <div class="w-24">
                <div class="text-xs font-semibold text-slate-800">Jul 25</div>
                <div class="text-xs font-bold text-slate-900 mt-0.5">1:00 PM</div>
              </div>

              <!-- Meeting Details -->
              <div>
                <h4 class="text-xs font-bold text-slate-800">
                  Ocean Peak Traders <span class="font-normal text-slate-400">—</span> final signing
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Ticket #TQ-3350 · Scheduled by Sales Agent
                </p>
              </div>
            </div>

            <!-- Status Pill -->
            <div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                Completed
              </span>
            </div>
          </div>

        </div>

      </div>

    
</div>
</main>

<?php include 'includes/chat-widget.php'; ?>



<script src="js/main.js"></script>
</body>
</html>
