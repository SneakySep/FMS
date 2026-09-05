<?php
$page_title = "Kanban Pipeline · PRIORITY HANDLING";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Fetch lahat ng leads mula sa FastAPI
$leads_res = make_api_request('/api/v1/leads/?limit=100&include_closed=true', 'GET');

// Robust check para sa iba't ibang wrapper ng API response
$all_leads = [];
if (isset($leads_res['data']['data']) && is_array($leads_res['data']['data'])) {
    $all_leads = $leads_res['data']['data'];
} elseif (isset($leads_res['data']) && is_array($leads_res['data'])) {
    $all_leads = $leads_res['data'];
} elseif (is_array($leads_res)) {
    $all_leads = $leads_res;
}

// 2. I-group ang leads ayon sa Sales Pipeline Stage
$columns = [
    'new_inquiry' => ['title' => 'NEW INQUIRY', 'items' => []],
    'qualifying'  => ['title' => 'QUALIFYING',  'items' => []],
    'quote_sent'  => ['title' => 'QUOTE SENT',  'items' => []],
    'negotiation' => ['title' => 'NEGOTIATION', 'items' => []],
    'closed_won'  => ['title' => 'WON',         'items' => []],
];

// Per-stage accent palette (mirrors my_leads.php status badges)
$stage_accent = [
    'new_inquiry' => ['bar' => 'bg-purple-500',  'chip' => 'bg-purple-100 text-purple-700',  'dot' => 'bg-purple-500',  'soft' => 'bg-purple-50',  'icon' => 'fa-lightbulb',          'total' => false],
    'qualifying'  => ['bar' => 'bg-amber-500',   'chip' => 'bg-amber-100 text-amber-700',   'dot' => 'bg-amber-500',   'soft' => 'bg-amber-50',   'icon' => 'fa-magnifying-glass',   'total' => false],
    'quote_sent'  => ['bar' => 'bg-blue-500',    'chip' => 'bg-blue-100 text-blue-700',     'dot' => 'bg-blue-500',    'soft' => 'bg-blue-50',    'icon' => 'fa-file-invoice-dollar','total' => false],
    'negotiation' => ['bar' => 'bg-indigo-500',  'chip' => 'bg-indigo-100 text-indigo-700', 'dot' => 'bg-indigo-500',  'soft' => 'bg-indigo-50',  'icon' => 'fa-handshake',          'total' => false],
    'closed_won'  => ['bar' => 'bg-emerald-500', 'chip' => 'bg-emerald-100 text-emerald-700','dot' => 'bg-emerald-500','soft' => 'bg-emerald-50', 'icon' => 'fa-trophy',             'total' => true],
];

foreach ($all_leads as $lead) {
    // Kunin ang status at i-clean
    $st = strtolower(trim($lead['status'] ?? 'new_inquiry'));

    // Handle variations ng status string mula sa DB
    if ($st === 'won' || $st === 'closed won' || $st === 'closed_won') {
        $st = 'closed_won';
    }

    if (isset($columns[$st])) {
        $columns[$st]['items'][] = $lead;
    } else {
        $columns['new_inquiry']['items'][] = $lead;
    }
}

// 3. Compute Dashboard KPIs (mirrors kpi_cards.php aesthetic)
$total_leads     = count($all_leads);
$pipeline_value  = 0.0; // open stages (exclude won)
$won_value       = 0.0;
$won_count       = 0;
$open_count      = 0;

foreach ($all_leads as $lead) {
    $amount = (float)($lead['estimated_amount'] ?? 0);
    $st = strtolower(trim($lead['status'] ?? 'new_inquiry'));
    if ($st === 'won' || $st === 'closed won' || $st === 'closed_won') {
        $won_value += $amount;
        $won_count++;
    } else {
        $pipeline_value += $amount;
        $open_count++;
    }
}

$win_rate = ($total_leads > 0)
    ? round(($won_count / $total_leads) * 100)
    : 0;

