<?php
$l = file('e:/Xampp/Files/htdocs/CRM/customer_relationship/.cache_bust/reg_v8.php');
echo implode('', array_map(fn($i) => ($i + 1) . ': ' . $l[$i], range(39, 95)));

