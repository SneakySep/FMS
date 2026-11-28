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

$sla_template_error   = null;
$sla_template_success = null;

// 1. Ipakita ang na-flash na mensahe mula sa naunang request (PRG pattern)
if (!empty($_SESSION['sla_template_flash'])) {
    $sla_template_success = $_SESSION['sla_template_flash']['success'] ?? null;
    $sla_template_error   = $_SESSION['sla_template_flash']['error'] ?? null;
    unset($_SESSION['sla_template_flash']);
}

/**
 * Helper: i-extract ang readable na error detail mula sa FastAPI response.
 */
if (!function_exists('slaTemplateErrorDetail')) {
    function slaTemplateErrorDetail($response, $fallback = 'SLA template request failed.')
    {
        $detail = $response['data']['detail'] ?? $response['error'] ?? $fallback;
        return is_array($detail) ? json_encode($detail) : $detail;
    }
}

// 2. POST handlers (PRG pattern: mag-redirect pagkatapos ng bawat action)
// Endpoints:
//   POST /api/sales/sla-templates/create
//   PUT  /api/sales/sla-templates/{template_id}
$post_action = $_POST['action'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && $post_action !== '') {

    // ---------------------------------------------------------------
    // 2a. ACTION: create_template
    // ---------------------------------------------------------------
    if ($post_action === 'create_template') {
        $name = trim($_POST['name'] ?? '');
        $mode = trim($_POST['mode'] ?? '');

        // Required fields (tumutugma sa SLATemplateCreateSchema)
        $missing = [];
        $required = [
            'name'                    => $name,
            'mode'                    => $mode,
            'transit_days'            => $_POST['transit_days'] ?? '',
            'pickup_window_hours'     => $_POST['pickup_window_hours'] ?? '',
            'ontime_target_pct'       => $_POST['ontime_target_pct'] ?? '',
            'tracking_interval_hours' => $_POST['tracking_interval_hours'] ?? '',
            'claims_response_hours'   => $_POST['claims_response_hours'] ?? '',
            'late_credit_pct_per_day' => $_POST['late_credit_pct_per_day'] ?? '',
            'credit_cap_pct'          => $_POST['credit_cap_pct'] ?? '',
            'liability_limit_pct'     => $_POST['liability_limit_pct'] ?? '',
        ];
        foreach ($required as $field => $value) {
            if (trim((string) $value) === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            $_SESSION['sla_template_flash']['error'] = 'Kulang ang required fields: ' . implode(', ', $missing) . '.';
        } else {
            $payload = [
                'name'                    => $name,
                'mode'                    => $mode,
                'transit_days'            => (int) ($_POST['transit_days'] ?? 0),
                'pickup_window_hours'     => (int) ($_POST['pickup_window_hours'] ?? 0),
                'ontime_target_pct'       => (float) ($_POST['ontime_target_pct'] ?? 0),
                'tracking_interval_hours' => (int) ($_POST['tracking_interval_hours'] ?? 0),
                'claims_response_hours'   => (int) ($_POST['claims_response_hours'] ?? 0),
                'late_credit_pct_per_day' => (float) ($_POST['late_credit_pct_per_day'] ?? 0),
                'credit_cap_pct'          => (float) ($_POST['credit_cap_pct'] ?? 0),
                'liability_limit_pct'     => (float) ($_POST['liability_limit_pct'] ?? 0),
                'is_active'               => filter_var($_POST['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ];

            $response = make_api_request('/api/sales/sla-templates/create', 'POST', $payload, false, $headers);

            if (in_array($response['status_code'], [200, 201], true)) {
                $created = $response['data']['data'] ?? [];
                $label   = $created['name'] ?? $name;
                $_SESSION['sla_template_flash']['success'] = 'SLA Template "' . $label . '" created successfully!';
            } else {
                $_SESSION['sla_template_flash']['error'] = slaTemplateErrorDetail($response, 'Failed to create SLA template.');
            }
        }

    // ---------------------------------------------------------------
    // 2b. ACTION: update_template
    // ---------------------------------------------------------------
    } elseif ($post_action === 'update_template') {
        $template_id = trim($_POST['template_id'] ?? '');

        if ($template_id === '') {
            $_SESSION['sla_template_flash']['error'] = 'Walang template_id na naipasa para sa update.';
        } else {
            // Isama lang ang mga field na aktwal na ipinasa (exclude_unset sa backend)
            $payload = [];
            $string_fields = ['name', 'mode'];
            $int_fields    = ['transit_days', 'pickup_window_hours', 'tracking_interval_hours', 'claims_response_hours'];
            $float_fields  = ['ontime_target_pct', 'late_credit_pct_per_day', 'credit_cap_pct', 'liability_limit_pct'];

            foreach ($string_fields as $field) {
                if (isset($_POST[$field]) && trim((string) $_POST[$field]) !== '') {
                    $payload[$field] = trim((string) $_POST[$field]);
                }
            }
            foreach ($int_fields as $field) {
                if (isset($_POST[$field]) && trim((string) $_POST[$field]) !== '') {
                    $payload[$field] = (int) $_POST[$field];
                }
            }
            foreach ($float_fields as $field) {
                if (isset($_POST[$field]) && trim((string) $_POST[$field]) !== '') {
                    $payload[$field] = (float) $_POST[$field];
                }
            }
            if (array_key_exists('is_active', $_POST)) {
                $payload['is_active'] = filter_var($_POST['is_active'], FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($payload)) {
                $_SESSION['sla_template_flash']['error'] = 'Walang valid na field na naipasa para sa update.';
            } else {
                $response = make_api_request('/api/sales/sla-templates/' . urlencode($template_id), 'PUT', $payload, false, $headers);

                if ($response['status_code'] == 200) {
                    $updated = $response['data']['data'] ?? [];
                    $label   = $updated['name'] ?? 'SLA Template';
                    $_SESSION['sla_template_flash']['success'] = '"' . $label . '" updated successfully!';
                } else {
                    $_SESSION['sla_template_flash']['error'] = slaTemplateErrorDetail($response, 'Failed to update SLA template.');
                }
            }
        }

    // ---------------------------------------------------------------
    // 2c. ACTION: toggle_template (soft delete / reactivate)
    //     Walang DELETE endpoint ang backend, kaya is_active ang gamit.
    // ---------------------------------------------------------------
    } elseif ($post_action === 'toggle_template') {
        $template_id = trim($_POST['template_id'] ?? '');
        $is_active   = filter_var($_POST['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($template_id === '') {
            $_SESSION['sla_template_flash']['error'] = 'Walang template_id na naipasa para sa toggle.';
        } else {
            $response = make_api_request('/api/sales/sla-templates/' . urlencode($template_id), 'PUT', ['is_active' => $is_active], false, $headers);

            if ($response['status_code'] == 200) {
                $data  = $response['data']['data'] ?? [];
                $label = $data['name'] ?? 'SLA Template';
                $_SESSION['sla_template_flash']['success'] = $is_active
                    ? '"' . $label . '" reactivated successfully!'
                    : '"' . $label . '" deactivated successfully!';
            } else {
                $_SESSION['sla_template_flash']['error'] = slaTemplateErrorDetail($response, 'Failed to update SLA template status.');
            }
        }
    }

    // I-redirect para maiwasan ang form resubmission (Post/Redirect/Get)
    $redirect = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    header('Location: ' . $redirect);
    exit();
}

// 3. Kunin ang listahan ng SLA templates (kailangan ang filters na supported ng route)
// Endpoint: GET /api/sales/sla-templates/?mode=&active_only=
$mode        = trim($_GET['mode'] ?? '');
$active_only = (($_GET['active_only'] ?? '1') !== '0');

$query = http_build_query([
    'mode'        => $mode !== '' ? $mode : null,
    'active_only' => $active_only ? 'true' : 'false',
]);

$list_response = make_api_request('/api/sales/sla-templates/?' . $query, 'GET', null, false, $headers);

$sla_templates = ($list_response['status_code'] == 200) ? ($list_response['data']['data'] ?? []) : [];

if ($list_response['status_code'] != 200) {
    $sla_template_error = $sla_template_error
        ?? slaTemplateErrorDetail($list_response, 'Could not load SLA templates. Please try again.');
}

// 4. Derived stats para sa header stat strip (kapareho ng leads.php KPI strip pattern)
$total_templates  = count($sla_templates);
$active_count     = count(array_filter($sla_templates, function ($t) {
    return !empty($t['is_active']);
}));
$inactive_count   = $total_templates - $active_count;

$mode_options = array_values(array_unique(array_filter(array_map(function ($t) {
    return trim((string) ($t['mode'] ?? ''));
}, $sla_templates))));
sort($mode_options);
$mode_count = count($mode_options);

// 5. Helper para sa status badge styles (kapareho ng getLeadStatusBadge() / getSlaStatusBadge())
if (!function_exists('getSlaTemplateBadge')) {
    function getSlaTemplateBadge($is_active)
    {
        return $is_active
            ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
            : 'bg-slate-100 text-slate-500 border-slate-200';
    }
}
