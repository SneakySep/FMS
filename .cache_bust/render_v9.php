<?php
/* Render every New_dash page in-process, escalating every PHP warning
   (undefined variable included) to a visible line so the contracts can be
   verified end to end. */
require_once 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/bootstrap.php';

use App\NewDash\ModuleRegistry;
use App\NewDash\PageLayout;

set_error_handler(function ($no, $str, $file, $line) {
    echo '    !! [' . $no . '] ' . $str . ' @ ' . basename($file) . ':' . $line . "\n";
    return true;
});

$layout = new PageLayout();
foreach (ModuleRegistry::all() as $key => $entry) {
    $class = $entry['class'];
    $qs = [];
    if (($q = parse_url($entry['url'], PHP_URL_QUERY)) !== null) parse_str($q, $qs);
    $m = new $class($qs);
    ob_start();
    echo "== $key ==\n";
    try {
        $layout->render($m);
        $html = ob_get_clean();
        echo '    rendered ' . number_format(strlen($html)) . " bytes\n";
        if (stripos($html, 'Warning:') !== false) echo "    !! warning text leaked into HTML\n";
    } catch (Throwable $e) {
        ob_end_clean();
        echo '    !! FATAL ' . get_class($e) . ': ' . $e->getMessage() . "\n";
    }
}
