<?php
require "e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/src/helpers/portal_access.php";

$_SERVER["SCRIPT_NAME"] = $argv[1];
echo "script      : " . $argv[1] . "\n";
echo "base        : " . portal_base_prefix() . "\n";
echo "login dest  : " . portal_base_prefix() . "login.php\n";
echo "b2b home    : " . portal_base_prefix() . dashboard_for_role("customer", "business") . "\n";
echo "c2b home    : " . portal_base_prefix() . dashboard_for_role("customer", "individual") . "\n";
echo "sales home  : " . portal_base_prefix() . dashboard_for_role("sales_agent") . "\n";
echo "normalize('" . $argv[2] . "') : " . var_export(normalize_customer_segment($argv[2]), true) . "\n";
echo "unknown seg : " . var_export(normalize_customer_segment("garbage"), true) . "\n";
echo "legacy 1arg : " . dashboard_for_role("customer") . "\n";
