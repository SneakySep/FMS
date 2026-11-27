<?php
session_start();
require_once __DIR__ . '/../../helpers/api_helper.php';

// Siguraduhing naka-login at tamang role ang sales agent
if (!isset($_SESSION["access_token"]) || !in_array($_SESSION["role"], ['sales', 'sales_agent', 'manager', 'super_admin'])) {
    header("Location: ../../../login.php");
    exit();
}

$token = $_SESSION["access_token"];
$agent_id = $_SESSION["employee_id"] ?? '';

// Headers para sa API request patungong FastAPI
$headers = [
    'Authorization: Bearer ' . $token
];

// 1. Kunin ang mga filter at pagination parameters mula sa URL
$current_status = $_GET['status'] ?? 'all';
$search_query   = $_GET['search'] ?? '';
$service_filter = $_GET['service_type'] ?? '';
$page           = max(1, (int) ($_GET['page'] ?? 1));
$limit          = 10;
$offset         = ($page - 1) * $limit;

// 2. Buuin ang query string para sa leads endpoint
$query = http_build_query(array_filter([
    'status'       => ($current_status !== 'all') ? $current_status : null,
    'search'       => $search_query ?: null,
    'service_type' => $service_filter ?: null,
    'limit'        => $limit,
    'offset'       => $offset,
]));

// 3. Kunin ang listahan ng leads mula sa FastAPI
$leads_response = make_api_request('/api/sales/leads/?' . $query, 'GET', null, false, $headers);
$raw_leads = ($leads_response['status_code'] == 200) ? ($leads_response['data']['data'] ?? []) : [];
$page_count = ($leads_response['status_code'] == 200) ? ($leads_response['data']['count'] ?? 0) : 0;
$leads_error = ($leads_response['status_code'] != 200)
    ? ($leads_response['error'] ?? 'Could not load leads. Please try again.')
    : null;

// Itago ang closed deals sa Inquiry Leads Directory (active pipeline lang ang ipapakita)
$leads_list = array_values(array_filter($raw_leads, function ($lead) {
    $status = strtolower(trim($lead['status'] ?? ''));
    return !in_array($status, ['closed_won', 'closed_lost'], true);
}));

// 3b. Kunin ang KPI stats para sa header stat strip
$kpi_response = make_api_request('/api/sales/dashboard/kpis?agent_id=' . urlencode($agent_id), 'GET', null, false, $headers);
$kpis = ($kpi_response['status_code'] == 200) ? ($kpi_response['data']['data'] ?? []) : [];

// 4. Pagination helpers (walang total count mula sa API, kaya base sa page count)
$has_next = ($page_count === $limit);
$has_prev = ($page > 1);

// 5. Helper para sa status badge styles
function getLeadStatusBadge($status)
{
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
