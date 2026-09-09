<?php
/* Request every thin page entry in-process and escalate PHP warnings, so the
   real entry files themselves (not just the harness modules) get verified. */
error_reporting(E_ALL);
set_error_handler(function ($no, $str, $file, $line) {
    echo '  !! [' . $no . '] ' . $str . ' @ ' . basename($file) . ':' . $line . "\n";
    return true;
});

$pages = [
    'dashboard.php', 'book.php', 'deliveries.php', 'fleet.php',
    'fleet.php?vehicle=van', 'tracking.php?wb=WB-90408', 'tracking.php',
    'ratings.php', 'settings.php', 'nonsense.php',
];
$base = 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/';
require $base . 'classes/bootstrap.php';

/* Each real entry file is included so its own thin wrapper code runs; the
   entry files require bootstrap.php themselves, which is already loaded. */
foreach ($pages as $p) {
    [$script, $qs] = array_pad(explode('?', $p, 2), 2, '');
    $_GET = [];
    if ($qs !== '') parse_str($qs, $_GET);
    $GLOBALS['argv'] = [$base . $script];

    if (!is_file($base . $script)) continue;   // nonsense.php handled below

    $_SERVER['SCRIPT_NAME'] = '/New_dash/' . $script;
    ob_start();
    try {
        include $base . $script;
    } catch (Throwable $e) {
        ob_end_clean();
        echo str_pad($p, 26) . " => !! FATAL " . get_class($e) . ': ' . $e->getMessage() . "\n";
        continue;
    }
    $html = ob_get_clean();
    $title  = preg_match('/<title>([^<]*)<\/title>/', $html, $m) ? $m[1] : '(no title)';
    $active = preg_match('/crm-nav-item is-active[^>]*>(.*?)<\/a>/s', $html, $a)
        ? trim(preg_replace('/\s+/', ' ', strip_tags($a[1]))) : '?';
    echo str_pad($p, 26) . ' => ' . number_format(strlen($html)) . " bytes | $title | sidebar: $active\n";
    if (stripos($html, '<b>Warning</b>') !== false) echo "  !! leaked warning HTML\n";
}

/* nonsense.php does not exist on disk - the registry fallback lives inside
   renderPage(), so exercise that directly instead of a failing include. */
$_GET = [];
$_SERVER['SCRIPT_NAME'] = '/New_dash/nonsense.php';
ob_start();
try {
    App\NewDash\ModuleRegistry::renderPage($base . 'nonsense.php', []);
} catch (Throwable $e) {
    ob_end_clean();
    echo "  !! FATAL " . get_class($e) . ': ' . $e->getMessage() . "\n";
}
$html = ob_get_clean();
$title = preg_match('/<title>([^<]*)<\/title>/', $html, $m) ? $m[1] : '(no title)';
echo str_pad('nonsense.php (fallback)', 26) . ' => ' . number_format(strlen($html)) . " bytes | $title\n";

echo "done\n";
