<?php
session_start();
require_once __DIR__ . '/../../helpers/api_helper.php';

if (!isset($_SESSION["temp_email"])) {
    header("Location: login.php");
    exit();
}

$error = "";
$email = $_SESSION["temp_email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_code = trim($_POST["otp_code"] ?? '');

    if (empty($otp_code)) {
        $error = "OTP should not be empty.";
    } else {
        $payload = [
            'email' => $email,
            'otp' => $otp_code
        ];

        $response = make_api_request('/api/auth/verify-otp', 'POST', $payload, false);

        if ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
            $data = $response['data'];

            $_SESSION["access_token"]  = $data["access_token"];
            $_SESSION["token_type"]    = $data["token_type"] ?? "bearer";
            $_SESSION["email"]         = $email;

            $emp = $data["employee"] ?? [];
            $_SESSION["user_id"]       = $emp["id"] ?? null;
            $_SESSION["first_name"]    = $emp["first_name"] ?? '';
            $_SESSION["last_name"]     = $emp["last_name"] ?? '';
            $_SESSION["user_name"]     = trim(($emp["first_name"] ?? '') . ' ' . ($emp["last_name"] ?? '')) ?: 'Employee';
            $_SESSION["employee_id"]   = $emp["employee_id"] ?? '';

            $user_role = strtolower($emp["role"] ?? "");
            if (empty($user_role)) {
                $token_parts = explode('.', $data["access_token"]);
                if (count($token_parts) === 3) {
                    $jwt_payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
                    $user_role = strtolower($jwt_payload["role"] ?? "");
                }
            }
            $_SESSION["role"] = $user_role;

            unset($_SESSION["temp_email"]);

            if ($user_role === "admin" || $user_role === "administrator") {
                header("Location: src/views/admin/dashboard.php");
            } else if ($user_role === "sales_agent" || $user_role === "sales") {
                header("Location: src/views/sales_agent/dashboard.php");
            } else if ($user_role === "super_admin") {
                header("Location: src/views/super_admin/dashboard.php");
            } else {
                header("Location: login.php");
            }
            exit();
        } else {
            $err_data = $response['error'] ?? ($response['data']['detail'] ?? "Invalid OTP or Expired.");
            $error    = is_array($err_data) ? json_encode($err_data) : $err_data;
        }
    }
}


?>