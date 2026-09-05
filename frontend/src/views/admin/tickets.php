<?php
$page_title = "Closed Won Tickets · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Tickets mula sa FastAPI endpoint
$tickets_res  = make_api_request('/api/v1/admin/close-won-tickets', 'GET');
$tickets_list = $tickets_res['data'] ?? [];
$total_tickets = count($tickets_list);

// Compute summary stats for the dashboard
$overdue_tickets = 0;
$today = time();
foreach ($tickets_list as $ticket) {
    $pickupISO = $ticket['pickup_datetime'] ?? $ticket['pickup_date'] ?? '';
    if (!empty($pickupISO)) {
        $pickupTS = strtotime($pickupISO);
        if ($pickupTS !== false && $pickupTS <= $today) {
            $overdue_tickets++;
        }
    }
}
$pending_count = $total_tickets; // Tickets still needing account creation
$revenue_at_stake = 0;
foreach ($tickets_list as $ticket) {
    $revenue_at_stake += (float)($ticket['agreed_amount'] ?? 0);
}
$created_today = 0; // Could be extended with creation date filtering
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

    <!-- TOP HEADER -->
  <?php include_once 'components/top_header.php'; ?>

  <!-- DASHBOARD HEADER -->
  <div class="mb-8">
    <div class="flex items-center gap-3 mb-1">
      <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
        <i class="fa-solid fa-ticket text-rose-500 text-lg"></i>
      </div>
      <h1 class="text-2xl font-black text-slate-900 tracking-tight italic">Ticket Dashboard</h1>
    </div>
    <p class="text-xs text-slate-400">Closed-won tickets requiring customer portal account provisioning</p>
  </div>

  <!-- ROW 1: KPI SUMMARY CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    
    <!-- Total Tickets -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-rose-300 transition-all group">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-rose-600 transition-colors">Total Tickets</span>
        <div class="w-8 h-8 bg-rose-100 text-rose-500 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-ticket text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-2"><?= htmlspecialchars((string)$total_tickets) ?></div>
        <div class="flex items-center text-xs font-medium text-rose-500">
          <i class="fa-solid fa-clock mr-1 text-[10px]"></i>
          <span>Awaiting provisioning</span>
        </div>
      </div>
    </div>

    <!-- Pending Accounts -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-amber-300 transition-all group">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-amber-600 transition-colors">Pending Accounts</span>
        <div class="w-8 h-8 bg-amber-100 text-amber-500 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-user-plus text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-2"><?= htmlspecialchars((string)$pending_count) ?></div>
        <div class="flex items-center text-xs font-medium text-amber-600">
          <i class="fa-solid fa-exclamation-triangle mr-1 text-[10px]"></i>
          <span>Needs portal creation</span>
        </div>
      </div>
    </div>

    <!-- Overdue Tickets -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-red-300 transition-all group">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-red-600 transition-colors">Overdue Tickets</span>
        <div class="w-8 h-8 bg-red-100 text-red-500 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-triangle-exclamation text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-2"><?= htmlspecialchars((string)$overdue_tickets) ?></div>
        <div class="flex items-center text-xs font-medium text-red-600">
          <i class="fa-solid fa-hourglass-end mr-1 text-[10px]"></i>
          <span>Pickup deadline passed</span>
        </div>
      </div>
    </div>

    <!-- Revenue At Stake -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-indigo-300 transition-all group">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 group-hover:text-indigo-600 transition-colors">Revenue At Stake</span>
        <div class="w-8 h-8 bg-indigo-100 text-indigo-500 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-coins text-xs"></i>
        </div>
      </div>
      <div>
        <div class="text-3xl font-bold text-slate-900 mb-2">₱<?= number_format($revenue_at_stake, 0) ?></div>
        <div class="flex items-center text-xs font-medium text-indigo-600">
          <i class="fa-solid fa-chart-line mr-1 text-[10px]"></i>
          <span>Awaiting account creation</span>
        </div>
      </div>
    </div>

  </div>

  <!-- FILTER PILLS -->
  <div class="flex items-center gap-2 mb-6">
    <button class="px-5 py-2 bg-rose-600 text-white rounded-full text-xs font-bold shadow-sm shadow-rose-200">
      <i class="fa-solid fa-ticket mr-1.5"></i> All (<?= $total_tickets ?>)
    </button>
    <button class="px-5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-full text-xs font-semibold shadow-sm transition-all">
      <i class="fa-solid fa-user-plus mr-1.5"></i> Pending Account (<?= $pending_count ?>)
    </button>
    <button class="px-5 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-full text-xs font-semibold shadow-sm transition-all">
      <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Overdue (<?= $overdue_tickets ?>)
    </button>
    <div class="ml-auto">
      <button type="button" onclick="window.location.reload()" class="text-xs font-bold text-slate-500 hover:text-indigo-600 flex items-center gap-1.5 transition-colors">
        <i class="fa-solid fa-rotate-right"></i> Refresh
      </button>
    </div>
  </div>

    <!-- TABLE CONTAINER CARD -->
    <!-- SEARCH AND FILTER -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
      <div class="relative w-full max-w-sm">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input type="text" id="ticketSearch" placeholder="Search by company or email..." 
               class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 shadow-sm"
               onkeyup="filterTickets()">
      </div>
    </div>

  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- CARD HEADER -->
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-ticket-stamp text-rose-500 text-xs"></i>
        </div>
        <div>
          <h2 class="text-xl font-black text-slate-900 tracking-tight italic">Closed Won Tickets</h2>
          <p class="text-xs text-slate-400 mt-0.5">Tickets waiting for customer portal account creation</p>
        </div>
      </div>
      <div class="text-xs text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full">
        <span class="font-bold text-slate-900"><?= $total_tickets ?></span> total tickets
      </div>
    </div>

    <!-- DATA TABLE -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <!-- TABLE HEADER -->
            <thead>
            <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                <th class="py-4 px-6">Company / Lead</th>
                <th class="py-4 px-6">Contact Email</th>
                <th class="py-4 px-6">Agreed Amount</th>
                <th class="py-4 px-6">Status</th>
                <th class="py-4 px-6 text-center">Action</th>
            </tr>
            </thead>

                        <!-- TABLE BODY -->
            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
            
            <?php if (empty($tickets_list)): ?>
                <tr class="empty-state">
                <td colspan="5" class="py-16 text-center text-slate-400">
                    <div class="flex flex-col items-center gap-3">
                      <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-2xl text-emerald-500"></i>
                      </div>
                      <div>
                        <p class="text-sm font-semibold text-slate-600 mb-1">All caught up!</p>
                        <p class="text-xs">No pending customer tickets found. All accounts created.</p>
                      </div>
                    </div>
                </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tickets_list as $ticket): ?>
                <?php  
                    $contactPerson = $ticket['contact_person'] ?? 'Client Contact';
                    $nameParts = explode(' ', trim($contactPerson));
                    $firstName = $nameParts[0] ?? '';
                    $lastName = count($nameParts) > 1 ? end($nameParts) : '';
                    
                    // Pickup ISO Timestamp
                    $pickupISO = $ticket['pickup_datetime'] ?? $ticket['pickup_date'] ?? '';
                ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    
                    <!-- COMPANY / LEAD -->
                    <td class="py-4 px-6">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                        <div class="font-bold text-slate-900"><?= htmlspecialchars($ticket['company_name'] ?? 'Individual Client') ?></div>
                        <div class="text-[11px] text-slate-400">Contact: <?= htmlspecialchars($contactPerson) ?></div>
                        </div>
                    </div>
                    </td>

                                                                                                    <!-- EMAIL -->
                    <td class="py-4 px-6 font-medium text-slate-600">
                    <div class="flex items-center gap-1.5">
                      <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                      <?= htmlspecialchars($ticket['email']) ?>
                    </div>
                    </td>

                    <!-- AGREED AMOUNT -->
                    <td class="py-4 px-6 font-bold text-slate-900">
                    ₱<?= number_format((float)($ticket['agreed_amount'] ?? 0), 2) ?>
                    </td>

                    <!-- STATUS WITH COUNTDOWN BADGE -->
                    <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <!-- Status Badge -->
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 font-semibold text-[10px] rounded-full inline-flex items-center gap-1 shrink-0">
                        <i class="fa-solid fa-clock text-[9px]"></i> Needs Account
                        </span>

                        <!-- Dynamic Countdown Badge -->
                        <?php if (!empty($pickupISO)): ?>
                        <span 
                            class="pickup-timer inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition-all shadow-sm"
                            data-pickup="<?= htmlspecialchars($pickupISO) ?>">
                            <i class="fa-solid fa-spinner fa-spin text-[9px]"></i> Loading...
                        </span>
                        <?php endif; ?>
                    </div>
                    </td>

                    <!-- ACTION BUTTON -->
                    <td class="py-4 px-6 text-center">
                    <button 
                        type="button"
                        onclick='openCreateModal(<?= json_encode([
                        "ticket_id" => $ticket["id"],
                        "email" => $ticket["email"],
                        "first_name" => $firstName,
                        "last_name" => $lastName,
                        "company_name" => $ticket["company_name"] ?? "",
                        "phone_number" => $ticket["phone_number"] ?? ""
                        ]) ?>)'
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all shadow-sm active:scale-95 inline-flex items-center gap-1.5"
                    >
                        <i class="fa-solid fa-user-plus text-[11px]"></i> Create Account
                    </button>
                    </td>

                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

  </div>

