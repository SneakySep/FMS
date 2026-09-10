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

/* Customer portal segments. Two axes are kept deliberately separate:
     role           authorisation    - customer | sales_agent | administrator
     customer_type  product surface  - business  | individual
   Do not overload `role` with the segment: that would make one customer a
   different *permission* level than another customer, when the only difference
   is which UI they are shown.

   These live here, alongside dashboard_for_role(), because the segment values
   are defined once and the constants must exist before that switch runs.
   src/helpers/portal_access.php builds the guard on top of them.

   The stored values are `business` / `individual`, NOT `b2b` / `b2c`: they read
   better in the UI ("Business account" vs "Personal account"), and b2b/b2c
   collide with the literal project and folder names used across this repo. */
if (!defined('CUSTOMER_SEGMENT_BUSINESS')) {
    define('CUSTOMER_SEGMENT_BUSINESS',   'business');
    define('CUSTOMER_SEGMENT_INDIVIDUAL', 'individual');
}

/**
 * Normalise any spelling of the segment into one of the two canonical values.
 * Returns null for anything unrecognised so callers can apply their own default
 * instead of silently landing on the wrong portal.
 */
function normalize_customer_segment(mixed $value): ?string
{
    switch (strtolower(trim((string) $value))) {
        case 'business':
        case 'corporate':
        case 'enterprise':
        case 'b2b':
            return CUSTOMER_SEGMENT_BUSINESS;
        case 'individual':
        case 'personal':
        case 'courier':
        case 'retail':
        case 'b2c':
            return CUSTOMER_SEGMENT_INDIVIDUAL;
        default:
            return null;
    }
}

/**
 * Resolve the landing dashboard for a role. Returns null for a role that has
 * no view, so callers can show an error instead of bouncing to a 404.
 *
 * $customerType only matters to the `customer` role: two different customer
 * portals exist (see src/helpers/portal_access.php). It is optional so every
 * existing single-argument call keeps working unchanged, and a customer whose
 * segment the backend has not been told yet lands on the B2B dashboard, which
 * is the live real-data surface. Pass null / '' / 'unknown' to mean "not known".
 *
 * Paths are relative to frontend/, so a caller sitting anywhere else must
 * resolve them against its own depth - portal_base_prefix() in portal_access.php
 * does exactly that for the guarded views.
 */
function dashboard_for_role($role, $customerType = null): ?string
{
    switch (strtolower((string) $role)) {
        case 'admin':
        case 'administrator':
            return 'src/views/admin/dashboard.php';
        case 'sales':
        case 'sales_agent':
            return 'src/views/sales_agent/dashboard.php';
        case 'customer':
            if (normalize_customer_segment($customerType) === CUSTOMER_SEGMENT_INDIVIDUAL) {
                return 'src/views/customer_courier/dashboard.php';
            }
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
