<?php
$leads_list  = $leads_list ?? [];
$leads_error = $leads_error ?? null;
$page        = $page ?? 1;
$limit       = $limit ?? 10;
$has_prev    = $has_prev ?? false;
$has_next    = $has_next ?? false;

/**
 * Render a field value, o isang muted na "None" kung walang laman.
 * Itinuturing ding walang laman ang mga placeholder tulad ng "string"/"null"/"N/A".
 */
if (!function_exists('noneText')) {
    function noneText($value)
    {
        $s = is_scalar($value) ? trim((string) $value) : '';
        $placeholders = ['', 'string', 'null', 'undefined', 'n/a', 'none'];

        if (in_array(strtolower($s), $placeholders, true)) {
            return '<span class="text-slate-400 italic">None</span>';
        }

        return htmlspecialchars($s);
    }
}
?>

<!-- LEADS TABLE CARD -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    <!-- CARD HEADER -->
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Inquiry Leads Directory</h3>
            <p class="text-xs text-slate-500">Manage status updates and customer inquiries</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/80 w-fit">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span><?= count($leads_list) ?> shown on page <?= (int) $page ?></span>
        </div>
    </div>

    <?php if (!empty($leads_error)): ?>
        <!-- ERROR STATE -->
        <div class="p-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800">Couldn't load leads</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto"><?= htmlspecialchars($leads_error) ?></p>
            <a href="my_leads.php" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                <i class="fa-solid fa-rotate-right text-[11px]"></i> Retry
            </a>
        </div>

    <?php elseif (empty($leads_list)): ?>
        <!-- EMPTY STATE -->
        <div class="p-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-inbox text-lg"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-800">No inquiries found</h4>
            <p class="text-xs text-slate-500 mt-1">Try changing the status filter or search keyword.</p>
            <button type="button" onclick="openLeadModal()" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition active:scale-95">
                <i class="fa-solid fa-plus text-[11px]"></i> Add New Lead
            </button>
        </div>

    <?php else: ?>

        <!-- DESKTOP TABLE -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 z-10 bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Inquiry Code</th>
                        <th class="py-3 px-4">Company &amp; Contact</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Service Type</th>
                        <th class="py-3 px-4">Cargo Details</th>
                        <th class="py-3 px-4">Handling</th>
                        <th class="py-3 px-4">Estimated Price</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Age</th>
                        <th class="py-3 px-4">Date Submitted</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($leads_list as $lead):
                        $leadJson = htmlspecialchars(json_encode($lead, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

                        $statusRaw   = $lead['status'] ?? 'new_inquiry';
                        $statusClean = strtolower(trim($statusRaw));
                        $isClosed    = in_array($statusClean, ['closed_won', 'closed_lost']);

                        $createdAt = !empty($lead['created_at']) ? strtotime($lead['created_at']) : null;
                        $daysOld   = $createdAt ? (int) floor((time() - $createdAt) / 86400) : null;

                        $emailRaw  = $lead['email'] ?? '';
                        $phoneRaw  = $lead['phone_number'] ?? $lead['phone'] ?? '';
                        $cargoRaw  = $lead['cargo_details'] ?? $lead['initial_inquiry_text'] ?? '';
                        $amountRaw = $lead['estimated_amount'] ?? $lead['estimated_price'] ?? null;
                        $hasAmount = ($amountRaw !== null && $amountRaw !== '' && (float) $amountRaw > 0);

                        // Age / SLA chip
                        if ($isClosed) {
                            $ageChip = '<span class="text-[11px] font-semibold text-slate-400">Closed</span>';
                        } elseif ($daysOld === null) {
                            $ageChip = '<span class="text-[11px] font-semibold text-slate-400 italic">None</span>';
                        } elseif ($daysOld <= 1) {
                            $ageChip = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap bg-emerald-100 text-emerald-700 border border-emerald-200">Fresh</span>';
                        } elseif ($daysOld <= 3) {
                            $ageChip = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap bg-amber-100 text-amber-700 border border-amber-200">' . $daysOld . 'd</span>';
                        } else {
                            $ageChip = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap bg-rose-100 text-rose-700 border border-rose-200">' . $daysOld . 'd overdue</span>';
                        }
                    ?>
                        <tr class="hover:bg-slate-50/80 transition border-l-2 border-transparent hover:border-indigo-400">

                            <!-- INQUIRY CODE -->
                            <td class="py-4 px-4 font-bold text-purple-600 align-top whitespace-nowrap">
                                <?= htmlspecialchars($lead['inquiry_code'] ?? 'INQ-' . substr($lead['id'] ?? '', 0, 8)) ?>
                            </td>

                            <!-- COMPANY & CONTACT -->
                            <td class="py-4 px-4 align-top">
                                <div class="font-bold text-slate-800"><?= noneText($lead['company_name'] ?? null) ?></div>
                                <div class="text-xs text-slate-500"><?= noneText($lead['contact_person'] ?? null) ?></div>
                            </td>

                            <!-- EMAIL -->
                            <td class="py-4 px-4 text-slate-600 align-top"><?= noneText($emailRaw) ?></td>

                            <!-- PHONE -->
                            <td class="py-4 px-4 text-slate-600 align-top whitespace-nowrap"><?= noneText($phoneRaw) ?></td>

                            <!-- SERVICE TYPE + ROUTE -->
                            <td class="py-4 px-4 text-slate-600 font-medium align-top">
                                <?= noneText($lead['service_type'] ?? null) ?>
                                <?php if (!empty($lead['origin']) && !empty($lead['destination'])): ?>
                                    <div class="text-xs text-slate-400"><?= htmlspecialchars($lead['origin']) ?> &#10132; <?= htmlspecialchars($lead['destination']) ?></div>
                                <?php endif; ?>
                            </td>

                            <!-- CARGO DETAILS -->
                            <td class="py-4 px-4 text-slate-600 align-top max-w-[240px]">
                                <div class="line-clamp-2" title="<?= htmlspecialchars((string) $cargoRaw) ?>"><?= noneText($cargoRaw) ?></div>
                            </td>

                            <!-- HANDLING -->
                            <td class="py-4 px-4 text-slate-600 font-medium align-top"><?= noneText($lead['handling'] ?? null) ?></td>

                            <!-- ESTIMATED PRICE -->
                            <td class="py-4 px-4 font-bold text-slate-800 align-top whitespace-nowrap">
                                <?= $hasAmount ? '&#8369;' . number_format((float) $amountRaw, 2) : '<span class="text-slate-400 italic font-normal">None</span>' ?>
                            </td>

                            <!-- STATUS -->
                            <td class="py-4 px-4 align-top">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getLeadStatusBadge($statusRaw) ?>">
                                    <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $statusRaw))) ?>
                                </span>
                            </td>

                            <!-- AGE / SLA -->
                            <td class="py-4 px-4 align-top whitespace-nowrap"><?= $ageChip ?></td>

                            <!-- DATE SUBMITTED -->
                            <td class="py-4 px-4 text-xs text-slate-500 align-top whitespace-nowrap">
                                <?= $createdAt ? date('M d, Y • h:i A', $createdAt) : '<span class="text-slate-400 italic">None</span>' ?>
                            </td>

                            <!-- ACTIONS -->
                            <td class="py-4 px-4 align-top">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick='openViewModal(<?= $leadJson ?>)'
                                            title="View &amp; Manage"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold rounded-xl text-xs border border-indigo-100 transition active:scale-95 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-eye text-[11px]"></i> View
                                    </button>
                                    <button type="button" onclick='openQuotationModal(<?= $leadJson ?>)'
                                            title="Send Quote"
                                            class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition active:scale-95 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-file-invoice-dollar text-[11px]"></i>
                                    </button>
                                    <button type="button" onclick='openContactModal(<?= $leadJson ?>)'
                                            title="Email / Call"
                                            class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-bold transition active:scale-95 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-address-book text-[11px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS -->
        <div class="md:hidden divide-y divide-slate-100">
            <?php foreach ($leads_list as $lead):
                $leadJson     = htmlspecialchars(json_encode($lead, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $statusRaw    = $lead['status'] ?? 'new_inquiry';
                $statusClean  = strtolower(trim($statusRaw));
                $isClosed     = in_array($statusClean, ['closed_won', 'closed_lost']);
                $createdAt    = !empty($lead['created_at']) ? strtotime($lead['created_at']) : null;
                $daysOld      = $createdAt ? (int) floor((time() - $createdAt) / 86400) : null;

                $emailRaw     = $lead['email'] ?? '';
                $phoneRaw     = $lead['phone_number'] ?? $lead['phone'] ?? '';
                $cargoRaw     = $lead['cargo_details'] ?? $lead['initial_inquiry_text'] ?? '';
                $amountRaw    = $lead['estimated_amount'] ?? $lead['estimated_price'] ?? null;
                $hasAmount    = ($amountRaw !== null && $amountRaw !== '' && (float) $amountRaw > 0);
            ?>
                <div class="p-4 active:bg-slate-50 transition cursor-pointer" onclick='openViewModal(<?= $leadJson ?>)'>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-800 truncate"><?= noneText($lead['company_name'] ?? null) ?></div>
                            <div class="text-xs text-slate-500 truncate"><?= noneText($lead['contact_person'] ?? null) ?></div>
                        </div>
                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[10px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getLeadStatusBadge($statusRaw) ?>">
                            <?= htmlspecialchars(strtoupper(str_replace('_', ' ', $statusRaw))) ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between mt-3 text-xs">
                        <span class="font-mono font-bold text-purple-600"><?= htmlspecialchars($lead['inquiry_code'] ?? 'INQ') ?></span>
                        <span class="font-bold text-slate-800">
                            <?= $hasAmount ? '&#8369;' . number_format((float) $amountRaw, 2) : '<span class="text-slate-400 italic font-normal">None</span>' ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between mt-2 text-[11px] text-slate-400">
                        <span><?= noneText($lead['service_type'] ?? null) ?></span>
                        <span>
                            <?php if ($isClosed): ?>Closed
                            <?php elseif ($daysOld === null): ?><span class="italic">None</span>
                            <?php elseif ($daysOld <= 1): ?><span class="text-emerald-600 font-bold">Fresh</span>
                            <?php elseif ($daysOld <= 3): ?><span class="text-amber-600 font-bold"><?= $daysOld ?>d</span>
                            <?php else: ?><span class="text-rose-600 font-bold whitespace-nowrap"><?= $daysOld ?>d overdue</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- EMAIL / PHONE / CARGO -->
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-500 border-t border-slate-100 pt-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-slate-400 w-3.5 text-center"></i>
                            <span class="truncate"><?= noneText($emailRaw) ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-400 w-3.5 text-center"></i>
                            <span class="truncate"><?= noneText($phoneRaw) ?></span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-box-open text-slate-400 w-3.5 text-center mt-0.5"></i>
                            <span class="line-clamp-2"><?= noneText($cargoRaw) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- PAGINATION FOOTER -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <div>Page <strong><?= (int) $page ?></strong></div>
            <div class="flex items-center gap-2">
                <?php if ($has_prev): ?>
                    <a href="<?= htmlspecialchars(leadsFilterUrl(['page' => $page - 1])) ?>"
                       class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-lg font-semibold hover:bg-slate-50 transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Previous
                    </a>
                <?php else: ?>
                    <span class="px-3.5 py-1.5 bg-slate-50 border border-slate-100 rounded-lg font-semibold text-slate-300 cursor-not-allowed inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Previous
                    </span>
                <?php endif; ?>

                <?php if ($has_next): ?>
                    <a href="<?= htmlspecialchars(leadsFilterUrl(['page' => $page + 1])) ?>"
                       class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-lg font-semibold hover:bg-slate-50 transition inline-flex items-center gap-1.5">
                        Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                <?php else: ?>
                    <span class="px-3.5 py-1.5 bg-slate-50 border border-slate-100 rounded-lg font-semibold text-slate-300 cursor-not-allowed inline-flex items-center gap-1.5">
                        Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
</div>