</main>

<!-- CREATE CUSTOMER ACCOUNT MODAL -->
<div data-brand="priority" id="accountModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transform transition-all">
    
        <!-- MODAL HEADER -->
    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
          <i class="fa-solid fa-user-plus text-indigo-500 text-lg"></i>
        </div>
        <div>
          <h3 class="text-lg font-black text-slate-900 italic">Create Portal Account</h3>
          <p class="text-xs text-slate-400">Generate customer login credentials</p>
        </div>
      </div>
      <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-sm">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- FORM -->
    <form id="createAccountForm" onsubmit="submitCreateAccount(event)">
      <input type="hidden" id="modal_ticket_id" name="ticket_id">

      <div class="space-y-3.5 text-xs">
        
        <div>
          <label class="block font-bold text-slate-700 mb-1">Email Address</label>
          <input type="email" id="modal_email" name="email" required readonly class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 font-semibold focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">First Name</label>
            <input type="text" id="modal_first_name" name="first_name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Last Name</label>
            <input type="text" id="modal_last_name" name="last_name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
          </div>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Company Name</label>
          <input type="text" id="modal_company_name" name="company_name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
          <input type="text" id="modal_phone_number" name="phone_number" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-indigo-500">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block font-bold text-slate-700">Generated Password</label>
            <button type="button" onclick="generatePassword()" class="text-[10px] font-bold text-indigo-600 hover:underline">Auto Generate</button>
          </div>
          <input type="text" id="modal_password" name="password" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono text-indigo-600 font-bold focus:outline-none focus:border-indigo-500">
        </div>

      </div>

      <!-- MODAL ACTIONS -->
      <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
          Cancel
        </button>
        <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all shadow-md inline-flex items-center gap-2">
          <span>Create Account</span>
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </button>
      </div>

    </form>

  </div>
</div>

<!-- JAVASCRIPT LOGIC FOR COUNTDOWN ENGINE -->
<script src="../../../assets/js/admin/countdown.js"></script>

<script src="../../../assets/js/admin/tickets.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>