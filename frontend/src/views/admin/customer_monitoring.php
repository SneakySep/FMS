<!-- ROW 3: CUSTOMER MONITORING SECTION -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

  <!-- LEFT COLUMN: Customer Tier Distribution (Doughnut Chart) -->
  <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm h-full">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-chart-pie text-teal-600 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Customer Tier Distribution</h2>
      </div>
      <a href="customers.php" class="text-xs font-bold text-teal-600 hover:text-teal-800 flex items-center gap-1 transition-colors">
        View All <i class="fa-solid fa-arrow-right text-[10px]"></i>
      </a>
    </div>
    <p class="text-[11px] text-slate-400 mb-4">Customer segments by booking volume</p>

    <!-- Doughnut Chart Canvas -->
    <div id="admin-tier-chart" class="w-full h-[240px]"></div>

    <!-- Tier Legend -->
    <div class="grid grid-cols-2 gap-3 mt-4">
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full" style="background: var(--chart-1)"></span>
        <span class="text-xs text-slate-600">Platinum</span>
        <span class="text-xs font-bold text-slate-900"><?= $tier_platinum ?></span>
      </div>
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full" style="background: var(--chart-2)"></span>
        <span class="text-xs text-slate-600">Gold</span>
        <span class="text-xs font-bold text-slate-900"><?= $tier_gold ?></span>
      </div>
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full" style="background: var(--chart-3)"></span>
        <span class="text-xs text-slate-600">Silver</span>
        <span class="text-xs font-bold text-slate-900"><?= $tier_silver ?></span>
      </div>
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full" style="background: var(--chart-4)"></span>
        <span class="text-xs text-slate-600">Bronze</span>
        <span class="text-xs font-bold text-slate-900"><?= $tier_bronze ?></span>
      </div>
    </div>

    <!-- Total -->
    <div class="mt-4 pt-3 border-t border-slate-100">
      <div class="flex items-center justify-between text-xs">
        <span class="text-slate-500">Total Customers</span>
        <span class="font-bold text-slate-900"><?= $total_customers_tier ?></span>
      </div>
    </div>
  </div>

  <!-- RIGHT COLUMN: Recent Customer Activity -->
  <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm h-full">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
        <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Recent Customer Accounts</h2>
      </div>
      <a href="customers.php" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
      </a>
    </div>
    <p class="text-[11px] text-slate-400 mb-4"><?= count($recent_customers) ?> most recently created</p>

    <?php if (!empty($recent_customers)): ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
              <th class="py-2.5 px-3">Customer</th>
              <th class="py-2.5 px-3">Email</th>
              <th class="py-2.5 px-3 text-center">Tier</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            <?php foreach ($recent_customers as $cust): ?>
              <?php
                $fname = $cust['first_name'] ?? '';
                $lname = $cust['last_name'] ?? '';
                $fullName = trim("$fname $lname") ?: 'Customer';
                $email = $cust['email'] ?? 'N/A';
                $tier = strtoupper($cust['tier'] ?? 'BRONZE');
                $tierBadge = [
                  'PLATINUM' => 'bg-purple-100 text-purple-700',
                  'GOLD'     => 'bg-yellow-100 text-yellow-800',
                  'SILVER'   => 'bg-slate-100 text-slate-700',
                  'BRONZE'   => 'bg-amber-100 text-amber-800'
                ][$tier] ?? 'bg-slate-100 text-slate-600';
              ?>
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-2.5 px-3 font-bold text-slate-800"><?= htmlspecialchars($fullName) ?></td>
                <td class="py-2.5 px-3 text-slate-500"><?= htmlspecialchars($email) ?></td>
                <td class="py-2.5 px-3 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?= $tierBadge ?>">
                    <?= $tier ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-xs text-slate-400 italic py-6 text-center">No customer accounts found yet.</p>
    <?php endif; ?>
  </div>
</div>