<?php
$b = 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/views/';
echo "=== deliveries.php 1-30 ===\n";
echo implode('', array_slice(file($b . 'deliveries.php'), 0, 30));
echo "\n=== deliveries.php 75-end ===\n";
echo implode('', array_slice(file($b . 'deliveries.php'), 74));
echo "\n=== partials/delivery_row.php ===\n";
echo file_get_contents($b . 'partials/delivery_row.php');
