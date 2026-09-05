<?php
/**
 * Shared flow helpers for the public auth screens (login / OTP / logout).
 *
 * These used to be copy-pasted between login.php and otp_verification.php,
 * which is how the two pages drifted apart: login redirected to relative
 * `src/views/...` paths while OTP redirected to absolute `/src/views/...`
 * paths that do not resolve under this layout, and OTP never handled the
 * `administrator` role that the JWT actually carries.
 */

/* How long a pending OTP challenge stays valid. Without a TTL, a `temp_email`
   left in the session by an abandoned login attempt would let the OTP page be
   re-posted to indefinitely. */
if (!defined('OTP_CHALLENGE_TTL')) {
    define('OTP_CHALLENGE_TTL', 600); // 10 minutes
}

/**
 * Resolve the landing dashboard for a role. Returns null for a role that has
 * no view, so callers can show an error instead of bouncing to a 404.
 */
function dashboard_for_role($role): ?string
{
    switch (strtolower((string) $role)) {
        case 'admin':
        case 'administrator':
            return 'src/views/admin/dashboard.php';
        case 'sales':
        case 'sales_agent':
            return 'src/views/sales_agent/dashboard.php';
        case 'customer':
            return 'src/views/customer/dashboard.php';
        default:
            return null;
    }
}

/**
 * Map a raw upstream failure onto something safe to render.
 *
 * The backend returns FastAPI `detail` strings, and make_api_request() returns
 * a cURL message on connection failure that leaks the internal host and port.
 * Neither belongs in the UI.
 */
function auth_error_message($raw, string $fallback): string
{
    $text = is_array($raw) ? json_encode($raw) : (string) $raw;

    if (stripos($text, 'connect') !== false || stripos($text, 'couldn') !== false) {
        return 'We could not reach the authentication service. Please try again in a moment.';
    }
    if (preg_match('/too many|rate.?limit|throt/i', $text)) {
        return 'Too many attempts. Please wait a minute before trying again.';
    }

    return $fallback;
}

/**
 * Show just enough of an address for the user to recognise which mailbox to
 * check, without printing the whole thing into the page on every render.
 */
function mask_email(string $email): string
{
    if (strpos($email, '@') === false) {
        return $email;
    }

    [$user, $domain] = explode('@', $email, 2);
    $visible = substr($user, 0, min(2, max(1, strlen($user) - 1)));
    $hidden  = max(3, strlen($user) - strlen($visible));

    return $visible . str_repeat("\u{2022}", $hidden) . '@' . $domain;
}

/**
 * Pull the role out of a JWT access token. Returns null when the token is not
 * a decodable three-part JWT.
 */
function role_from_jwt(string $token): ?string
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    $payload = json_decode(
        base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])),
        true
    );

    if (!is_array($payload) || empty($payload['role'])) {
        return null;
    }

    return strtolower((string) $payload['role']);
}
