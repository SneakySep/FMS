<?php
$page_title = "Customer Accounts · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch Customer Accounts mula sa FastAPI endpoint
$customers_res  = make_api_request('/api/v1/admin/customer-accounts', 'GET');

// Handle single-wrapped or double-wrapped JSON response
$raw_list       = $customers_res['data'] ?? [];
$customers_list = isset($raw_list['data']) && is_array($raw_list['data']) ? $raw_list['data'] : $raw_list;

if (!is_array($customers_list)) {
    $customers_list = [];
}

$total_customers = count($customers_list);
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <?php include_once 'components/top_header.php'; ?>

  <!-- PAGE HEADER & ACTIONS -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight italic">Customer Management</h1>
          <p class="text-slate-500 text-sm">Manage and monitor all client portal accounts.</p>
      </div>
      <div class="flex items-center gap-3">
          <button type="button" onclick="openAddCustomerModal()"
                  class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
              <i class="fa-solid fa-plus"></i> Add New Customer
          </button>
      </div>
  </div>

  <!-- TABLE CONTAINER CARD -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    
    <!-- CARD HEADER / SEARCH -->
    <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-900">All Accounts</h2>
            <p class="text-xs text-slate-400">Showing <?= $total_customers ?> registered customers</p>
        </div>
      </div>
      
      <div class="relative w-full md:w-72">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" id="customerSearch" placeholder="Search customers..." 
               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
      </div>
    </div>

    <!-- DATA TABLE -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <!-- TABLE HEADER -->
        <thead>
          <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
            <th class="py-4 px-6">Customer</th>
            <th class="py-4 px-6">Email Address</th>
            <th class="py-4 px-6">Type</th>
            <th class="py-4 px-6">Company</th>
            <th class="py-4 px-6">Status</th>
            <th class="py-4 px-6 text-center">Actions</th>
          </tr>
        </thead>

        <!-- TABLE BODY -->
        <tbody class="divide-y divide-slate-100 text-xs text-slate-700" id="customerTableBody">
          <?php if (empty($customers_list)): ?>
            <tr class="empty-state">
              <td colspan="6" class="py-12 text-center text-slate-400">
                <i class="fa-solid fa-users-slash text-2xl mb-2 text-slate-300 block"></i>
                No customer accounts created yet.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($customers_list as $user): ?>
              <?php 
                $firstName = $user['first_name'] ?? '';
                $lastName  = $user['last_name'] ?? '';
                $fullName  = trim("$firstName $lastName");
                if (empty($fullName)) $fullName = $user['full_name'] ?? 'Customer';

                $email     = $user['email'] ?? 'N/A';
                $company   = !empty($user['company_name']) ? $user['company_name'] : 'Individual';

                // Admin-managed status (customer_status.sql). Falls back to
                // 'Active' until the migration has been applied.
                $statusRaw = trim((string)($user['status'] ?? ''));
                $allowed   = ['Active', 'Inactive', 'Deactivated'];
                if (!in_array($statusRaw, $allowed, true)) {
                    $statusRaw = 'Active';
                }
                $status = $statusRaw;
                $statusClass = [
                    'Active'      => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'Inactive'    => 'bg-slate-100 text-slate-500 border-slate-200',
                    'Deactivated' => 'bg-rose-50 text-rose-600 border-rose-100',
                ][$status];
                // Permanent delete unlocks only after the account is deactivated.
                $canDelete = ($status === 'Deactivated');

                // Derive B2B / B2C. Prioritize the real customer_type column; fall
                // back to company_name so rows created before the migration still
                // badge correctly.
                $typeRaw   = strtolower((string)($user['customer_type'] ?? ''));
                $isB2B     = $typeRaw === 'business'
                             || ($typeRaw === '' && !empty($user['company_name']));
                $typeLabel = $isB2B ? 'B2B' : 'B2C';
                $typeClass = $isB2B
                    ? 'bg-sky-50 text-sky-600 border-sky-100'
                    : 'bg-amber-50 text-amber-600 border-amber-100';

                // NOTE: JSON_HEX_QUOT is deliberately NOT used. It escapes the
                // structural quotes of the JSON to \u0022, which is invalid
                // JavaScript once the payload is interpolated into an inline
                // handler. JSON_HEX_APOS keeps the payload safe inside the
                // single-quoted data-customer attribute below instead.
                $detailPayload = json_encode([
                    'id'            => $user['id'] ?? '',
                    'first_name'    => $firstName,
                    'last_name'     => $lastName,
                    'email'         => $email,
                    'company_name'  => $user['company_name'] ?? '',
                    'phone_number'  => $user['phone_number'] ?? '',
                    'created_at'    => $user['created_at'] ?? '',
                    'account_type'  => $typeLabel,
                    'address'       => $user['address'] ?? '',
                    'tax_id'        => $user['tax_id'] ?? '',
                    'website'       => $user['website'] ?? '',
                    'status'        => $status,
                ], JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP);
              ?>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="py-4 px-6 font-semibold text-slate-900"><?= htmlspecialchars($fullName) ?></td>
                <td class="py-4 px-6 text-slate-500"><?= htmlspecialchars($email) ?></td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border <?= $typeClass ?>">
                        <?= $typeLabel ?>
                    </span>
                </td>
                <td class="py-4 px-6 text-slate-600"><?= htmlspecialchars($company) ?></td>
                <td class="py-4 px-6">
                    <span data-status-badge="<?= htmlspecialchars($user['id'] ?? '') ?>"
                          class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border <?= $statusClass ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </td>
                <td class="py-4 px-6 text-center">
                  <div class="relative inline-flex items-center gap-1 justify-center cstm-actions"
                       id="cstm-actions-<?= htmlspecialchars($user['id'] ?? '') ?>"
                       data-customer='<?= $detailPayload ?>'>
                    <button type="button" onclick="openDetailsModal(customerRowData(this))"
                            class="text-slate-400 hover:text-indigo-600 transition-colors p-2" title="View details">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    <button type="button"
                            onclick="toggleCustomerActionMenu(event, 'cstm-menu-<?= htmlspecialchars($user['id'] ?? '') ?>')"
                            class="text-slate-400 hover:text-slate-800 transition-colors p-2" title="Actions">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>

                    <!-- ACTIONS DROPDOWN -->
                    <div id="cstm-menu-<?= htmlspecialchars($user['id'] ?? '') ?>"
                         class="cstm-menu hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-xl border border-slate-200 shadow-lg text-left z-30 py-1">
                      <button type="button" onclick="openDetailsModal(customerRowData(this))"
                              class="w-full text-left px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        <i class="fa-solid fa-address-card w-4 text-slate-400"></i> View details
                      </button>
                      <div class="my-1 border-t border-slate-100"></div>
                      <button type="button" onclick="openManageCustomerModal(customerRowData(this))"
                              class="w-full text-left px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        <i class="fa-solid fa-sliders w-4 text-slate-400"></i> Manage status&hellip;
                      </button>
                      <button type="button" data-quick-status="Active" onclick="applyQuickStatus(customerRowData(this), 'Active')"
                              class="w-full text-left px-3.5 py-2 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 <?= $status === 'Active' ? 'opacity-40 pointer-events-none' : '' ?>">
                        <i class="fa-solid fa-circle-check w-4"></i> Set Active
                      </button>
                      <button type="button" data-quick-status="Inactive" onclick="applyQuickStatus(customerRowData(this), 'Inactive')"
                              class="w-full text-left px-3.5 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-50 <?= $status === 'Inactive' ? 'opacity-40 pointer-events-none' : '' ?>">
                        <i class="fa-solid fa-pause w-4"></i> Set Inactive
                      </button>
                      <button type="button" data-quick-status="Deactivated" onclick="applyQuickStatus(customerRowData(this), 'Deactivated')"
                              class="w-full text-left px-3.5 py-2 text-xs font-semibold text-amber-600 hover:bg-amber-50 <?= $status === 'Deactivated' ? 'opacity-40 pointer-events-none' : '' ?>">
                        <i class="fa-solid fa-lock w-4"></i> Deactivate
                      </button>
                      <div class="my-1 border-t border-slate-100"></div>
                      <!-- Dalawang bersyon ng Delete: ipinapakita lang yung
                           tumutugma sa kasalukuyang status ng row. -->
                      <button type="button" data-delete-option="deactivated" hidden
                              onclick="openDeleteCustomerModal(customerRowData(this))"
                              class="w-full text-left px-3.5 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                        <i class="fa-solid fa-trash-can w-4"></i> Delete
                      </button>
                      <button type="button" data-delete-option="locked" <?= $canDelete ? 'hidden' : '' ?> disabled
                              title="Deactivate this account first"
                              class="w-full text-left px-3.5 py-2 text-xs font-bold text-slate-300 cursor-not-allowed">
                        <i class="fa-solid fa-trash-can w-4"></i> Delete
                        <span class="block pl-7 text-[9px] font-medium text-slate-400 -mt-0.5">Deactivate first</span>
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</main>

