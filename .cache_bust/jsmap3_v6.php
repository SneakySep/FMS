<?php
$d = file('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/js/dashboard.js');
echo implode('', array_map(fn($i) => ($i + 1) . ': ' . $d[$i], range(44, 77)));
