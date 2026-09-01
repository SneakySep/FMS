<!-- MY CONTRACTS -->
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-sm font-bold text-slate-900">My Contracts</h2>
    <a href="quotes.php" class="text-xs font-bold text-purple-600 hover:text-purple-700">View All &rarr;</a>
  </div>

  <?php if (!empty($contracts)): ?>
    <div class="space-y-3">
      <?php foreach ($contracts as $contract): ?>
        <?php
          $contract_id   = $contract['id'] ?? $contract['contract_id'] ?? '#';
          $cust_name     = $contract['company_name'] ?? $contract['customer_name'] ?? 'N/A';
          $status        = strtoupper(trim($contract['status'] ?? 'DRAFT'));
          $badge_class   = getContractStatusBadge($status);
          $created       = $contract['created_at'] ?? '';
          $amount        = (float)($contract['total_amount'] ?? $contract['agreed_amount'] ?? 0);
        ?>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-purple-50 hover:border-purple-100 transition flex items-center gap-3">
          <div class="shrink-0 w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
            <i class="fa-solid fa-file-contract text-xs"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2">
              <h3 class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars($cust_name) ?></h3>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?= $badge_class ?>">
                <?= htmlspecialchars($status) ?>
              </span>
            </div>
            <div class="flex items-center justify-between mt-1">
              <p class="text-[11px] text-slate-500"><?= htmlspecialchars($contract_id) ?></p>
              <?php if ($amount > 0): ?>
                <p class="text-[11px] font-bold text-emerald-600">₱<?= number_format($amount, 2) ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-6">
      <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
        <i class="fa-solid fa-file-contract text-slate-400 text-sm"></i>
      </div>
      <p class="text-xs text-slate-400 italic">No active contracts found.</p>
      <a href="quotes.php" class="text-[11px] font-semibold text-purple-600 hover:underline mt-2 inline-block">Create a quote &rarr;</a>
    </div>
  <?php endif; ?>
</div>