<!-- VIEW DETAILS MODAL -->
<div data-brand="priority" id="customerDetailsModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
    
    <!-- MODAL HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-bold">
          <i class="fa-solid fa-address-card"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Customer Details</h3>
          <p class="text-xs text-slate-400">Portal Account Information</p>
        </div>
      </div>
      <button type="button" onclick="closeDetailsModal()" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- MODAL BODY -->
    <div class="py-5 space-y-3.5">
      
      <!-- FIRST NAME & LAST NAME -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">First Name</label>
          <div id="modal_first_name" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Last Name</label>
          <div id="modal_last_name" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
        </div>
      </div>

      <!-- ACCOUNT TYPE -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Account Type</label>
        <div id="modal_account_type" class="text-xs font-bold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
      </div>

      <!-- EMAIL -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Email Address</label>
        <div id="modal_email" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-2">
          <i class="fa-regular fa-envelope text-slate-400"></i>
          <span>--</span>
        </div>
      </div>

      <!-- COMPANY NAME -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Company Name</label>
        <div id="modal_company" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-building text-slate-400"></i>
          <span>--</span>
        </div>
      </div>

      <!-- PHONE NUMBER & CREATED AT -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Phone Number</label>
          <div id="modal_phone" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-1.5">
            <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
            <span>--</span>
          </div>
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Date Created</label>
          <div id="modal_created_at" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-1.5">
            <i class="fa-regular fa-calendar-check text-slate-400 text-[10px]"></i>
            <span>--</span>
          </div>
        </div>
      </div>

      <!-- BUSINESS DETAILS (B2B only) -->
      <div id="modal_business_details" class="hidden space-y-3.5">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tax ID</label>
            <div id="modal_tax_id" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
          </div>
          <div>
            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Website</label>
            <div id="modal_website" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
          </div>
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Address</label>
          <div id="modal_address" class="text-xs font-semibold text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">--</div>
        </div>
      </div>

    </div>

    <!-- MODAL FOOTER -->
    <div class="pt-4 border-t border-slate-100 flex justify-end">
      <button
        type="button"
        onclick="closeDetailsModal()"
        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all active:scale-95">
        Close
      </button>
    </div>

  </div>
