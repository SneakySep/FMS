<?php
$sla_exists   = $sla_exists ?? false;
$sla_contract = $sla_contract ?? null;
$sla_success  = $sla_success ?? null;
$sla_error    = $sla_error ?? null;
$inquiry_id   = trim($_GET['inquiry_id'] ?? '');

$has_lookup = ($inquiry_id !== '');
$has_result = ($has_lookup && $sla_exists && is_array($sla_contract));

/**
 * Status badge styles para sa SLA contract
 * (kapareho ng getLeadStatusBadge() pattern sa leads.php).
 */
if (!function_exists('getSlaStatusBadge')) {
    function getSlaStatusBadge($status)
    {
        switch (strtolower(trim((string) $status))) {
            case 'active':
                return 'bg-emerald-100 text-emerald-700 border-emerald-200';
            case 'pending':
                return 'bg-amber-100 text-amber-700 border-amber-200';
            case 'expired':
                return 'bg-rose-100 text-rose-700 border-rose-200';
            case 'cancelled':
            case 'canceled':
                return 'bg-slate-100 text-slate-700 border-slate-200';
            default:
                return 'bg-blue-100 text-blue-700 border-blue-200';
        }
    }
}

$created_label = '—';
if ($has_result && !empty($sla_contract['created_at'])) {
    $created_label = date('d M Y', strtotime($sla_contract['created_at']));
}
?>

<!-- SLA CHECK TABLE CARD -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" id="slaCheckCard">

    <!-- CARD HEADER -->
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">SLA Contract Lookup</h3>
            <p class="text-xs text-slate-500">I-type ang Inquiry ID para i-check kung may existing na SLA contract</p>
        </div>

        <!-- LOOKUP FORM (targets GET /api/sales/sla/check/{inquiry_id}) -->
        <form id="slaLookupForm" method="GET" action="sla_contracts.php" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="text" id="slaInquiryInput" name="inquiry_id" value="<?php echo htmlspecialchars($inquiry_id); ?>" placeholder="Inquiry ID..."
                   class="flex-1 sm:w-64 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" />
            <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                <span>Check</span>
            </button>
        </form>
    </div>

    <?php if (!empty($sla_success)): ?>
        <!-- SUCCESS BANNER -->
        <div class="m-5 mb-0 flex items-start gap-2.5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <span><?php echo htmlspecialchars($sla_success); ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($sla_error)): ?>
        <!-- ERROR BANNER -->
        <div class="m-5 mb-0 flex items-start gap-2.5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <span><?php echo htmlspecialchars($sla_error); ?></span>
        </div>
    <?php endif; ?>

    <!-- INFO / EMPTY STATE (server-side, JS-updatable) -->
    <div id="slaEmptyState" class="<?php echo $has_result ? 'hidden' : ''; ?> p-10 text-center">
        <?php if ($has_lookup && !$sla_exists): ?>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-file-circle-xmark text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Walang SLA Contract na nahanap</h4>
            <p class="text-xs text-slate-500 mt-1">Walang existing na SLA contract para sa Inquiry ID <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($inquiry_id); ?></span>.</p>
        <?php else: ?>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-file-shield text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Mag-check ng SLA Contract</h4>
            <p class="text-xs text-slate-500 mt-1">I-type ang Inquiry ID sa itaas at pindutin ang "Check" para makita ang detalye.</p>
        <?php endif; ?>
    </div>

    <!-- RESULT TABLE -->
    <div id="slaTableWrap" class="<?php echo $has_result ? '' : 'hidden'; ?> overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                <tr>
                    <th class="py-3 px-4">Contract No</th>
                    <th class="py-3 px-4">Inquiry ID</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Created</th>
                    <th class="py-3 px-4">PDF</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="slaTableBody" class="divide-y divide-slate-100 text-sm">
                <?php if ($has_result): $status_raw = $sla_contract['status'] ?? '—'; ?>
                    <tr>
                        <td class="py-3 px-4 font-semibold text-slate-800"><?php echo htmlspecialchars($sla_contract['contract_no'] ?? '—'); ?></td>
                        <td class="py-3 px-4 text-slate-600"><?php echo htmlspecialchars($inquiry_id); ?></td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-[11px] font-semibold capitalize <?php echo getSlaStatusBadge($status_raw); ?>">
                                <?php echo htmlspecialchars($status_raw); ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-600"><?php echo htmlspecialchars($created_label); ?></td>
                        <td class="py-3 px-4 text-slate-600 max-w-[220px]">
                            <?php if (!empty($sla_contract['pdf_path'])): ?>
                                <span class="inline-flex items-center gap-1.5 max-w-[220px] truncate" title="<?php echo htmlspecialchars($sla_contract['pdf_path']); ?>">
                                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                    <span class="truncate"><?php echo htmlspecialchars(basename($sla_contract['pdf_path'])); ?></span>
                                </span>
                            <?php else: ?>
                                <span class="text-slate-400 italic">Not generated</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if (!empty($sla_contract['pdf_path'])): ?>
                                <button type="button" data-copy-pdf="<?php echo htmlspecialchars($sla_contract['pdf_path']); ?>" onclick="copySlaPdfPath(this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition">
                                    <i class="fa-regular fa-copy"></i> Copy Path
                                </button>
                            <?php else: ?>
                                <span class="text-slate-300">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>