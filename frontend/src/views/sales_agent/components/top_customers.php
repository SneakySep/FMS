<!-- TOP CUSTOMERS (by total bookings) -->
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
  <div class="flex items-center justify-between mb-1">
    <h2 class="text-sm font-bold text-slate-900">Top Customers</h2>
    <a href="/sales_agent/customer.php" class="text-xs font-bold text-purple-600 hover:text-purple-700">View All &rarr;</a>
  </div>
  <p class="text-[11px] text-slate-400 mb-4">Highest total bookings</p>

  <?php if (!empty($top_customers)): ?>
    <div class="space-y-3">
      <?php foreach ($top_customers as $index => $cust): ?>
        <?php
          $company = $cust["company_name"] ?? $cust["company"] ?? "N/A";
          $contact = $cust["contact_person"] ?? $cust["name"] ?? "No Contact Person";
          $bookings = (int)($cust["total_bookings"] ?? $cust["bookings"] ?? 0);
          $tier = strtoupper($cust["tier"] ?? "BRONZE");
          $cust_id = $cust["id"] ?? "";
          $pct = $top_max > 0 ? min(100, round(($bookings / $top_max) * 100)) : 0;

          // Rank medal coloring
          $rank_color = match ($index) {
            0 => "bg-amber-100 text-amber-700",
            1 => "bg-slate-200 text-slate-600",
            2 => "bg-orange-100 text-orange-700",
            default => "bg-slate-100 text-slate-500"
          };

          $tier_badge = match ($tier) {
            "PLATINUM" => "bg-purple-100 text-purple-700 border-purple-200",
            "GOLD"     => "bg-yellow-100 text-yellow-800 border-yellow-300",
            "SILVER"   => "bg-slate-100 text-slate-700 border-slate-200",
            default    => "bg-amber-100 text-amber-800 border-amber-200"
          };
        ?>
        <a href="view_customer.php?id=<?= urlencode($cust_id) ?>"
           class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-purple-50 hover:border-purple-100 transition">
          <!-- Rank -->
          <div class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold <?= $rank_color ?>">
            <?= $index + 1 ?>
          </div>

          <!-- Avatar initial -->
          <div class="shrink-0 w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center text-sm font-bold">
            <?= htmlspecialchars(strtoupper(substr($company, 0, 1))) ?>
          </div>

          <!-- Details -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
              <h3 class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($company) ?></h3>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border tracking-wide whitespace-nowrap leading-none <?= $tier_badge ?>">
                <?= htmlspecialchars($tier) ?>
              </span>
            </div>
            <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($contact) ?></p>
            <!-- Progress bar relative to top bookings -->
            <div class="w-full bg-slate-200 rounded-full h-1.5 mt-1.5">
              <div class="bg-purple-500 h-1.5 rounded-full" style="width: <?= $pct ?>%;"></div>
            </div>
          </div>

          <!-- Bookings count -->
          <div class="shrink-0 text-right">
            <p class="text-sm font-extrabold text-slate-900"><?= $bookings ?></p>
            <p class="text-[10px] text-slate-400">bookings</p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-xs text-slate-400 italic py-4 text-center">No customers found.</p>
  <?php endif; ?>
</div>
