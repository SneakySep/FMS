<?php
/* Render harness: boots each module through PageLayout and reports every
   PHP warning/notice (undefined view variables etc). Not part of the demo. */
error_reporting(E_ALL);
ini_set('display_errors', '1');

$_SERVER['SCRIPT_NAME'] = '/New_dash/dashboard.php';

require_once 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/bootstrap.php';

use App\NewDash\ModuleRegistry;
use App\NewDash\PageLayout;

set_error_handler(function ($no, $str) {
    echo "!! [$no] $str\n";
    return true;
});

$layout = new PageLayout();
foreach (ModuleRegistry::all() as $key => $entry) {
    $class = $entry['class'];
    $qs = [];
    $url = $entry['url'];
    if (($q = parse_url($url, PHP_URL_QUERY)) !== null) {
        parse_str($q, $qs);
    }
    echo "===== $key (" . basename($url) . ") =====\n";
    $module = new $class($qs);
    $html = $module->render();
    echo 'render length: ' . strlen($html) . "\n";
    if (strpos($html, 'missing view') !== false) {
        echo "!! MISSING TEMPLATE: " . $html . "\n";
    }
}
echo "DONE\n";
