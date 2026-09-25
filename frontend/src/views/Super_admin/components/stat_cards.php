<?php
$c = $c ?? ['total_users'=>0,'restricted_count'=>0,'audit_unread'=>0,'by_role'=>[]];
$cards = [
 ['label'=>'Total Users','value'=>$c['total_users'],'sub'=>'All roles combined','icon'=>'fa-users','bg'=>'bg-blue-600','link'=>'create_account.php','link_label'=>'Manage accounts'],
 ['label'=>'Restricted Users','value'=>$c['restricted_count'],'sub'=>'Max login attempts (5/5)','icon'=>'fa-user-lock','bg'=>'bg-rose-600','link'=>'restricted_accounts.php','link_label'=>'View restricted'],
 ['label'=>'Audit Notifications','value'=>$c['audit_unread'],'sub'=>'Unread high/critical SIEM events','icon'=>'fa-bell','bg'=>'bg-amber-500','link'=>'audit_trail.php','link_label'=>'Open audit trail'],
];
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<?php foreach ($cards as $card): ?>
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex items-start gap-4">
<div class="w-11 h-11 <?= $card['bg'] ?> text-white rounded-xl flex items-center justify-center text-base shadow"><i class="fa-solid <?= $card['icon'] ?>"></i></div>
<div class="flex-1"><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400"><?= $card['label'] ?></p>
<p class="text-3xl font-black text-slate-900 leading-none mt-1"><span class="counter" data-target="<?= (int)$card['value'] ?>">0</span></p>
<p class="text-[11px] text-slate-400 mt-1"><?= htmlspecialchars($card['sub']) ?></p>
<a href="<?= $card['link'] ?>" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 hover:underline mt-2"><?= $card['link_label'] ?> <i class="fa-solid fa-arrow-right text-[9px]"></i></a></div>
</div>
<?php endforeach; ?>
</div>
