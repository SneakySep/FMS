<?php
$b = 'e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/';
copy($b . 'classes/DemoData.php', 'e:/Xampp/Files/htdocs/CRM/customer_relationship/.cache_bust/demo_v6.php');
echo file_get_contents($b . 'views/_head_quickactions.php');
echo "\n=== _assemble.ps1 ===\n";
echo file_get_contents($b . 'views/_assemble.ps1');