</div>

<!-- ADD NEW CUSTOMER MODAL -->
<div data-brand="priority" id="addCustomerModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[92vh] overflow-y-auto">

    <!-- MODAL HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-bold">
          <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Add New Customer</h3>
          <p class="text-xs text-slate-400">Create a B2B or B2C portal account</p>
        </div>
      </div>
      <button type="button" onclick="closeAddCustomerModal()" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="addCustomerForm" onsubmit="return submitAddCustomer(event)" class="py-5 space-y-4">

      <!-- ACCOUNT TYPE SELECTOR -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Account Type</label>
        <div class="grid grid-cols-2 gap-3">
          <label class="account-type-card cursor-pointer rounded-xl border-2 border-slate-200 p-3 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/60">
            <input type="radio" name="account_type" value="business" class="sr-only" onchange="onAccountTypeChange()" />
            <span class="flex items-center gap-2 text-xs font-bold text-slate-700"><i class="fa-solid fa-building text-sky-500"></i> Business (B2B)</span>
            <span class="block text-[10px] text-slate-400 mt-0.5">Company account</span>
          </label>
          <label class="account-type-card cursor-pointer rounded-xl border-2 border-slate-200 p-3 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/60">
            <input type="radio" name="account_type" value="individual" class="sr-only" checked onchange="onAccountTypeChange()" />
            <span class="flex items-center gap-2 text-xs font-bold text-slate-700"><i class="fa-solid fa-user text-amber-500"></i> Individual (B2C)</span>
            <span class="block text-[10px] text-slate-400 mt-0.5">Personal account</span>
          </label>
        </div>
      </div>

      <!-- COMPANY NAME (B2B only) -->
      <div id="acm_company_group" class="hidden">
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Company Name <span class="text-rose-500">*</span></label>
        <input type="text" id="acm_company_name" class="acm-input" placeholder="ABC Logistics Inc." />
      </div>

      <!-- FIRST & LAST NAME -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">First Name <span class="text-rose-500">*</span></label>
          <input type="text" id="acm_first_name" class="acm-input" placeholder="Juan" />
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Last Name <span class="text-rose-500">*</span></label>
          <input type="text" id="acm_last_name" class="acm-input" placeholder="Dela Cruz" />
        </div>
      </div>

      <!-- CONTACT PERSON (B2B optional) -->
      <div id="acm_contact_person_group" class="hidden">
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Contact Person <span class="text-slate-300 normal-case font-medium">(optional)</span></label>
        <input type="text" id="acm_contact_person" class="acm-input" placeholder="Person handling shipments" />
      </div>

      <!-- EMAIL & PHONE -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Email <span class="text-rose-500">*</span></label>
          <input type="email" id="acm_email" class="acm-input" placeholder="customer@email.com" />
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Phone <span class="text-rose-500">*</span></label>
          <input type="text" id="acm_phone" class="acm-input" placeholder="09171234567" />
        </div>
      </div>

      <!-- PASSWORD -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Password <span class="text-rose-500">*</span></label>
        <div class="flex gap-2">
          <input type="text" id="acm_password" class="acm-input flex-1" placeholder="Min 8 characters" />
          <button type="button" onclick="generateCustomerPassword()" title="Auto-generate password"
                  class="px-3 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl text-xs transition-colors shrink-0">
            <i class="fa-solid fa-dice"></i>
          </button>
        </div>
      </div>

      <!-- B2B EXTRAS -->
      <div id="acm_business_extras" class="hidden grid grid-cols-2 gap-3">
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tax ID <span class="text-slate-300 normal-case font-medium">(optional)</span></label>
          <input type="text" id="acm_tax_id" class="acm-input" placeholder="123-456-789-000" />
        </div>
        <div>
          <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Website <span class="text-slate-300 normal-case font-medium">(optional)</span></label>
          <input type="text" id="acm_website" class="acm-input" placeholder="https://abclogistics.ph" />
        </div>
      </div>

      <!-- ADDRESS / BILLING ADDRESS -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1"><span id="acm_address_label">Billing Address</span> <span class="text-slate-300 normal-case font-medium">(optional)</span></label>
        <textarea id="acm_address" rows="2" class="acm-input resize-none" placeholder="Street, City, Province"></textarea>
      </div>

      <!-- FORM ERROR -->
      <div id="acm_error" class="hidden text-[11px] font-semibold text-rose-600 bg-rose-50 border border-rose-100 rounded-xl px-3 py-2.5"></div>

      <!-- FOOTER -->
      <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
        <button type="button" onclick="closeAddCustomerModal()"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-all active:scale-95">
          Cancel
        </button>
        <button type="submit" id="acmSubmitBtn"
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
          <i class="fa-solid fa-user-plus"></i> <span>Create Customer</span>
        </button>
      </div>

    </form>
  </div>
