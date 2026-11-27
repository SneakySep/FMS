<?php
$current_status = $current_status ?? 'all';
$search_query   = $search_query ?? '';
$service_filter = $service_filter ?? '';

// Status filter tabs (labels only - counts require a backend stats route)
$statusTabs = [
    'all'         => 'All',
    'new_inquiry' => 'New',
    'qualifying'  => 'Qualifying',
    'quote_sent'  => 'Quote Sent',
    'negotiation' => 'Negotiation',
];

$serviceOptions = ['Full Truckload (FTL)', 'Less Than Truckload (LTL)', 'Sea Freight', 'Air Freight'];
?>

<!-- FILTER BAR -->
<div class="bg-white border border-slate-200 p-4 sm:p-5 rounded-2xl shadow-sm mb-6">

    <!-- STATUS FILTER TABS -->
    <div class="flex items-center gap-2 mb-4 overflow-x-auto pb-1 custom-scrollbar">
        <?php foreach ($statusTabs as $key => $label):
            $isActive = ($current_status === $key);
        ?>
            <a href="<?= htmlspecialchars(leadsFilterUrl(['status' => $key])) ?>"
               class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition border
                      <?= $isActive
                          ? 'bg-purple-600 text-white border-purple-600 shadow-md'
                          : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                <?= htmlspecialchars($label) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- SEARCH + SERVICE FILTER -->
    <form method="GET" action="my_leads.php" class="flex flex-col sm:flex-row gap-3">
        <input type="hidden" name="status" value="<?= htmlspecialchars($current_status) ?>">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass text-xs text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>"
                   placeholder="Search company, contact person or inquiry code..."
                   class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
        </div>

        <select name="service_type"
                class="sm:w-56 px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 text-xs text-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            <option value="">All Service Types</option>
            <?php foreach ($serviceOptions as $opt): ?>
                <option value="<?= htmlspecialchars($opt) ?>" <?= ($service_filter === $opt) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($opt) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition active:scale-95 flex items-center justify-center gap-2">
            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
            <span class="hidden sm:inline">Search</span>
        </button>

        <?php if ($search_query !== '' || $service_filter !== ''): ?>
            <a href="<?= htmlspecialchars(leadsFilterUrl(['search' => '', 'service_type' => ''])) ?>"
               class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-rotate-left text-[11px]"></i>
                <span class="hidden sm:inline">Clear</span>
            </a>
        <?php endif; ?>
    </form>

</div>
