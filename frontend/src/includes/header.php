<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Extract Agent ID (UUID mula sa profiles/auth table)
$agentId = $_SESSION['user_id'] 
    ?? $_SESSION['id'] 
    ?? $_SESSION['user']['id'] 
    ?? $_SESSION['agent_id'] 
    ?? '';

// 2. Extract Name (Gamit ang first_name at last_name columns mo)
$firstName = $_SESSION['first_name'] ?? $_SESSION['user']['first_name'] ?? '';
$lastName  = $_SESSION['last_name']  ?? $_SESSION['user']['last_name']  ?? '';

if (!empty($firstName) && !empty($lastName)) {
    $agentName = strtoupper($firstName[0]) . '. ' . ucfirst($lastName);
} elseif (!empty($firstName)) {
    $agentName = ucfirst($firstName);
} else {
    $agentName = $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? 'M. Reyes';
}

// 3. Extract Email (Mula sa Auth Session)
$agentEmail = $_SESSION['email'] 
    ?? $_SESSION['user']['email'] 
    ?? $_SESSION['agent_email'] 
    ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'SwiftFreight - Sales Portal' ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/image/logo.png">

    <!-- Custom CSS Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <?php 
    if (file_exists(__DIR__ . '/tailwind_config.php')) {
        include_once __DIR__ . '/tailwind_config.php';
    } 
    ?>

    <!-- Optional per-page <head> assets (e.g. tracking.php Leaflet CSS) -->
    <?php if (!empty($extraHead)) { echo $extraHead; } ?>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- ApexCharts CDN (pipeline snapshot line chart) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0"></script>
</head>
<body 
    class="bg-[#f8fafc] text-slate-800 font-sans antialiased min-h-screen flex"
    data-agent-id="<?= htmlspecialchars($agentId) ?>"
    data-agent-name="<?= htmlspecialchars($agentName) ?>"
    data-agent-email="<?= htmlspecialchars($agentEmail) ?>"
>