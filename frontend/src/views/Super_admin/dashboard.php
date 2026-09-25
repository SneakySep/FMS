<?php
$activePage = 'dashboard';
$page_title = 'Super Admin Dashboard';
require_once __DIR__ . '/../../helpers/super_admin_helper.php';
superadmin_require_role();
$data = load_superadmin_mock();
$c = superadmin_counts($data);
$map = superadmin_user_map($data['users']);
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
<?php include_once '../../components/top_header.php'; ?>
<div class="mb-4 flex items-center gap-2 text-[11px] font-bold">
<span class="px-2.5 py-1 rounded-full bg-violet-100 text-violet-700 border border-violet-200">SUPER ADMIN</span>
<span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">DEMO DATA (JSON)</span>
<span class="text-slate-400 font-semibold">RA 10173: masked PII in demo mode</span>
</div>
<?php include_once __DIR__ . '/components/stat_cards.php'; ?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
<div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100 flex items-center justify-between">
<div><h2 class="text-sm font-black text-slate-900">Restricted Accounts</h2>
<p class="text-[11px] text-slate-400">Reached maximum login attempts (5/5)</p></div>
<a href="restricted_accounts.php" class="text-[11px] font-bold text-indigo-600 hover:underline">View all (<?= $c['restricted_count'] ?>)</a>
</div>
<div class="divide-y divide-slate-100">
<?php if (empty($c['restricted_list'])): ?>
<div class="p-8 text-center text-xs text-slate-400">No restricted accounts.</div>
<?php else: foreach (array_slice($c['restricted_list'],0,4) as $r):
$u = $map[$r['user_id']] ?? ['first_name'=>'Unknown','last_name'=>'','email'=>'','role'=>'']; ?>
<div class="p-4 flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black"><i class="fa-solid fa-user-lock"></i></div>
<div class="min-w-0 flex-1"><p class="text-xs font-bold text-slate-800 truncate"><?= htmlspecialchars(trim(($u['first_name']??'').' '.($u['last_name']??''))) ?> <span class="text-slate-400 font-semibold"><?= htmlspecialchars($r['user_id']) ?></span></p>
<p class="text-[11px] text-slate-400 truncate"><?= htmlspecialchars($u['email'] ?? '') ?> · <?= htmlspecialchars($r['reason'] ?? '') ?> · <?= (int)($r['attempts']??0) ?>/<?= (int)($r['max_attempts']??5) ?></p></div>
<span class="text-[10px] font-bold px-2 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200">LOCKED</span>
</div>
<?php endforeach; endif; ?>
</div></div>
<div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100 flex items-center justify-between">
<div><h2 class="text-sm font-black text-slate-900">Audit Notifications</h2>
<p class="text-[11px] text-slate-400"><?= $c['audit_unread'] ?> unread high/critical events</p></div>
<a href="audit_trail.php" class="text-[11px] font-bold text-indigo-600 hover:underline">Open SIEM</a>
</div>
<div class="divide-y divide-slate-100">
<?php if (empty($c['recent_unread'])): ?>
<div class="p-8 text-center text-xs text-slate-400"><i class="fa-solid fa-circle-check text-emerald-500 block text-xl mb-2"></i>All critical events reviewed.</div>
<?php else: foreach ($c['recent_unread'] as $e): ?>
<div class="p-4 flex items-start gap-3">
<span class="mt-0.5 text-[10px] font-black px-2 py-0.5 rounded-full border <?= superadmin_sev_class($e['severity'] ?? '') ?>"><?= strtoupper(htmlspecialchars($e['severity'] ?? '')) ?></span>
<div class="min-w-0"><p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($e['action'] ?? '') ?></p>
<p class="text-[11px] text-slate-400 truncate"><?= htmlspecialchars($e['details'] ?? '') ?></p>
<p class="text-[10px] text-slate-400 mt-0.5"><?= htmlspecialchars($e['timestamp'] ?? '') ?> · <?= htmlspecialchars($e['actor'] ?? '') ?></p></div>
</div>
<?php endforeach; endif; ?>
</div></div>
<div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100"><h2 class="text-sm font-black text-slate-900">Users by Role</h2>
<p class="text-[11px] text-slate-400">Distribution of <?= $c['total_users'] ?> accounts</p></div>
<div class="p-5"><div id="roleChart"></div>
<div class="mt-3 space-y-2 text-[11px] font-semibold text-slate-600">
<?php foreach ($c['by_role'] as $role=>$n): ?>
<div class="flex justify-between"><span class="capitalize"><?= str_replace('_',' ',$role) ?></span><span class="font-black text-slate-900"><?= $n ?></span></div>
<?php endforeach; ?>
</div></div></div>
</div>
<div class="mt-6 bg-slate-900 text-slate-300 rounded-2xl p-5 text-[11px] leading-relaxed border border-slate-800">
<p class="font-bold text-white text-xs mb-1"><i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i>Data Privacy Act (RA 10173) Notice</p>
This console shows <b>masked demo data only</b>. Personal data is minimized, access is role-gated to Super_admin, and every restrict / unrestrict / create / export action is written to the audit trail. Retention: 90 days. Do not upload real customer PII into the JSON mock.
</div>
</main>
<script>
window.SUPERADMIN_ROLE_DATA = <?= json_encode(array_values($c['by_role'])) ?>;
window.SUPERADMIN_ROLE_LABELS = <?= json_encode(array_map(function($k){return str_replace('_',' ',$k);}, array_keys($c['by_role']))) ?>;
</script>
<script src="../../../assets/js/super_admin/dashboard.js"></script>
<?php include_once '../../includes/footer.php'; ?>
