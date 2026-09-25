<?php
// Shared loader + guard for Super_admin demo (JSON mock mode).
// Migration path: replace load_superadmin_mock() body with make_api_request()
// calls to /api/v1/superadmin/* once backend tables exist.

function superadmin_require_role(): void {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $role = strtolower($_SESSION['role'] ?? '');
    $allowed = ['super_admin', 'superadmin'];
    // DEMO MODE: allow access even without login so UI can be previewed.
    // Remove the next line to enforce strict guard in production:
    if (!isset($_SESSION['role'])) { return; }
    if (!in_array($role, $allowed, true)) {
        header('Location: ../../../login.php');
        exit();
    }
}

function load_superadmin_mock(): array {
    $path = __DIR__ . '/../data/super_admin_mock.json';
    if (!file_exists($path)) { return ['users'=>[],'restricted_accounts'=>[],'audit_events'=>[],'meta'=>[]]; }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) { return ['users'=>[],'restricted_accounts'=>[],'audit_events'=>[],'meta'=>[]]; }
    $data['users'] = $data['users'] ?? [];
    $data['restricted_accounts'] = $data['restricted_accounts'] ?? [];
    $data['audit_events'] = $data['audit_events'] ?? [];
    $data['meta'] = $data['meta'] ?? [];
    return $data;
}

function superadmin_user_map(array $users): array {
    $map = [];
    foreach ($users as $u) { $map[$u['id'] ?? ''] = $u; }
    return $map;
}

function superadmin_counts(array $data): array {
    $users = $data['users'];
    $restricted = array_values(array_filter($data['restricted_accounts'], function($r){
        return ($r['status'] ?? '') === 'restricted';
    }));
    $events = $data['audit_events'];
    $unread = array_values(array_filter($events, function($e){
        return empty($e['is_read']) && in_array($e['severity'] ?? '', ['high','critical'], true);
    }));
    $by_role = ['super_admin'=>0,'admin'=>0,'sales_agent'=>0,'customer'=>0];
    foreach ($users as $u) {
        $r = strtolower($u['role'] ?? '');
        if (isset($by_role[$r])) { $by_role[$r]++; }
    }
    usort($events, function($a,$b){ return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? ''); });
    return [
        'total_users' => count($users),
        'restricted_count' => count($restricted),
        'audit_unread' => count($unread),
        'audit_total' => count($events),
        'by_role' => $by_role,
        'recent_unread' => array_slice($unread, 0, 5),
        'recent_events' => array_slice($events, 0, 5),
        'restricted_list' => $restricted,
    ];
}

function superadmin_sev_class(string $sev): string {
    switch (strtolower($sev)) {
        case 'critical': return 'bg-rose-100 text-rose-700 border-rose-200';
        case 'high': return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'medium': return 'bg-blue-100 text-blue-700 border-blue-200';
        default: return 'bg-slate-100 text-slate-600 border-slate-200';
    }
}
