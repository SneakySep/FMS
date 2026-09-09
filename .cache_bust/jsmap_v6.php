<?php
$d = file('e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/New_dash/js/dashboard.js');
foreach ($d as $i => $l) {
    if (preg_match("/(readStore|writeStore)\(|= 'crm|= 'dlv|function (toast|crmToast)|Storage/i", $l)) {
        echo ($i + 1) . ': ' . trim($l) . "\n";
    }
}
