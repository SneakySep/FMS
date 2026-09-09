<?php
/* probe_v13.php — checks the nonsense.php fallback and that ?wb= really swaps
   the rendered waybill. renderPage() is void and echoes, so capture with ob. */
error_reporting(E_ALL);
ini_set('display_errors', '1');
chdir(dirname(__DIR__) . '/frontend/New_dash');
require __DIR__ . '/../frontend/New_dash/classes/bootstrap.php';

use App\NewDash\ModuleRegistry;

$base = str_replace('\\', '/', dirname(__DIR__) . '/frontend/New_dash');
$capture = fn(string $script, array $q) => (function () use ($script, $q) {
    ob_start();
    ModuleRegistry::renderPage($script, $q);
    return (string) ob_get_clean();
})($script, $q);

/* 1. unknown script -> must fall back to a full dashboard render */
$nonsense = $capture($base . '/nonsense.php', []);
echo "nonsense bytes: " . strlen($nonsense) . PHP_EOL;
echo "nonsense has <html: " . (strpos($nonsense, '<html') !== false ? 'yes' : 'no') . PHP_EOL;
echo "nonsense title: " . (preg_match('/<title>([^<]*)<\/title>/', $nonsense, $m) ? $m[1] : '(none)') . PHP_EOL . PHP_EOL;

/* 2. wb swap */
$plain = $capture($base . '/tracking.php', []);
$alt   = $capture($base . '/tracking.php', ['wb' => 'WB-90408']);
preg_match_all('/WB-\d+/', $plain, $m1);
preg_match_all('/WB-\d+/', $alt, $m2);
echo "plain ids: " . implode(',', array_unique($m1[0])) . PHP_EOL;
echo "wb=90408 ids: " . implode(',', array_unique($m2[0])) . PHP_EOL;
echo "byte delta: " . (strlen($alt) - strlen($plain)) . PHP_EOL;