</div>

<!-- ============================================================================
     MANAGE CUSTOMER (status) MODAL  ->  PATCH /api/v1/admin/customer-accounts/status
     ========================================================================== -->
<div data-brand="priority" id="manageCustomerModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-bold">
          <i class="fa-solid fa-sliders"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900">Manage Customer</h3>
          <p class="text-xs text-slate-400" id="mcm_subtitle">Account status</p>
        </div>
      </div>
      <button type="button" onclick="closeManageCustomerModal()" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="py-5 space-y-4">
      <input type="hidden" id="mcm_user_id" value="" />

      <p class="text-xs text-slate-500">
        Choose the status to apply to <span class="font-bold text-slate-700" id="mcm_name">this customer</span>.
        Nothing changes until you press <span class="font-bold text-slate-700">Apply Changes</span>.
      </p>

      <!-- STATUS CHOICES -->
      <div class="space-y-2" id="mcm_choices">
        <label class="mcm-choice block cursor-pointer border border-slate-200 rounded-xl p-3.5 transition-all" data-status="Active" onclick="selectCustomerStatus('Active')">
          <div class="flex items-start gap-3">
            <span class="mcm-radio mt-0.5 w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0"></span>
            <div>
              <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Active</div>
              <p class="text-[11px] text-slate-500 mt-0.5">Normal portal access. The customer can sign in and create bookings.</p>
            </div>
          </div>
        </label>

        <label class="mcm-choice block cursor-pointer border border-slate-200 rounded-xl p-3.5 transition-all" data-status="Inactive" onclick="selectCustomerStatus('Inactive')">
          <div class="flex items-start gap-3">
            <span class="mcm-radio mt-0.5 w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0"></span>
            <div>
              <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Inactive</div>
              <p class="text-[11px] text-slate-500 mt-0.5">Parked account. All data is kept and sign-in still works, but the customer is flagged as on hold.</p>
            </div>
          </div>
        </label>

        <label class="mcm-choice block cursor-pointer border border-slate-200 rounded-xl p-3.5 transition-all" data-status="Deactivated" onclick="selectCustomerStatus('Deactivated')">
          <div class="flex items-start gap-3">
            <span class="mcm-radio mt-0.5 w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0"></span>
            <div>
              <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Deactivated</div>
              <p class="text-[11px] text-slate-500 mt-0.5">Locks the customer out of the portal immediately. Records are kept, and permanent deletion becomes available.</p>
            </div>
          </div>
        </label>
      </div>

      <div id="mcm_current_note" class="text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-100 rounded-xl px-3 py-2.5"></div>

      <!-- ERROR -->
      <div id="mcm_error" class="hidden text-[11px] font-semibold text-rose-600 bg-rose-50 border border-rose-100 rounded-xl px-3 py-2.5"></div>
    </div>

    <!-- FOOTER -->
    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
      <button type="button" onclick="closeManageCustomerModal()"
              class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-all active:scale-95">
        Cancel
      </button>
      <button type="button" id="mcmApplyBtn" onclick="applyCustomerChanges()"
              class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
        <i class="fa-solid fa-check"></i> <span>Apply Changes</span>
      </button>
    </div>
  </div>
