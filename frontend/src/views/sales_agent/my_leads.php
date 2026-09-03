<?php
$pageTitle  = "My Leads";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Parameters mula sa URL Filter & Pagination
$current_status = $_GET['status'] ?? 'all';
$search_query   = $_GET['search'] ?? '';
$page           = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit          = 10;

// 2. Fetch Live Stats mula sa FastAPI 
$stats_res  = make_api_request('/api/v1/leads/stats', 'GET');
$stats_data = $stats_res['data'] ?? [];

$count_all         = (int)($stats_data['all'] ?? 0);
$count_new         = (int)($stats_data['new_inquiry'] ?? 0);
$count_qualifying  = (int)($stats_data['qualifying'] ?? 0);
$count_quote_sent  = (int)($stats_data['quote_sent'] ?? 0);
$count_negotiation = (int)($stats_data['negotiation'] ?? 0);

// 3. Build API Endpoint Query String para sa Leads Table
$api_url = "/api/v1/leads/?page={$page}&limit={$limit}";

if (!empty($current_status) && $current_status !== 'all') {
    $api_url .= "&status=" . urlencode($current_status);
}

if (!empty($search_query)) {
    $api_url .= "&search=" . urlencode($search_query);
}

// Fetch Leads Data
$leads_res  = make_api_request($api_url, 'GET');
$leads_list = $leads_res['data']['data'] ?? [];
$total_rows = $leads_res['data']['total'] ?? 0;
$total_pages = ceil($total_rows / $limit);

