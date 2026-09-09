<?php
/* Dump the contracts the overview view + shared row partial must satisfy. */
require_once 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/classes/bootstrap.php';

use App\NewDash\DemoData;

echo "kpis():\n";
foreach (DemoData::kpis() as $k => $v) {
    echo '  ' . str_pad($k, 16) . ' => ' . var_export($v, true) . "\n";
}
echo "\nmoving-like sample row keys:\n";
foreach (DemoData::deliveries() as $d) { echo '  ' . implode(', ', array_keys($d)) . "\n"; break; }

$row = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/partials/delivery_row.php');
preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', $row, $m);
echo "\ndelivery_row.php vars: " . implode(' ', array_values(array_unique($m[1]))) . "\n";

$side = file_get_contents('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/components/sidebar.php');
foreach (explode("\n", $side) as $i => $l) {
    if (preg_match('/activePage|navSections|\\\$current|is_active/', $l)) echo 'sidebar ' . ($i + 1) . ': ' . trim($l) . "\n";
}
