<?php
/* Spot-check: does ?wb= change the waybill shown, and does the sidebar light
   up the right row per page? */
$base = 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/';
require_once $base . 'classes/bootstrap.php';

use App\NewDash\ModuleRegistry;
use App\NewDash\PageLayout;

function page(string $script, array $get): string {
    $m = new (ModuleRegistry::all()[array_key_first(array_filter(
        ModuleRegistry::all(), fn($e) => basename((string) parse_url($e['url'], PHP_URL_PATH)) === $script
    ))]['class'])($get);
    ob_start();
    (new PageLayout())->render($m);
    return ob_get_clean();
}

foreach ([['tracking.php', ['wb' => 'WB-90408']], ['tracking.php', []]] as [$s, $g]) {
    $h = page($s, $g);
    preg_match_all('/WB-\d+/', $h, $m);
    echo $s . '?' . http_build_query($g) . ' first waybills: ' . implode(',', array_slice(array_unique($m[0]), 0, 3)) . "\n";
}

foreach (['dashboard.php' => [], 'settings.php' => [], 'deliveries.php' => []] as $s => $g) {
    $h = page($s, $g);
    preg_match('/<body[^>]*data-module="([^"]+)"/', $h, $mm);
    echo $s . ' data-module=' . ($mm[1] ?? '?') . "\n";
}
