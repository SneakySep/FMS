<?php
$d = file('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/js/dashboard.js');
echo implode('', array_map(fn($i) => ($i + 1) . ': ' . $d[$i], range(22, 30)));
echo "\n--- clear btn region ---\n";
echo implode('', array_map(fn($i) => ($i + 1) . ': ' . $d[$i], range(462, 480)));
echo "\n--- toast fn ---\n";
echo implode('', array_map(fn($i) => ($i + 1) . ': ' . $d[$i], range(78, 96)));