// Helper function para sa Status Badge Styles
function getLeadStatusBadge($status) {
    switch (strtolower(trim($status))) {
        case 'new_inquiry':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'qualifying':
            return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'quote_sent':
            return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'negotiation':
            return 'bg-indigo-100 text-indigo-700 border-indigo-200';
        case 'closed_won':
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'closed_lost':
            return 'bg-rose-100 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC]">

  <?php 
  $header_title = "My Leads";
  $header_subtitle = "Manage your active leads and pipeline stages.";
  $header_actions = '<button type="button" onclick="openLeadModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-sm hover:shadow-indigo-200 flex items-center gap-2"><i class="fa-solid fa-plus text-[10px]"></i><span>New Lead</span></button>';
  include_once 'components/dashboard_header.php'; 
  ?>

  <div class="p-6 lg:p-8">

    <!-- STATUS FILTER TABS -->
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    <a href="?status=all<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      All (<?= $count_all ?>)
    </a>
    <a href="?status=new_inquiry<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'new_inquiry' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      New (<?= $count_new ?>)
    </a>
    <a href="?status=qualifying<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'qualifying' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Qualifying (<?= $count_qualifying ?>)
    </a>
    <a href="?status=quote_sent<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'quote_sent' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Quote Sent (<?= $count_quote_sent ?>)
    </a>
    <a href="?status=negotiation<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_status === 'negotiation' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Negotiation (<?= $count_negotiation ?>)
    </a>
  </div>

  <!-- LEADS TABLE CARD -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm shadow-slate-200/50 overflow-hidden">
    
    <!-- CARD HEADER -->
    <div class="relative overflow-hidden p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-violet-50/80 via-indigo-50/60 to-white">
      <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-violet-200/30 blur-xl"></div>
      <div class="relative">
        <div class="flex items-center gap-2.5">
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 text-white shadow-md shadow-indigo-500/30">
            <i class="fa-solid fa-inbox text-sm"></i>
          </div>
          <div>
            <h2 class="text-base font-bold text-slate-800 leading-tight">Inquiry Leads Directory</h2>
            <p class="text-xs text-slate-400">Manage status updates and customer inquiries</p>
          </div>
        </div>
      </div>
      <button class="relative text-sm font-semibold text-purple-600 hover:text-purple-700 hover:bg-white px-3 py-1.5 rounded-xl border border-purple-100 bg-white/70 transition flex items-center gap-1.5 shadow-sm">
        <i class="fa-solid fa-file-export text-xs"></i> Export CSV
      </button>
    </div>

    <!-- MAIN TABLE -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
            <th class="py-3.5 px-4">Inquiry Code</th>
            <th class="py-3.5 px-4">Company & Contact</th>
            <th class="py-3.5 px-4">Service Type</th>
            <th class="py-3.5 px-4">Estimated Price</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4">Date Submitted</th>
            <th class="py-3.5 px-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
          <?php if (empty($leads_list)): ?>
            <tr>
              <td colspan="7" class="py-14 text-center">
                <div class="flex flex-col items-center justify-center gap-3 text-slate-400">
                  <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                    <i class="fa-regular fa-folder-open text-2xl"></i>
                  </div>
                  <p class="text-sm font-medium">No inquiries found.</p>
                  <p class="text-xs text-slate-300">New leads will appear here once submitted.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($leads_list as $lead): ?>
              <tr class="hover:bg-slate-50/70 transition-all hover:shadow-[inset_0_0_0_1px_rgba(124,58,237,0.08)]">
                
                <!-- 1. INQUIRY CODE / ID -->
                <td class="py-4 px-4">
                  <span class="inline-block rounded-lg bg-violet-50 px-2.5 py-1 font-bold text-[13px] text-violet-600 ring-1 ring-violet-100">
                    <?= htmlspecialchars($lead['inquiry_code'] ?? 'INQ-'.substr($lead['id'], 0, 8)) ?>
                  </span>
                </td>

                <!-- 2. COMPANY NAME & CONTACT PERSON -->
                <td class="py-4 px-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 text-[11px] font-extrabold uppercase text-slate-500 ring-1 ring-slate-200">
                      <?php
                        $cn = trim((string)($lead['company_name'] ?? ''));
                        $initials = '?';
                        if ($cn !== '') {
                          $parts = array_filter(explode(' ', $cn));
                          $initials = '';
                          foreach ($parts as $w) {
                            $initials .= mb_substr($w, 0, 1);
                            if (strlen($initials) >= 2) break;
                          }
                          $initials = strtoupper($initials);
                        }
                        echo htmlspecialchars($initials);
                      ?>
                    </div>
                    <div>
                      <div class="font-bold text-slate-800 leading-tight">
                        <?= htmlspecialchars($lead['company_name'] ?? 'N/A') ?>
                      </div>
                      <div class="text-xs text-slate-500">
                         <?= htmlspecialchars($lead['contact_person'] ?? 'No Contact Person') ?>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- 3. SERVICE TYPE -->
                <td class="py-4 px-4 text-slate-600 font-medium">
                  <?= htmlspecialchars($lead['service_type'] ?? 'General Freight') ?>
                  <?php if (!empty($lead['origin']) && !empty($lead['destination'])): ?>
                    <div class="text-xs text-slate-400">
                      <?= htmlspecialchars($lead['origin']) ?> ➔ <?= htmlspecialchars($lead['destination']) ?>
                    </div>
                  <?php endif; ?>
                </td>

                <!--  ESTIMATED PRICE DISPLAY -->
                <td class="py-4 px-4">
                  <span class="font-bold text-emerald-600">₱<?= number_format((float)($lead['estimated_amount'] ?? $lead['estimated_price'] ?? 0), 2) ?></span>
                </td>

                <!-- 4. STATUS -->
                <td class="py-4 px-4 align-middle">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getLeadStatusBadge($lead['status'] ?? 'new_inquiry') ?>">
                    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                    <?= htmlspecialchars(str_replace('_', ' ', strtoupper($lead['status'] ?? 'NEW INQUIRY'))) ?>
                  </span>
                </td>

                <!-- 5. CREATED AT -->
                <td class="py-4 px-4 text-xs text-slate-500">
                  <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-slate-300"></i>
                    <?= date('M d, Y • h:i A', strtotime($lead['created_at'])) ?>
                  </div>
                </td>

                <!-- 6. ACTIONS -->
                <td class="py-4 px-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <!-- FIXED  -->
                    <button 
                      type="button"
                      onclick="openViewModal(<?= htmlspecialchars(json_encode($lead, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>)" 
                      class="group px-3.5 py-1.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold rounded-xl text-xs transition-all active:scale-95 shadow-sm shadow-indigo-500/30 hover:shadow-md hover:shadow-indigo-500/30 inline-flex items-center gap-1.5">
                      <i class="fa-solid fa-eye text-[11px] transition group-hover:scale-110"></i> View & Manage
                    </button>
                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION FOOTER -->
    <?php if ($total_pages > 1): ?>
      <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <div>Showing page <strong><?= $page ?></strong> of <strong><?= $total_pages ?></strong></div>
        <div class="flex items-center gap-1">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&status=<?= $current_status ?>" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg font-medium hover:bg-slate-50">Previous</a>
          <?php endif; ?>
          <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&status=<?= $current_status ?>" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg font-medium hover:bg-slate-50">Next</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>

</main>

<?php include_once 'components/view_lead_modal.php'; ?>
<?php include_once 'components/lead_modal.php'; ?>

<!-- JAVASCRIPT FOR MODAL -->
<script src="../../../assets/js/sales_agent/myleads.js"></script>

<?php include_once 'components/alert.php'; ?>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>