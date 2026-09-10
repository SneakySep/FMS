<?php
/* Verifies the segment resolution + routing matrix. Runs each case in its own
   process because resolve_customer_segment() reads a live $_SESSION. */
require "e:/Xampp/Files/htdocs/CRM/customer_relationship/frontend/src/helpers/portal_access.php";

session_start();
$_SESSION = [];

$case = $argv[1] ?? '';

/* A syntactically valid unsigned JWT: header.payload.sig */
function fake_jwt(array $payload): string
{
    $b64 = fn ($arr) => rtrim(strtr(base64_encode(json_encode($arr)), '+/', '-_'), '=');
    return $b64(['alg' => 'HS256']) . '.' . $b64($payload) . '.sig';
}

switch ($case) {
    case 'no_data':
        $_SESSION['access_token'] = fake_jwt(['role' => 'customer']);
        $_SESSION['role'] = 'customer';
        break;
    case 'jwt_claim':
        $_SESSION['access_token'] = fake_jwt(['role' => 'customer', 'customer_type' => 'individual']);
        $_SESSION['role'] = 'customer';
        break;
    case 'jwt_alias':
        $_SESSION['access_token'] = fake_jwt(['segment' => 'B2C']);
        $_SESSION['role'] = 'customer';
        break;
    case 'session_wins':
        $_SESSION['access_token'] = fake_jwt(['customer_type' => 'individual']);
        $_SESSION['role'] = 'customer';
        $_SESSION['customer_type'] = 'business';
        break;
    case 'garbage_claim':
        $_SESSION['access_token'] = fake_jwt(['customer_type' => 'weekday']);
        $_SESSION['role'] = 'customer';
        break;
    case 'not_a_jwt':
        $_SESSION['access_token'] = 'opaque-token-without-dots';
        $_SESSION['role'] = 'customer';
        break;
    default:
        echo "unknown case\n";
        exit(1);
}

$resolved = resolve_customer_segment();
echo str_pad($case, 16)
    . " resolved=" . str_pad($resolved, 11)
    . " landing=" . dashboard_for_role('customer', $resolved)
    . "\n";