function kanbanMoney($value) {
    if ($value >= 1000000) {
        return '₱' . number_format($value / 1000000, 2) . 'M';
    } elseif ($value >= 1000) {
        return '₱' . number_format($value / 1000, 1) . 'K';
    }
    return '₱' . number_format($value, 2);
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC]">

  <?php 
  $header_title = "Kanban Pipeline";
  $header_subtitle = "Drag and drop leads between stages — changes sync live.";
  include_once 'components/dashboard_header.php'; 
  ?>
  
  <div class="p-6 lg:p-8">
    <!-- PAGE TITLE ROW -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kanban Pipeline</h1>
      <p class="text-sm text-slate-500 mt-1">Drag and drop leads between stages — changes sync live across My Leads & Kanban.</p>
    </div>

    <!-- LOCAL QUICK SEARCH -->
    <div class="relative">
      <input
        type="text"
        id="kanbanSearch"
        placeholder="Search leads..."
        onkeyup="filterKanbanCards()"
        class="w-full sm:w-72 pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 shadow-sm transition"
      >
      <i class="fa-solid fa-magnifying-glass text-xs text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
    </div>
  </div>


  <!-- ROW 1: DASHBOARD KPI CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">

    <!-- Total Leads -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-bold text-slate-700">Total Leads</span>
        <div class="p-1.5 rounded-lg bg-purple-100 text-purple-600">
          <i class="fa-solid fa-user-group text-xs"></i>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)$total_leads) ?></p>
        <p class="text-[11px] text-slate-500 font-semibold mt-2 flex items-center gap-1">
          <i class="fa-solid fa-layer-group text-purple-400"></i>
          <span>Across <?= count($columns) ?> pipeline stages</span>
        </p>
      </div>
    </div>

    <!-- Pipeline Value (Open) -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-bold text-slate-700">Open Pipeline Value</span>
        <div class="p-1.5 rounded-lg bg-blue-100 text-blue-600">
          <i class="fa-solid fa-chart-line text-xs"></i>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= kanbanMoney($pipeline_value) ?></p>
        <p class="text-[11px] text-slate-500 font-semibold mt-2 flex items-center gap-1">
          <i class="fa-solid fa-arrow-trend-up text-blue-400"></i>
          <span><?= $open_count ?> active opportunities</span>
        </p>
      </div>
    </div>

    <!-- Won Value -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-bold text-slate-700">Won Value</span>
        <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600">
          <i class="fa-solid fa-trophy text-xs"></i>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= kanbanMoney($won_value) ?></p>
        <p class="text-[11px] text-emerald-600 font-semibold mt-2 flex items-center gap-1">
          <i class="fa-solid fa-check-circle text-emerald-500"></i>
          <span><?= $won_count ?> deals closed</span>
        </p>
      </div>
    </div>

    <!-- Win Rate -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
      <div class="flex justify-between items-start">
        <span class="text-xs font-bold text-slate-700">Win Rate</span>
        <div class="p-1.5 rounded-lg bg-indigo-100 text-indigo-600">
          <i class="fa-solid fa-bullseye text-xs"></i>
        </div>
      </div>
      <div class="mt-3">
        <p class="text-3xl font-extrabold text-slate-900"><?= htmlspecialchars((string)$win_rate) ?>%</p>
        <div class="mt-2 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full" style="width:<?= $win_rate ?>%"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- ROW 2: KANBAN BOARD (responsive grid that fills full width) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-start">

    <?php foreach ($columns as $stage_key => $column):
        $acc = $stage_accent[$stage_key];
        $count = count($column['items']);
    ?>
      <div
        class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col min-h-[72vh] overflow-hidden"
        ondragover="allowDrop(event)"
        ondrop="dropLead(event, '<?= $stage_key ?>')"
        data-stage="<?= $stage_key ?>"
      >
        <!-- Accent top bar -->
        <div class="h-1.5 w-full <?= $acc['bar'] ?>"></div>

        <!-- COLUMN HEADER -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
          <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-lg <?= $acc['soft'] ?> <?= $acc['chip'] ?> flex items-center justify-center shrink-0">
              <i class="fa-solid <?= $acc['icon'] ?> text-[11px]"></i>
            </span>
            <h2 class="text-[11px] font-bold text-slate-600 tracking-wider uppercase truncate">
              <?= $column['title'] ?>
            </h2>
          </div>
          <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full shrink-0">
            <?= $count ?>
          </span>
        </div>

        <!-- CARDS CONTAINER -->
        <div class="p-3 space-y-3 flex-1 overflow-y-auto kanban-column-body">

          <?php if ($count === 0): ?>
            <div class="p-6 border-2 border-dashed border-slate-200 rounded-xl text-center">
              <i class="fa-regular fa-folder-open text-slate-300 text-xl mb-2 block"></i>
              <p class="text-xs text-slate-400">No leads in this stage</p>
            </div>
          <?php else: ?>
            <?php foreach ($column['items'] as $lead):
                $amount = (float)($lead['estimated_amount'] ?? 0);
                $company = htmlspecialchars($lead['company_name'] ?? 'Unassigned Company');
                $contact = htmlspecialchars($lead['contact_person'] ?? 'No contact person');
                $source  = htmlspecialchars($lead['source'] ?? '');
                $created = $lead['created_at'] ?? '';
                $created_label = $created ? date('M d, Y', strtotime($created)) : '';
                $hover_border = explode('-', $acc['chip'])[0] . '-300';
            ?>
              <!-- KANBAN CARD -->
              <div
                draggable="true"
                ondragstart="dragLead(event, '<?= $lead['id'] ?>')"
                id="lead-card-<?= $lead['id'] ?>"
                class="kanban-card group bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-<?= $hover_border ?> transition cursor-grab active:cursor-grabbing relative"
              >
                <!-- Accent dot + company -->
                <div class="flex items-start gap-2.5">
                  <span class="mt-1.5 w-2 h-2 rounded-full <?= $acc['dot'] ?> shrink-0"></span>
                  <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-indigo-600 transition truncate">
                      <?= $company ?>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 truncate">
                      <?= $contact ?>
                    </p>
                  </div>
                </div>

                <!-- Meta row -->
                <div class="mt-3 flex items-center gap-3 text-[11px] text-slate-400">
                  <?php if ($source): ?>
                    <span class="inline-flex items-center gap-1">
                      <i class="fa-solid fa-location-arrow text-slate-300"></i><?= $source ?>
                    </span>
                  <?php endif; ?>
                  <?php if ($created_label): ?>
                    <span class="inline-flex items-center gap-1">
                      <i class="fa-regular fa-calendar text-slate-300"></i><?= $created_label ?>
                    </span>
                  <?php endif; ?>
                </div>

                <!-- ESTIMATED PRICE BADGE -->
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                  <div class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                    <i class="fa-solid fa-peso-sign text-[10px]"></i>
                    <span>₱<?= number_format($amount, 2) ?></span>
                  </div>
                  <span class="text-[10px] text-slate-300 opacity-0 group-hover:opacity-100 transition">
                    <i class="fa-solid fa-up-down-left-right"></i>
                  </span>
                </div>

              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>

      </div>
    <?php endforeach; ?>

  </div>

</main>

<!-- JAVASCRIPT FOR REAL-TIME SYNC & DRAG & DROP -->
<script src="../../../assets/js/sales_agent/kanban.js"></script>

<?php include_once 'components/alert.php'; ?>
<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

