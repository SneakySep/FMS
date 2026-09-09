<?php
/* List every PHP variable read inside each view file, plus the keys each
   module's prepare() supplies, so the two contracts can be diffed. */
$views = glob('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/*.php');
foreach ($views as $f) {
    if (strpos(basename($f), '_') === 0) continue;
    $src = file_get_contents($f);
    preg_match_all('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $src, $m);
    $vars = array_values(array_unique($m[0]));
    sort($vars);
    echo basename($f) . " => " . implode(' ', $vars) . "\n\n";
}
echo "=== module prepare() keys ===\n";
foreach (glob('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/modules/*.php') as $f) {
    $src = file_get_contents($f);
    if (preg_match('/\$this->vars\s*=\s*\[(.*?)\n\s*\];/s', $src, $m)) {
        preg_match_all("/'([a-zA-Z_]+)'\s*=>/", $m[1], $k);
        echo basename($f) . " => " . implode(' ', $k[1]) . "\n";
    } else {
        echo basename($f) . " => (no vars array matched)\n";
    }
}
