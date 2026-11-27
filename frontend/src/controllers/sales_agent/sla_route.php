<?php
session_start();
require_once __DIR__ . '/../../helpers/api_helper.php';

// Siguraduhing naka-login at tamang role ang sales agent
if (!isset($_SESSION["access_token"]) || !in_array($_SESSION["role"], ['sales', 'sales_agent', 'manager', 'super_admin'])) {
    header("Location: ../../../login.php");
    exit();
}

$token    = $_SESSION["access_token"];
$agent_id = $_SESSION["employee_id"] ?? '';

// Headers para sa API request patungong FastAPI
$headers = [
    'Authorization: Bearer ' . $token
];

$sla_error   = null;
$sla_success = null;

// 1. Ipakita ang na-flash na mensahe mula sa naunang request (PRG pattern)
if (!empty($_SESSION['sla_flash'])) {
    $sla_success = $_SESSION['sla_flash']['success'] ?? null;
    $sla_error   = $_SESSION['sla_flash']['error'] ?? null;
    unset($_SESSION['sla_flash']);
}

// 2. POST handler: gumawa ng SLA contract
// Endpoint: POST /api/sales/sla/contract (tumutugma sa @router.post("/contract") ng backend)
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'create_sla') {
    // Kunin at linisin ang mga input
    $inquiry_id    = trim($_POST['inquiry_id'] ?? '');
    $customer_id   = trim($_POST['customer_id'] ?? '');
    $template_id   = trim($_POST['template_id'] ?? '');
    $mode          = trim($_POST['mode'] ?? '');
    $origin        = trim($_POST['origin'] ?? '');
    $destination   = trim($_POST['destination'] ?? '');
    $rate_basis    = trim($_POST['rate_basis'] ?? '');
    $currency      = trim($_POST['currency'] ?? 'PHP') ?: 'PHP';
    $payment_terms = trim($_POST['payment_terms'] ?? '');
    $start_date    = trim($_POST['start_date'] ?? '');
    $end_date      = trim($_POST['end_date'] ?? '');

    // Validation ng required fields
    $missing = [];
    foreach ([
        'inquiry_id'    => $inquiry_id,
        'customer_id'   => $customer_id,
        'mode'          => $mode,
        'origin'        => $origin,
        'destination'   => $destination,
        'rate_basis'    => $rate_basis,
        'payment_terms' => $payment_terms,
        'start_date'    => $start_date,
        'end_date'      => $end_date,
    ] as $field => $value) {
        if ($value === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        $_SESSION['sla_flash']['error'] = 'Kulang ang required fields: ' . implode(', ', $missing) . '.';
    } else {
        // Buuin ang payload na tumutugma sa SLACreateRequest schema
        $payload = [
            'inquiry_id'              => $inquiry_id,
            'customer_id'             => $customer_id,
            'template_id'             => $template_id !== '' ? $template_id : null,
            'start_date'              => $start_date,
            'end_date'                => $end_date,
            'auto_renew'              => filter_var($_POST['auto_renew'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'notice_days'             => (int) ($_POST['notice_days'] ?? 30),
            'mode'                    => $mode,
            'origin'                  => $origin,
            'destination'             => $destination,
            'rate'                    => (float) ($_POST['rate'] ?? 0),
            'rate_basis'              => $rate_basis,
            'currency'                => $currency,
            'fuel_surcharge_pct'      => ($_POST['fuel_surcharge_pct'] ?? '') !== '' ? (float) $_POST['fuel_surcharge_pct'] : null,
            'payment_terms'           => $payment_terms,
            'transit_days'            => (int) ($_POST['transit_days'] ?? 0),
            'pickup_window_hours'     => (int) ($_POST['pickup_window_hours'] ?? 0),
            'ontime_target_pct'       => (float) ($_POST['ontime_target_pct'] ?? 0),
            'tracking_interval_hours' => (int) ($_POST['tracking_interval_hours'] ?? 0),
            'claims_response_hours'   => (int) ($_POST['claims_response_hours'] ?? 0),
            'late_credit_pct_per_day' => (float) ($_POST['late_credit_pct_per_day'] ?? 0),
            'credit_cap_pct'          => (float) ($_POST['credit_cap_pct'] ?? 0),
            'liability_limit_pct'     => (float) ($_POST['liability_limit_pct'] ?? 0),
        ];

        $response = make_api_request('/api/sales/sla/contract', 'POST', $payload, false, $headers);

        if (in_array($response['status_code'], [200, 201], true)) {
            $data = $response['data']['data'] ?? [];
            $_SESSION['sla_flash']['success'] = 'SLA Contract created successfully! Contract No: ' . ($data['contract_no'] ?? 'N/A');
        } else {
            $detail = $response['data']['detail'] ?? $response['error'] ?? 'Failed to create SLA contract.';
            $_SESSION['sla_flash']['error'] = is_array($detail) ? json_encode($detail) : $detail;
        }
    }

    // I-redirect para maiwasan ang form resubmission (Post/Redirect/Get)
    $redirect = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    header('Location: ' . $redirect);
    exit();
}

// 3. Kunin ang existence check kung may inquiry_id
// Endpoint: GET /api/sales/sla/check/{inquiry_id}
$sla_exists   = false;
$sla_contract = null;

$inquiry_id = trim($_GET['inquiry_id'] ?? '');
if ($inquiry_id !== '') {
    $check_response = make_api_request('/api/sales/sla/check/' . urlencode($inquiry_id), 'GET', null, false, $headers);

    if ($check_response['status_code'] == 200) {
        $sla_exists   = $check_response['data']['exists'] ?? false;
        $sla_contract = $check_response['data']['data'] ?? null;
    } else {
        $detail    = $check_response['data']['detail'] ?? $check_response['error'] ?? 'Failed to check SLA existence.';
        $sla_error = is_array($detail) ? json_encode($detail) : $detail;
    }
}
