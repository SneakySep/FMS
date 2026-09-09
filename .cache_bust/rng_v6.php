<?php
/* Dump reference line ranges so the overview view can be carved 1:1. */
$l = file('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/_reference/dashboard_reference.txt');
$range = [(int) $argv[1], (int) $argv[2]];
for ($i = $range[0]; $i <= $range[1]; $i++) {
    echo $i . ': ' . $l[$i];
}
