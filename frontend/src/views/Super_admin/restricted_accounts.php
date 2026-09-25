<?php
$activePage = 'restricted_accounts';
$page_title = 'Restricted Accounts';
require_once __DIR__ . '/../../helpers/super_admin_helper.php';
superadmin_require_role();
$data = load_superadmin_mock();
$c = superadmin_counts($data);
$map = superadmin_user_map($data['users']);
$list = $data['restricted_accounts'];
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
<?php include_once '../../components/top_header.php'; ?>
<div class="mb-4 flex items-center gap-2 text-[11px] font-bold">
<span class="px-2.5 py-1 rounded-full bg-violet-100 text-violet-700 border border-violet-200">SUPER ADMIN</span>
<span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">DEMO DATA (JSON)</span>
<span class="text-slate-400">Threshold: 5 failed attempts = auto-restrict</span>
</div>
<?php include_once __DIR__ . '/components/stat_cards.php'; ?>
<div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100 flex flex-wrap gap-3 items-center justify-between">
<div><h2 class="text-sm font-black text-slate-900">Accounts at Maximum Attempts</h2>
<p class="text-[11px] text-slate-400">Locked + watchlist. PII is masked per RA 10173.</p></div>
<div class="flex gap-2">
<input id="resSearch" placeholder="Search..." class="px-3 py-2 border border-slate-200 rounded-xl text-xs w-56">
<select id="resStatus" class="px-3 py-2 border border-slate-200 rounded-xl text-xs"><option value="">All</option><option value="restricted">restricted</option><option value="watchlist">watchlist</option></select>
</div></div>
<div class="overflow-x-auto"><table class="w-full text-left text-xs">
<thead><tr class="bg-slate-50/70 text-[10px] uppercase text-slate-400 border-b border-slate-100"><th class="px-5 py-3">User</th><th class="px-5 py-3">Reason</th><th class="px-5 py-3">Attempts</th><th class="px-5 py-3">Restricted At</th><th class="px-5 py-3">By</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-center">Action</th></tr></thead>
<tbody id="resBody" class="divide-y divide-slate-100">
<?php foreach ($list as $r): $u = $map[$r['user_id']] ?? ['first_name'=>'Unknown','last_name'=>'','email'=>'','role'=>'']; ?>
<tr class="res-row hover:bg-slate-50" data-search="<?= htmlspecialchars(strtolower(($u['first_name']??'').' '.($u['last_name']??'').' '.($u['email']??'').' '.($r['user_id']??''))) ?>" data-status="<?= htmlspecialchars($r['status'] ?? '') ?>" data-id="<?= htmlspecialchars($r['user_id'] ?? '') ?>">
<td class="px-5 py-3 font-bold text-slate-800"><?= htmlspecialchars(trim(($u['first_name']??'').' '.($u['last_name']??''))) ?><span class="block text-[10px] font-semibold text-slate-400"><?= htmlspecialchars($r['user_id'] ?? '') ?> · <?= htmlspecialchars($u['email'] ?? '') ?></span></td>
<td class="px-5 py-3 text-slate-500"><?= htmlspecialchars($r['reason'] ?? '') ?></td>
<td class="px-5 py-3 font-mono font-bold"><?= (int)($r['attempts']??0) ?>/<?= (int)($r['max_attempts']??5) ?></td>
<td class="px-5 py-3 text-slate-400"><?= htmlspecialchars($r['restricted_at'] ?? '—') ?></td>
<td class="px-5 py-3 text-slate-500"><?= htmlspecialchars($r['restricted_by'] ?? '—') ?></td>
<td class="px-5 py-3"><span class="res-pill px-2 py-1 rounded-full text-[10px] font-bold border <?= ($r['status']??'')==='restricted'?'bg-rose-100 text-rose-700 border-rose-200':'bg-amber-100 text-amber-700 border-amber-200' ?>"><?= htmlspecialchars($r['status'] ?? '') ?></span></td>
<td class="px-5 py-3 text-center"><button class="res-toggle px-3 py-1.5 rounded-lg text-[11px] font-bold bg-slate-900 text-white hover:bg-slate-700">Unrestrict</button></td>
</tr>
<?php endforeach; ?>
</tbody></table><div id="resEmpty" class="hidden p-8 text-center text-xs text-slate-400">No accounts match.</div>
</div></div>
<div class="mt-4 text-[11px] text-slate-400">Unrestrict/Restrict here is demo-only (localStorage). Backend should enforce lockout + write to audit_trail table.</div>
</main>
<script src="../../../assets/js/super_admin/restricted_accounts.js"></script>
<?php include_once '../../includes/footer.php'; ?>
