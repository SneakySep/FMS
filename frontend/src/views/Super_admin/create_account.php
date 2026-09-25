<?php
$activePage = 'create_account';
$page_title = 'Create Account';
require_once __DIR__ . '/../../helpers/super_admin_helper.php';
superadmin_require_role();
$data = load_superadmin_mock();
$c = superadmin_counts($data);
$users = $data['users'];
usort($users, function($a,$b){ return strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''); });
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">
<?php include_once '../../components/top_header.php'; ?>
<div class="mb-4 flex items-center gap-2 text-[11px] font-bold">
<span class="px-2.5 py-1 rounded-full bg-violet-100 text-violet-700 border border-violet-200">SUPER ADMIN</span>
<span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">DEMO DATA (JSON)</span>
</div>
<?php include_once __DIR__ . '/components/stat_cards.php'; ?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
<div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
<h2 class="text-sm font-black text-slate-900">Create Account</h2>
<p class="text-[11px] text-slate-400 mb-4">Demo: rows are added to the table below only.</p>
<form id="createAccountForm" class="space-y-3 text-xs">
<div class="grid grid-cols-2 gap-3">
<div><label class="font-bold text-slate-700 block mb-1">First Name</label><input id="f_first" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl" placeholder="Juan"></div>
<div><label class="font-bold text-slate-700 block mb-1">Last Name</label><input id="f_last" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl" placeholder="Dela Cruz"></div>
</div>
<div><label class="font-bold text-slate-700 block mb-1">Email</label><input id="f_email" type="email" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl" placeholder="name@priority-ph.com"></div>
<div class="grid grid-cols-2 gap-3">
<div><label class="font-bold text-slate-700 block mb-1">Role</label><select id="f_role" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl"><option value="customer">customer</option><option value="sales_agent">sales_agent</option><option value="admin">admin</option><option value="super_admin">super_admin</option></select></div>
<div><label class="font-bold text-slate-700 block mb-1">Company</label><input id="f_company" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl" placeholder="Optional"></div>
</div>
<div><div class="flex justify-between items-center mb-1"><label class="font-bold text-slate-700">Temp Password</label><button type="button" id="genPass" class="text-[11px] font-bold text-indigo-600 hover:underline">Auto Generate</button></div><input id="f_pass" required minlength="8" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl font-mono text-indigo-700 font-bold" placeholder="Min. 8 chars"></div>
<p id="formMsg" class="hidden text-[11px] font-bold"></p>
<button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs">Create Account</button>
</form></div>

<div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100 flex flex-wrap gap-3 items-center justify-between">
<div><h2 class="text-sm font-black text-slate-900">All Accounts (<?= count($users) ?>)</h2><p class="text-[11px] text-slate-400">From JSON mock.</p></div>
<div class="flex gap-2"><input id="accSearch" placeholder="Search..." class="px-3 py-2 border border-slate-200 rounded-xl text-xs w-56"><select id="accRole" class="px-3 py-2 border border-slate-200 rounded-xl text-xs"><option value="">All roles</option><option value="super_admin">super_admin</option><option value="admin">admin</option><option value="sales_agent">sales_agent</option><option value="customer">customer</option></select></div></div>
<div class="overflow-x-auto"><table class="w-full text-left text-xs">
<thead><tr class="bg-slate-50/70 text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-100"><th class="px-5 py-3">User</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Attempts</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Created</th></tr></thead>
<tbody id="accBody" class="divide-y divide-slate-100">
<?php foreach ($users as $u): ?>
<tr class="acc-row hover:bg-slate-50" data-search="<?= htmlspecialchars(strtolower(($u['first_name']??'').' '.($u['last_name']??'').' '.($u['email']??'').' '.($u['id']??''))) ?>" data-role="<?= htmlspecialchars($u['role'] ?? '') ?>">
<td class="px-5 py-3 font-bold text-slate-800"><?= htmlspecialchars(trim(($u['first_name']??'').' '.($u['last_name']??''))) ?><span class="block text-[10px] font-semibold text-slate-400"><?= htmlspecialchars($u['id'] ?? '') ?></span></td>
<td class="px-5 py-3 text-slate-500"><?= htmlspecialchars($u['email'] ?? '') ?></td>
<td class="px-5 py-3"><span class="px-2 py-1 rounded-full bg-slate-100 border border-slate-200 text-[10px] font-bold"><?= htmlspecialchars($u['role'] ?? '') ?></span></td>
<td class="px-5 py-3 font-mono"><?= (int)($u['login_attempts']??0) ?>/5</td>
<td class="px-5 py-3"><span class="px-2 py-1 rounded-full text-[10px] font-bold border"><?= htmlspecialchars($u['status'] ?? '') ?></span></td>
<td class="px-5 py-3 text-slate-400"><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</tbody></table><div id="accEmpty" class="hidden p-8 text-center text-xs text-slate-400">No accounts match.</div>
</div></div></div></main>
<script src="../../../assets/js/super_admin/create_account.js"></script>
<?php include_once '../../includes/footer.php'; ?>
