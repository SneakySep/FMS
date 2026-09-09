<?php
/* Map the original dashboard markup (captured in _reference) into the line
   ranges the new overview.php view needs: banner + KPI row + route strip. */
$src = file('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/_reference/dashboard_reference.txt');
foreach ($src as $i => $line) {
    if (preg_match('/(ROW \d|WELCOME BANNER|<section|<\/section>|MOVING|right now|<main|<\/main|MODULE CONTENT)/i', $line)) {
        echo ($i + 1) . ': ' . trim($line) . "\n";
    }
}
