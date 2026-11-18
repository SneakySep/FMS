<?php
session_start();
require_once __DIR__ . '/../../helpers/api_helper.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and Password should not be empty.";
    } else {
        $login_payload = [
            'email' => $email,
            'password' => $password
        ];

        $response = make_api_request('/api/auth/login', 'POST', $login_payload, false);

        // Kapag na-trigger ang 403 Forbidden (Lockout) mula sa backend
        if ($response['status_code'] == 403) {
            $_SESSION["lockout_email"] = $email;
            header("Location: account_locked.php");
            exit();
        }

        if ($response['status_code'] == 200 && isset($response['data']['requires_otp']) && $response['data']['requires_otp'] === true) {
            $_SESSION["temp_email"] = $email;
            header("Location: otp_verification.php");
            exit();
        } 
        elseif ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
            $token = $response['data']['access_token'];
            $_SESSION["access_token"] = $token;

            $token_parts = explode('.', $token);
            if (count($token_parts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
                $_SESSION["email"] = $payload["email"] ?? $email;
                $_SESSION["role"] = strtolower($payload["role"] ?? "");
            }

            $role = $_SESSION["role"] ?? "";

            if ($role === 'sales' || $role === 'sales_agent') {
                header("Location: src/views/sales_agent/dashboard.php");
            } else if ($role === 'admin' || $role === 'administrator') {
                header("Location: src/views/admin/dashboard.php");
            } else if ($role === 'super_admin') {
                header("Location: src/views/super_admin/dashboard.php");
            } else {
                $error = "Unauthorized role. Please contact support.";
            }
            exit();
        } 
        else {
            if (isset($response['data']['detail'])) {
                $detail = $response['data']['detail'];
                if (is_array($detail)) {
                    $error = $detail[0]['msg'] ?? "Invalid email or password.";
                } else {
                    $error = $detail;
                    // Kung sakaling ang detail message ay naglalaman ng locked out notice
                    if (stripos($detail, "locked") !== false) {
                        $_SESSION["lockout_email"] = $email;
                        header("Location: account_locked.php");
                        exit();
                    }
                }
            } elseif (isset($response['error'])) {
                $error = $response['error'];
            } else {
                $error = "Invalid email or password.";
            }
        }
    }
}

?>