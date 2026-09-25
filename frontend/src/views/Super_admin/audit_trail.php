<?php
$activePage = 'audit_trail';
$page_title = 'Audit Trail SIEM';
require_once __DIR__ . '/../../helpers/super_admin_helper.php';
superadmin_require_role();
$data = load_superadmin_mock();
$c = superadmin_counts($data);
$events = $data['audit_events'];
usort($events, function($a,$b){ return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? ''); });
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
<?php include_once '../../components/top_header.php'; ?>
<div class="mb-4 p-4 rounded-2xl bg-slate-900 text-slate-300 text-[11px] leading-relaxed border border-slate-800">
<span class="font-black text-white text-xs">Security Information & Event Management · RA 10173 Compliant (Demo)</span><br>
Purpose-limited security logging. Emails/IPs are masked, passwords & OTPs are never stored, retention 90 days, Super_admin access only. Export produces a masked CSV.
</div>
<?php include_once __DIR__ . '/components/stat_cards.php'; ?>
<div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100 flex flex-wrap gap-2 items-center">
<input id="auditSearch" placeholder="Search actor / action / details..." class="px-3 py-2 border border-slate-200 rounded-xl text-xs w-64">
<select id="auditSev" class="px-3 py-2 border border-slate-200 rounded-xl text-xs"><option value="">All severity</option><option value="critical">critical</option><option value="high">high</option><option value="medium">medium</option><option value="low">low</option></select>
<select id="auditCat" class="px-3 py-2 border border-slate-200 rounded-xl text-xs"><option value="">All categories</option><option value="authentication">authentication</option><option value="account">account</option><option value="security">security</option><option value="system">system</option></select>
<button id="auditExport" class="ml-auto px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-slate-700"><i class="fa-solid fa-download mr-1"></i>Export masked CSV</button>
<button id="auditRead" class="px-4 py-2 bg-white border border-slate-200 text-xs font-bold rounded-xl hover:bg-slate-50">Mark all reviewed</button>
</div>
<div class="overflow-x-auto"><table class="w-full text-left text-xs">
<thead><tr class="bg-slate-50/70 text-[10px] uppercase text-slate-400 border-b border-slate-100"><th class="px-5 py-3">Timestamp</th><th class="px-5 py-3">Actor</th><th class="px-5 py-3">Action</th><th class="px-5 py-3">Resource</th><th class="px-5 py-3">Severity</th><th class="px-5 py-3">IP</th><th class="px-5 py-3">Details</th><th class="px-5 py-3">State</th></tr></thead>
<tbody id="auditBody" class="divide-y divide-slate-100">
<?php foreach ($events as $e): ?>
<tr class="audit-row hover:bg-slate-50" data-search="<?= htmlspecialchars(strtolower(($e['actor']??'').' '.($e['action']??'').' '.($e['details']??'').' '.($e['resource']??''))) ?>" data-sev="<?= htmlspecialchars($e['severity'] ?? '') ?>" data-cat="<?= htmlspecialchars($e['category'] ?? '') ?>" data-id="<?= htmlspecialchars($e['id'] ?? '') ?>">
<td class="px-5 py-3 text-slate-500 whitespace-nowrap"><?= htmlspecialchars($e['timestamp'] ?? '') ?></td>
<td class="px-5 py-3 font-bold text-slate-800"><?= htmlspecialchars($e['actor'] ?? '') ?><span class="block text-[10px] font-semibold text-slate-400"><?= htmlspecialchars($e['actor_role'] ?? '') ?> · <?= htmlspecialchars($e['category'] ?? '') ?></span></td>
<td class="px-5 py-3 font-mono text-[11px]"><?= htmlspecialchars($e['action'] ?? '') ?></td>
<td class="px-5 py-3 text-slate-500"><?= htmlspecialchars($e['resource'] ?? '') ?></td>
<td class="px-5 py-3"><span class="px-2 py-1 rounded-full text-[10px] font-black border <?= superadmin_sev_class($e['severity'] ?? '') ?>"><?= strtoupper(htmlspecialchars($e['severity'] ?? '')) ?></span></td>
<td class="px-5 py-3 font-mono text-slate-400"><?= htmlspecialchars($e['ip'] ?? '') ?></td>
<td class="px-5 py-3 text-slate-500 max-w-[260px] truncate" title="<?= htmlspecialchars($e['details'] ?? '') ?>"><?= htmlspecialchars($e['details'] ?? '') ?></td>
<td class="px-5 py-3"><span class="audit-state px-2 py-1 rounded-full text-[10px] font-bold border <?= empty($e['is_read'])?'bg-amber-100 text-amber-700 border-amber-200':'bg-emerald-100 text-emerald-700 border-emerald-200' ?>"><?= empty($e['is_read'])?'UNREAD':'READ' ?></span></td>
</tr>
<?php endforeach; ?>
</tbody></table><div id="auditEmpty" class="hidden p-8 text-center text-xs text-slate-400">No events match.</div>
</div></div>
</main>
<script src="../../../assets/js/super_admin/audit_trail.js"></script>
<?php include_once '../../includes/footer.php'; ?>