</div>

<!-- ============================================================================
     DELETE CUSTOMER MODAL  ->  POST /api/v1/admin/delete-customer
     Privacy-Policy-gated and two-step: only a Deactivated account can be erased.
     ========================================================================== -->
<div data-brand="priority" id="deleteCustomerModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-rose-100">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-rose-100">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-lg font-bold">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-rose-700">Permanently Delete Account</h3>
          <p class="text-xs text-slate-400">This action cannot be undone</p>
        </div>
      </div>
      <button type="button" onclick="closeDeleteCustomerModal()" class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="py-5 space-y-4">
      <input type="hidden" id="dcm_user_id" value="" />
      <input type="hidden" id="dcm_email" value="" />

      <!-- TARGET ACCOUNT -->
      <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Account being erased</div>
        <div class="text-xs font-bold text-slate-800" id="dcm_name">--</div>
        <div class="text-[11px] text-slate-500" id="dcm_email_label">--</div>
      </div>

      <!-- PRIVACY POLICY NOTICE -->
      <div class="bg-rose-50/70 border border-rose-100 rounded-xl p-3.5 space-y-2">
        <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-rose-700">
          <i class="fa-solid fa-shield-heart"></i> Privacy Policy &amp; RA 10173
        </div>
        <p class="text-[11px] leading-relaxed text-slate-600">
          Permanent deletion erases this customer's portal account, CRM record and portal
          settings. Under the <span class="font-bold">Data Privacy Act of 2012 (RA 10173)</span>
          and our Privacy Policy, the data subject has the right to erasure or blocking of
          personal information. Only exercise this after the customer's lawful request, and
          retain any records that other laws require us to keep.
        </p>
        <p class="text-[11px] leading-relaxed text-slate-600">
          <i class="fa-solid fa-ban text-rose-500"></i>
          Bookings, invoices and tickets created for this customer are <span class="font-bold">not</span>
          removed here and may still reference the deleted account.
        </p>
        <details class="pt-1">
          <summary class="text-[11px] font-bold text-indigo-600 cursor-pointer select-none hover:text-indigo-800">
            <i class="fa-solid fa-book-open text-[9px]"></i> Relevant Privacy Policy clauses
          </summary>
          <div class="mt-2 space-y-1.5 text-[10.5px] leading-relaxed text-slate-600 border-t border-rose-100 pt-2">
            <p><span class="font-bold">6. Your Rights Under RA 10173 &ndash; Erasure or Blocking:</span>
              Request the suspension, withdrawal, or removal of your personal data from our systems
              (subject to legal or contractual limitations, such as active freight contracts or
              customs retention mandates).</p>
            <p><span class="font-bold">5. Data Retention and Security:</span>
              Personal data is retained only for as long as necessary to fulfill this policy, settle
              accounts, resolve disputes, or comply with statutory retention requirements under
              Philippine law.</p>
            <p><span class="font-bold">7. Data Protection Officer:</span>
              Priority Handling Logistics Inc. &mdash; cs@priority-ph.com
              (subject line &ldquo;Attn: Data Protection Officer / Privacy Request&rdquo;).</p>
          </div>
        </details>
      </div>

      <!-- ACKNOWLEDGEMENT -->
      <label class="flex items-start gap-3 cursor-pointer bg-white border border-slate-200 rounded-xl p-3.5 hover:border-rose-200 transition-colors">
        <input type="checkbox" id="dcm_ack" class="mt-0.5 accent-rose-600" onchange="validateDeleteForm()" />
        <span class="text-[11px] leading-relaxed text-slate-600">
          I have read the Privacy Policy and confirm this erasure is lawful and requested by
          (or lawfully permitted for) this customer.
        </span>
      </label>

      <!-- REASON -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
          Reason for deletion <span class="text-rose-500">*</span>
          <span class="text-slate-300 normal-case font-medium">- recorded in the audit log, min 10 characters</span>
        </label>
        <textarea id="dcm_reason" rows="2" class="acm-input resize-none"
                  placeholder="e.g. Written erasure request received from the data subject on ..."
                  oninput="validateDeleteForm()"></textarea>
      </div>

      <!-- TYPED CONFIRMATION -->
      <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
          Type the customer's email to confirm <span class="text-rose-500">*</span>
        </label>
        <input type="text" id="dcm_confirm" autocomplete="off" class="acm-input"
               placeholder="name@example.com" oninput="validateDeleteForm()" />
      </div>

      <!-- ERROR -->
      <div id="dcm_error" class="hidden text-[11px] font-semibold text-rose-600 bg-rose-50 border border-rose-100 rounded-xl px-3 py-2.5"></div>
    </div>

    <!-- FOOTER -->
    <div class="pt-4 border-t border-rose-100 flex justify-end gap-2">
      <button type="button" onclick="closeDeleteCustomerModal()"
              class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-all active:scale-95">
        Cancel
      </button>
      <button type="button" id="dcmDeleteBtn" onclick="confirmDeleteCustomer()" disabled
              class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm shadow-rose-200 transition-all active:scale-95 flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-rose-600">
        <i class="fa-solid fa-trash-can"></i> <span>Permanently Delete</span>
      </button>
    </div>
  </div>
</div>

<!-- Shared styles for Add Customer modal inputs -->
<style>
  .acm-input {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.625rem 0.75rem;
    font-size: 12px;
    font-weight: 500;
    color: #1e293b;
    outline: none;
    transition: all .15s ease;
  }
  .acm-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    background: #fff;
  }
  .acm-input.acm-invalid {
    border-color: #f43f5e;
    box-shadow: 0 0 0 3px rgba(244, 63, 94, .12);
  }
</style>

<!-- JAVASCRIPT -->
<?php include_once '../../components/alert.php'; ?>

<script src="../../../assets/js/admin/customers.js"></script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>