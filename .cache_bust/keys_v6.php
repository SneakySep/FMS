<?php
/* Authoritative check: instantiate every module and dump the exact keys its
   prepare() produced, plus the variables each view file references. */
require_once 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/bootstrap.php';

use App\NewDash\ModuleRegistry;

foreach (ModuleRegistry::all() as $key => $entry) {
    $class = $entry['class'];
    $qs = [];
    if (($q = parse_url($entry['url'], PHP_URL_QUERY)) !== null) parse_str($q, $qs);
    $m = new $class($qs);
    $r = new ReflectionClass($m);
    $p = $r->getProperty('vars');
    $p->setAccessible(true);
    echo str_pad($key, 14) . ' vars: ' . implode(', ', array_keys($p->getValue($m))) . "\n";
}
