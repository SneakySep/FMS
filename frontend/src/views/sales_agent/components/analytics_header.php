<!-- ANALYTICS-SPECIFIC HEADER -->
<header class="w-full mb-8">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    
    <!-- LEFT: Title & Breadcrumbs -->
    <div>
        <nav class="flex items-center gap-1 text-[10px] text-slate-400 font-medium mb-1">
            <span>Sales Agent</span>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-indigo-600">Analytics</span>
        </nav>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Analytics Dashboard</h1>
        <p class="text-slate-500 text-sm">Monitor your pipeline performance and revenue growth.</p>
    </div>

    <!-- RIGHT: Date Filter & Actions -->
    <div class="flex items-center gap-3">
        <!-- Date Range Filter -->
        <button class="flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:border-indigo-300 transition-all shadow-sm">
            <i class="fa-solid fa-calendar-days text-slate-400"></i>
            <span>Last 30 Days</span>
            <i class="fa-solid fa-chevron-down text-[10px] ml-1"></i>
        </button>

        <!-- Primary Action -->
        <button type="button" onclick="openLeadModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-sm hover:shadow-indigo-200 flex items-center gap-2">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>New Lead</span>
        </button>
    </div>
  </div>
</header>
