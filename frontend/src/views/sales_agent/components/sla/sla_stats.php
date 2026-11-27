<?php
$sla_exists   = $sla_exists ?? false;
$sla_contract = $sla_contract ?? null;
$inquiry_id   = trim($_GET['inquiry_id'] ?? '');

$has_lookup  = ($inquiry_id !== '');
$contract_no = is_array($sla_contract) ? ($sla_contract['contract_no'] ?? '—') : '—';
$status      = is_array($sla_contract) ? ($sla_contract['status'] ?? '—') : '—';
$pdf_path    = is_array($sla_contract) ? ($sla_contract['pdf_path'] ?? '') : '';

if (!$has_lookup) {
    $result_label = '—';
    $result_class = 'text-slate-900';
    $result_hint  = 'Maglagay ng Inquiry ID';
} elseif ($sla_exists) {
    $result_label = 'Found';
    $result_class = 'text-emerald-600';
    $result_hint  = 'May existing SLA contract';
} else {
    $result_label = 'Not Found';
    $result_class = 'text-rose-600';
    $result_hint  = 'Walang SLA contract';
}
?>
<!-- SLA STAT STRIP (dashboard card style) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <!-- 1. Lookup Result -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Lookup Result</span>
            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        <h3 id="stat-result" class="text-xl font-extrabold <?php echo $result_class; ?>"><?php echo htmlspecialchars($result_label); ?></h3>
        <p class="text-[11px] text-slate-500 mt-1"><?php echo htmlspecialchars($result_hint); ?></p>
    </div>

    <!-- 2. Contract No -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Contract No</span>
            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-file-contract"></i>
            </div>
        </div>
        <h3 id="stat-contract-no" class="text-lg font-extrabold text-slate-900 truncate"><?php echo htmlspecialchars($contract_no === '' ? '—' : $contract_no); ?></h3>
        <p class="text-[11px] text-slate-500 mt-1">SLA reference</p>
    </div>

    <!-- 3. Status -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</span>
            <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <h3 id="stat-status" class="text-xl font-extrabold text-slate-900 capitalize"><?php echo htmlspecialchars($status === '' ? '—' : $status); ?></h3>
        <p class="text-[11px] text-slate-500 mt-1">Current state</p>
    </div>

    <!-- 4. PDF Ready -->
    <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">PDF Ready</span>
            <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xs">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
        </div>
        <h3 id="stat-pdf" class="text-xl font-extrabold <?php echo !empty($pdf_path) ? 'text-emerald-600' : 'text-slate-900'; ?>"><?php echo !empty($pdf_path) ? 'Yes' : '—'; ?></h3>
        <p class="text-[11px] text-slate-500 mt-1">Generated document</p>
    </div>

</div>