<?php
/* For each view, list $$vars the VIEW actually reads (code regions only), so
   unused module-supplied keys can be dropped for one naming style per view. */
$pairs = [
    'overview'   => ['dashboard'],
    'book'       => ['book'],
    'deliveries' => ['deliveries'],
    'fleet'      => ['motorcycles', 'vans'],
    'tracking'   => ['live'],
    'ratings'    => ['ratings'],
    'settings'   => ['settings'],
    'partials/delivery_row' => ['deliveries', 'dashboard'],
    'quick_actions' => ['dashboard'],
];
require_once 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/bootstrap.php';
use App\NewDash\ModuleRegistry;

foreach ($pairs as $view => $mods) {
    $code = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/' . $view . '.php');
    preg_match_all('/\$([a-z][a-zA-Z0-9_]*)/', $code, $m);
    $used = array_values(array_unique($m[1]));
    sort($used);
    $supplied = [];
    foreach ($mods as $mk) {
        $cls = ModuleRegistry::entry($mk)['class'];
        $r = new ReflectionClass($cls);
        $inst = new $cls([]);
        $v = $r->getProperty('vars'); $v->setAccessible(true);
        foreach (array_keys($v->getValue($inst)) as $k) $supplied[$k] = true;
    }
    $unused = array_diff(array_keys($supplied), $used, ['rowCompact', 'i']);
    echo str_pad($view, 24) . 'unused module keys: ' . (count($unused) ? implode(', ', $unused) : '(none)') . "\n";
    $undefined = array_diff($used, array_keys($supplied), ['d', 'badge', 'veh', 'compact', 'status_badges', 'vehicle_meta', 'row_compact', 'i', 'cnt', 'pct', 'stars', 'rv', 'sectionTitle', 'items', 'key', 'isActive', 'url']);
    echo str_pad('', 24) . 'view vars not supplied: ' . (count($undefined) ? implode(', ', $undefined) : '(none)') . "\n";
}

/* Show the module->vars array verbatim for the two mixed-case modules. */
foreach (['RatingsModule', 'TrackingModule'] as $clsFq) {
    $cls = 'App\\NewDash\\' . $clsFq;
    $inst = new $cls([]);
    $r = new ReflectionClass($cls);
    $v = $r->getProperty('vars'); $v->setAccessible(true);
    echo "\n" . $clsFq . " keys: " . implode(', ', array_keys($v->getValue($inst))) . "\n";
}
$src = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/tracking.php');
foreach (explode("\n", $src) as $i => $l) {
    if (preg_match('/\$(delivery|vehicle|milestones|summary|statusBadges)\b/', $l)) echo 'track:' . ($i + 1) . ': ' . trim($l) . "\n";
}
$src = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/ratings.php');
foreach (explode("\n", $src) as $i => $l) {
    if (preg_match('/\$(positivePct|reviewed|awaiting|statusBadges|status_badges|vehicleMeta|vehicle_meta|rating_average|rating_total)\b/', $l)) echo 'rate:' . ($i + 1) . ': ' . trim($l) . "\n";
}
$src = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/book.php');
foreach (explode("\n", $src) as $i => $l) {
    if (preg_match('/\$(fleet|selected|services|defaultService|vehicle_meta)\b/', $l)) echo 'book:' . ($i + 1) . ': ' . trim($l) . "\n";
}
$src = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/fleet.php');
foreach (explode("\n", $src) as $i => $l) {
    if (preg_match('/\$(fleet|vehicle|available|total|perks)\b/', $l)) echo 'fleet:' . ($i + 1) . ': ' . trim($l) . "\n";
}

