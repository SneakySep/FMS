<?php
$pageTitle = 'Chat - Priority Handling Logistics';
$activePage = 'chat';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal Priority Handling Logistics support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f4f7fa] dark:bg-[#0a1628]">
<header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between flex-shrink-0 dark:bg-[#112240] dark:border-slate-700/40">
      <!-- Title -->
      <h2 class="text-2xl font-bold italic tracking-tight text-slate-900">Chat</h2>

      <!-- Search Bar -->
      <div class="w-96">
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
            <i class="fa-solid fa-magnifying-glass text-sm"></i>
          </span>
          <input type="text" placeholder="Search leads, customer, quotes..." class="w-full text-xs pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue transition">
        </div>
      </div>

      <!-- Right Action Items -->
      <div class="flex items-center space-x-4">
        <!-- Notification Bell -->
        <button class="relative p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100 transition">
          <i class="fa-solid fa-bell text-lg"></i>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
        </button>

        <!-- + New Quote Button -->
        <a href="quotes.php" class="bg-gradient-to-r from-brand-blue to-brand-darkblue hover:from-brand-darkblue hover:to-brand-navy text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all inline-flex items-center gap-1.5">
<span>+ New Quote</span>
</a>
      </div>
    </header>
<div class="flex-1 overflow-hidden p-6 bg-[#f4f7fa] dark:bg-[#0a1628]">
      <div class="h-full bg-white rounded-2xl border border-slate-200/80 shadow-sm flex overflow-hidden dark:bg-[#112240]">
        
        <!-- LEFT COLUMN: Conversation List -->
        <div class="w-80 border-r border-slate-200/80 flex flex-col flex-shrink-0 bg-slate-50/30 dark:bg-[#0f1f3a]">
          
          <div class="overflow-y-auto flex-1 divide-y divide-slate-100">

            <!-- Chat Item 1 (Selected) -->
            <div class="p-4 bg-brand-blue/10 border-l-4 border-brand-blue cursor-pointer transition">
              <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                  <div class="w-9 h-9 rounded-full bg-blue-500 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                    CH
                  </div>
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 truncate">Charlie Hub Inc.</h4>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">You: Sure, let's do the volume-ti...</p>
                  </div>
                </div>
                <div class="text-right flex-shrink-0 pl-2">
                  <span class="text-[10px] text-slate-400">9:14 AM</span>
                  <div class="mt-1 flex justify-end">
                    <span class="w-4 h-4 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">1</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat Item 2 -->
            <div class="p-4 hover:bg-slate-50 cursor-pointer transition">
              <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                  <div class="w-9 h-9 rounded-full bg-blue-500 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                    MF
                  </div>
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 truncate">Meridian Foods Co.</h4>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">Client: We need custom liability ..</p>
                  </div>
                </div>
                <div class="text-right flex-shrink-0 pl-2">
                  <span class="text-[10px] text-slate-400">Yesterday</span>
                  <div class="mt-1 flex justify-end">
                    <span class="w-4 h-4 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center">1</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat Item 3 -->
            <div class="p-4 hover:bg-slate-50 cursor-pointer transition">
              <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                  <div class="w-9 h-9 rounded-full bg-blue-500 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                    NL
                  </div>
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 truncate">Northbay Logistics</h4>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">You: See you at 2:30 PM today.</p>
                  </div>
                </div>
                <div class="text-right flex-shrink-0 pl-2">
                  <span class="text-[10px] text-slate-400">Jul 29</span>
                </div>
              </div>
            </div>

            <!-- Chat Item 4 -->
            <div class="p-4 hover:bg-slate-50 cursor-pointer transition">
              <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                  <div class="w-9 h-9 rounded-full bg-blue-500 text-white font-bold flex items-center justify-center text-xs flex-shrink-0">
                    TB
                  </div>
                  <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-900 truncate">Tan Bros. Distribution</h4>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">Client: Sounds good. sending the signe...</p>
                  </div>
                </div>
                <div class="text-right flex-shrink-0 pl-2">
                  <span class="text-[10px] text-slate-400">Jul 26</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- RIGHT COLUMN: Active Chat Messages Thread -->
        <div class="flex-1 flex flex-col min-w-0 bg-white">
          
          <!-- Chat Thread Header -->
          <div class="px-6 py-4 border-b border-slate-200/80 flex items-center justify-between flex-shrink-0">
            <div>
              <h3 class="text-sm font-bold text-slate-900">Charlie Hub Inc.</h3>
              <p class="text-[11px] text-slate-400 mt-0.5">
                Ticket #TQ-3391 · Escalated by AI · confidence 0.58
              </p>
            </div>
            <button class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
              Open Ticket
            </button>
          </div>

          <!-- Messages Scroll Area -->
          <div class="flex-1 overflow-y-auto p-6 space-y-6">
            
            <!-- Message 1: Customer -->
            <div class="max-w-xl">
              <div class="bg-slate-100/80 text-slate-800 rounded-2xl rounded-tl-sm p-4 space-y-1">
                <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                  CUSTOMER
                </div>
                <p class="text-xs leading-relaxed font-medium">
                  Hi, we're looking at shipping around 25 containers a month from Cebu to Manila. What kind of rate can you offer?
                </p>
              </div>
            </div>

            <!-- Message 2: AI Agent -->
            <div class="max-w-xl">
              <div class="bg-brand-blue/10 border border-brand-blue/20 text-slate-800 rounded-2xl rounded-tl-sm p-4 space-y-1.5">
                <div class="text-[10px] font-bold tracking-wider text-brand-blue uppercase">
                  AI AGENT
                </div>
                <p class="text-xs leading-relaxed font-medium text-brand-navy">
                  Thanks for reaching out! For that volume we typically offer tiered pricing. Let me connect you with a sales agent who can give you exact numbers.
                </p>
                <div class="text-[10px] text-brand-blue pt-0.5">
                  confidence 0.58 · escalated to Jenna Reyes
                </div>
              </div>
            </div>

            <!-- Message 3: Customer -->
            <div class="max-w-xl">
              <div class="bg-slate-100/80 text-slate-800 rounded-2xl rounded-tl-sm p-4 space-y-1">
                <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                  CUSTOMER
                </div>
                <p class="text-xs leading-relaxed font-medium">
                  Okay, also — can we get flexible payment terms? Our previous provider gave us 30 days.
                </p>
              </div>
            </div>

            <!-- Message 4: Agent Response (Right Aligned Blue Bubble) -->
            <div class="flex justify-end">
              <div class="max-w-xl">
                <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm p-4 shadow-sm">
                  <p class="text-xs leading-relaxed font-medium">
                    Hi! This is Jenna from Priority Handling Logistics. Happy to work with your volume — for 20+ containers/month we can offer our volume-tier rate plus 30-day terms. Want to hop on a quick call this week?
                  </p>
                </div>
                <div class="text-right mt-1.5">
                  <span class="text-[10px] font-medium text-slate-400">10:02 AM</span>
                </div>
              </div>
            </div>

          </div>

          <!-- Bottom Message Input Area -->
          <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
            <div class="flex items-center space-x-2">
              <input type="text" placeholder="Type a message..." class="flex-1 text-xs px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
              <button class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-xl transition shadow-sm">
                <i class="fa-solid fa-paper-plane text-sm"></i>
              </button>
            </div>
          </div>

        </div>

      </div>
    
</div>
</main>




<script src="js/main.js"></script>
</body>
</html>
