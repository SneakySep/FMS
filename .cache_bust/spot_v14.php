<?php
/* spot_v14.php — per-page sidebar highlight + fleet query variants. */
error_reporting(E_ALL);
set_error_handler(function ($no, $str, $file, $line) {
    echo '  !! [' . $no . '] ' . $str . ' @ ' . basename($file) . ':' . $line . "\n";
    return true;
});
chdir('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash');
require 'classes/bootstrap.php';

use App\NewDash\ModuleRegistry;

$cases = [
    ['dashboard.php',  []],
    ['book.php',       []],
    ['book.php',       ['vehicle' => 'van']],
    ['deliveries.php', []],
    ['fleet.php',      []],
    ['fleet.php',      ['vehicle' => 'van']],
    ['fleet.php',      ['vehicle' => 'motorcycle']],
    ['tracking.php',   []],
    ['ratings.php',    []],
    ['settings.php',   []],
];

/* PageLayout includes the sidebar with include_once, so a second render inside
   the same process would skip it - real requests are one render per process, so
   when invoked with an index argument, render only that case and exit. */
if (isset($argv[1])) {
    [$script, $q] = $cases[(int) $argv[1]];
    ob_start();
    ModuleRegistry::renderPage(__DIR__ . '/' . $script, $q);
    $html = ob_get_clean();
    preg_match('/crm-nav-item is-active[^>]*>(.*?)<\/a>/s', $html, $m);
    echo preg_replace('/\s+/', ' ', trim(strip_tags($m[1] ?? 'NONE')))
        . ' | bytes: ' . number_format(strlen($html));
    exit;
}

foreach ($cases as $i => [$script, $q]) {
    $qs   = $q ? '?' . http_build_query($q) : '';
    $out  = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . $i . ' 2>&1');
    echo str_pad($script . $qs, 30) . ' sidebar: ' . trim((string) $out) . "\n";
}
echo "done\n";
