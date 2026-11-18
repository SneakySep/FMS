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

// 1. Kunin ang KPIs data
$kpi_response = make_api_request('/api/sales/dashboard/kpis?agent_id=' . urlencode($agent_id), 'GET', null, false, $headers);
$kpis = ($kpi_response['status_code'] == 200) ? ($kpi_response['data']['data'] ?? []) : [];

// 2. Kunin ang Kanban Board data
$kanban_response = make_api_request('/api/sales/kanban', 'GET', null, false, $headers);
$kanban_columns = ($kanban_response['status_code'] == 200) ? ($kanban_response['data']['data'] ?? []) : [];

?>