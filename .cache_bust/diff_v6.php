<?php
/* Diff: variables each view references vs the keys its module supplies. */
require_once 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/bootstrap.php';

use App\NewDash\ModuleRegistry;

$viewDir = 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/';

foreach (ModuleRegistry::all() as $key => $entry) {
    $class = $entry['class'];
    $qs = [];
    if (($q = parse_url($entry['url'], PHP_URL_QUERY)) !== null) parse_str($q, $qs);
    $m = new $class($qs);
    $r = new ReflectionClass($m);
    $tp = $r->getProperty('template'); $tp->setAccessible(true);
    $template = $tp->getValue($m);
    $vp = $r->getProperty('vars'); $vp->setAccessible(true);
    $supplied = array_keys($vp->getValue($m));

    $file = $viewDir . $template . '.php';
    if (!is_file($file)) { echo str_pad($key, 14) . " => MISSING VIEW '$template'\n\n"; continue; }

    $src = file_get_contents($file);
    /* strip locally-assigned vars ($f_pct etc) so only reads remain */
    preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=/', $src, $asg);
    $local = array_unique($asg[1]);
    preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', $src, $all);
    $used = array_unique($all[1]);
    $loopish = ['i', 'd', 'f', 'v', 'm', 'rv', 'ms', 'cnt', 'pct', 'stars', 'key', 'entry', 'row'];
    $missing = [];
    foreach ($used as $u) {
        if (in_array($u, $supplied, true)) continue;
        if (in_array($u, $local, true)) continue;
        if (in_array($u, $loopish, true)) continue;
        if (strlen($u) <= 2) continue;
        $missing[] = $u;
    }
    echo str_pad($key, 14) . " view '$template' : " . (count($missing) ? 'UNSUPPLIED: ' . implode(', ', $missing) : 'ok') . "\n";
    echo '    supplied: ' . implode(', ', $supplied) . "\n";
}
