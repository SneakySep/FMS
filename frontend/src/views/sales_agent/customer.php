<?php
$pageTitle = "Customer Directory";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// 1. Parameters mula sa URL Filter
$current_tier  = $_GET['tier'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// 2. Fetch Live Stats 
$stats_res  = make_api_request('/api/v1/customers/stats', 'GET');
$stats_data = $stats_res['data']['data'] ?? $stats_res['data'] ?? [];

$count_all      = (int)($stats_data['all'] ?? 0);
$count_bronze   = (int)($stats_data['bronze'] ?? 0);
$count_silver   = (int)($stats_data['silver'] ?? 0);
$count_gold     = (int)($stats_data['gold'] ?? 0);
$count_platinum = (int)($stats_data['platinum'] ?? 0);

// 3. Build API Endpoint Query String
$api_url = "/api/v1/customers?tier=" . urlencode($current_tier);

if (!empty($search_query)) {
    $api_url .= "&search=" . urlencode($search_query);
}

// Fetch Customers List
$customers_res  = make_api_request($api_url, 'GET');
$customers_list = $customers_res['data']['data'] ?? $customers_res['data'] ?? [];

// Helper function para sa Tier Status Badge
function getCustomerTierBadge($tier) {
    switch (strtoupper(trim($tier ?? 'BRONZE'))) {
        case 'PLATINUM':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'GOLD':
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'SILVER':
            return 'bg-slate-100 text-slate-700 border-slate-200';
        case 'BRONZE':
        default:
            return 'bg-amber-100 text-amber-800 border-amber-200';
    }
}
?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER & NAVBAR -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- TIER FILTER TABS -->
  <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    <a href="?tier=all<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $current_tier === 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      All (<?= $count_all ?>)
    </a>
    <a href="?tier=BRONZE<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= strtoupper($current_tier) === 'BRONZE' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Bronze (<?= $count_bronze ?>)
    </a>
    <a href="?tier=SILVER<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= strtoupper($current_tier) === 'SILVER' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Silver (<?= $count_silver ?>)
    </a>
    <a href="?tier=GOLD<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= strtoupper($current_tier) === 'GOLD' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Gold (<?= $count_gold ?>)
    </a>
    <a href="?tier=PLATINUM<?= !empty($search_query) ? '&search='.urlencode($search_query) : '' ?>" 
       class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= strtoupper($current_tier) === 'PLATINUM' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
      Platinum (<?= $count_platinum ?>)
    </a>
  </div>

  <!-- CUSTOMERS TABLE CARD -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    
    <!-- CARD HEADER -->
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-base font-bold text-slate-800">Customer Directory</h2>
        <p class="text-xs text-slate-400">View and manage customer tiers and booking accounts</p>
      </div>
      <button class="text-sm font-semibold text-purple-600 hover:text-purple-700 flex items-center gap-1">
        Export CSV ➔
      </button>
    </div>

    <!-- MAIN TABLE -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
            <th class="py-3 px-4">Company & Contact</th>
            <th class="py-3 px-4">Email Address</th>
            <th class="py-3 px-4">Phone Number</th>
            <th class="py-3 px-4">Total Bookings</th>
            <th class="py-3 px-4">Tier Status</th>
            <th class="py-3 px-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm">
          <?php if (empty($customers_list)): ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">
                No customers found.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($customers_list as $customer): ?>
              <?php
                // Safe fallbacks para sa data field mapping
                $company = $customer['company_name'] ?? $customer['company'] ?? 'N/A';
                $contact = $customer['contact_person'] ?? $customer['name'] ?? 'No Contact Person';
                $email   = $customer['email'] ?? 'N/A';
                $phone   = $customer['phone_number'] ?? $customer['phone'] ?? 'N/A';
                $bookings = $customer['total_bookings'] ?? $customer['bookings'] ?? 0;
                $tier    = $customer['tier'] ?? 'BRONZE';
                $cust_id  = $customer['id'] ?? '';
              ?>
              <tr class="hover:bg-slate-50/80 transition">
                
                <!-- 1. COMPANY NAME & CONTACT PERSON -->
                <td class="py-4 px-4">
                  <div class="font-bold text-slate-800">
                    <?= htmlspecialchars($company) ?>
                  </div>
                  <div class="text-xs text-slate-500">
                     <?= htmlspecialchars($contact) ?>
                  </div>
                </td>

                <!-- 2. EMAIL ADDRESS -->
                <td class="py-4 px-4 text-slate-600 font-medium">
                  <?= htmlspecialchars($email) ?>
                </td>

                <!-- 3. PHONE NUMBER -->
                <td class="py-4 px-4 text-slate-600">
                  <?= htmlspecialchars($phone) ?>
                </td>

                <!-- 4. TOTAL BOOKINGS -->
                <td class="py-4 px-4 font-bold text-slate-800">
                  <?= (int)$bookings ?>
                </td>

                <!-- 5. TIER STATUS -->
                <td class="py-4 px-4 align-middle">
                  <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-[11px] font-bold border tracking-wide whitespace-nowrap leading-none <?= getCustomerTierBadge($tier) ?>">
                    <?= htmlspecialchars(strtoupper($tier)) ?>
                  </span>
                </td>

                <!-- 6. ACTIONS -->
                  <td class="py-4 px-4 align-middle text-right">
                    <!-- Palitan ang 'your_target_page.php' ng destination PHP file mo -->
                    <a href="view_customer.php?id=<?= urlencode($cust_id) ?>" 
                      class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg inline-flex items-center justify-center transition"
                      title="View Details">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                      </svg>
                    </a>
                  </td>


              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>

</main>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>