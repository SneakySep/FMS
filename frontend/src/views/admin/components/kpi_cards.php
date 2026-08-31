<!-- ROW 1: TOP KPI METRICS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

  <!-- Card 1: Total Revenue (MTD) -->
  <a href="analytics.php" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-500 group-hover:text-indigo-600 transition-colors">Total Revenue (MTD)</span>
      <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-coins text-xs"></i>
      </div>
    </div>
    <div>
      <div class="text-3xl font-bold text-slate-900 mb-2">₱<?= number_format($revenue_current, 2) ?></div>
      <div class="flex items-center text-xs font-medium <?= $revenue_diff >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
        <i class="fa-solid <?= $revenue_diff >= 0 ? 'fa-arrow-up-right' : 'fa-arrow-down-right' ?> mr-1 text-[10px]"></i>
        <span>₱<?= number_format(abs($revenue_diff), 2) ?> vs last month</span>
      </div>
    </div>
  </a>

  <!-- Card 2: Customers Closed (MTD) -->
  <a href="customers.php" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-emerald-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-500 group-hover:text-emerald-600 transition-colors">Customers Closed (MTD)</span>
      <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-user-check text-xs"></i>
      </div>
    </div>
    <div>
      <div class="text-3xl font-bold text-slate-900 mb-2"><?= htmlspecialchars((string)$customers_mtd) ?></div>
      <div class="flex items-center text-xs font-medium <?= $customers_mtd_diff >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
        <i class="fa-solid <?= $customers_mtd_diff >= 0 ? 'fa-arrow-up-right' : 'fa-arrow-down-right' ?> mr-1 text-[10px]"></i>
        <span><?= abs($customers_mtd_diff) ?> vs last month</span>
      </div>
    </div>
  </a>

  <!-- Card 3: Active Customer -->
  <a href="customers.php" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-purple-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-500 group-hover:text-purple-600 transition-colors">Active Customer</span>
      <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-user text-xs"></i>
      </div>
    </div>
    <div>
      <div class="text-3xl font-bold text-slate-900 mb-2"><?= htmlspecialchars((string)$active_customers) ?></div>
      <div class="flex items-center text-xs font-medium text-purple-600">
        <i class="fa-solid fa-check-circle mr-1 text-[10px]"></i>
        <span>Registered Portal Users</span>
      </div>
    </div>
  </a>

  <!-- Card 4: Customer Ticket (Closed Won - Gagawan Pa Lang ng Account) -->
  <a href="tickets.php" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-rose-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-semibold text-slate-500 group-hover:text-rose-600 transition-colors">Customer Ticket</span>
      <div class="w-8 h-8 bg-rose-100 text-rose-500 rounded-lg flex items-center justify-center">
        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
      </div>
    </div>
    <div>
      <div class="text-3xl font-bold text-slate-900 mb-2"><?= htmlspecialchars((string)$ticket_count) ?></div>
      <div class="flex items-center text-xs font-medium text-rose-500">
        <i class="fa-solid fa-clock mr-1 text-[10px]"></i>
        <span>Needs Account Provisioning</span>
      </div>
    </div>
  </a>

</div>