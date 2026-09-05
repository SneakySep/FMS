<?php
$page_title = "Agents Management · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// Fetch agents from the backend API
$agents_res = make_api_request('/api/v1/admin/agents', 'GET');

// Handle API response: check for errors or missing data
$agents_raw = [];
if ($agents_res['status_code'] === 200 && !empty($agents_res['data'])) {
    $api_data = $agents_res['data'];
    // Handle double-wrapped response: { "status": "success", "data": [...] }
    if (isset($api_data['data']) && is_array($api_data['data'])) {
        $agents_raw = $api_data['data'];
    } elseif (is_array($api_data) && !isset($api_data['detail'])) {
        // Single-wrapped or flat array (but not an error object)
        $agents_raw = $api_data;
    }
}

// Track whether the API actually answered, so the view can tell "no agents
// yet" apart from "backend unavailable". Placeholder rows are never injected:
// an admin panel that invents agents and revenue is a data-integrity hazard.
$agents_error = false;
if ($agents_res['status_code'] !== 200) {
    $agents_error = true;
}

// Normalize into a flat array the table expects
$agents = [];
if (is_array($agents_raw)) {
    foreach ($agents_raw as $row) {
        if (!is_array($row)) {
            continue; // Skip non-array entries (e.g. error objects)
        }
        // Properly resolve status with correct precedence
        if (isset($row['status']) && $row['status'] !== null) {
            $status = ucfirst((string) $row['status']);
        } elseif (isset($row['is_active'])) {
            $status = $row['is_active'] ? 'Active' : 'Inactive';
        } else {
            $status = 'Active';
        }

        $agents[] = [
            'id'     => $row['id'] ?? $row['agent_id'] ?? 0,
            'name'   => $row['name'] ?? trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
            'email'  => $row['email'] ?? '',
            'status' => $status,
            'sales'  => (float)($row['sales'] ?? $row['total_sales'] ?? $row['revenue'] ?? 0),
        ];
    }
}

?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

    <!-- TOP HEADER -->
    <?php include_once 'components/top_header.php'; ?>

    <!-- BREADCRUMBS -->
    <nav class="flex mt-6 mb-4 text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="/admin/dashboard.php" class="hover:text-blue-600">Dashboard</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="font-semibold text-gray-800">Agents</li>
        </ol>
    </nav>

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Agent Management</h1>
            <p class="text-gray-600">View and manage your sales agents.</p>
        </div>
        <button onclick="toggleModal('addAgentModal')" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition flex items-center shadow-sm">
            <i class="fas fa-plus mr-2"></i> Add Agent
        </button>
    </div>

    <!-- AGENT KPI CARDS -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-full mr-4"><i class="fas fa-users text-xl"></i></div>
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Agents</h3>
                <p class="text-3xl font-bold text-gray-900"><?php echo count($agents); ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 bg-green-50 text-green-600 rounded-full mr-4"><i class="fas fa-user-check text-xl"></i></div>
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Active Agents</h3>
                <p class="text-3xl font-bold text-gray-900">
                    <?php echo count(array_filter($agents, function($a) { return $a['status'] === 'Active'; })); ?>
                </p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-full mr-4"><i class="fas fa-chart-line text-xl"></i></div>
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Revenue</h3>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format(array_sum(array_column($agents, 'sales')), 2); ?></p>
            </div>
        </div>
    </div>

    <!-- AGENTS TABLE -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-800">All Agents</h2>
            <div class="flex gap-2">
                <input type="text" placeholder="Search agents..." class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php foreach ($agents as $agent): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $agent['name']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $agent['email']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full <?php echo $agent['status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $agent['status']; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo number_format($agent['sales'], 2); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium relative">
                        <button onclick="toggleActionMenu('menu-<?php echo $agent['id']; ?>')" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="menu-<?php echo $agent['id']; ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-100 z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-eye mr-2"></i>View Details</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-edit mr-2"></i>Edit Agent</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sync-alt mr-2"></i>Toggle Status</a>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash-alt mr-2"></i>Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($agents)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-14 text-center">
                        <i class="fas <?php echo $agents_error ? 'fa-triangle-exclamation text-amber-500' : 'fa-user-slash text-slate-300'; ?> text-2xl mb-3"></i>
                        <p class="text-sm font-semibold text-gray-800">
                            <?php echo $agents_error ? 'Could not reach the agents service' : 'No agents yet'; ?>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo $agents_error ? 'The backend returned an error. Check the API status and reload.' : 'Add your first sales agent to start tracking performance.'; ?>
                        </p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<!-- Add Agent Modal -->
<div data-brand="priority" id="addAgentModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">Add New Agent</h2>
        <form>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" class="w-full border border-gray-300 rounded-md px-3 py-2 mt-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="toggleModal('addAgentModal')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Agent</button>
            </div>
        </form>
    </div>
</div>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>

<!-- Admin Agents JS -->
<script src="../../../assets/js/admin/agents.js"></script>
