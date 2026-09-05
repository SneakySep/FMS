<?php
/**
 * Session-scoped CSRF protection for the public auth forms.
 *
 * The FastAPI backend is a pure token API and holds no session, so there is
 * no server-side CSRF concept to defer to. These forms are the only ones that
 * mutate PHP session state (login promotes a session, OTP consumes
 * `temp_email`), which makes them the exact places a cross-site POST could
 * abuse: a logged-out attacker's browser could be driven through the OTP step,
 * and a form could be force-submitted to burn or flip session flags.
 *
 * The token lives in the PHP session and is echoed as a hidden field. It is
 * deliberately NOT rotated on every render - a user with the login page open
 * in two tabs would otherwise fail the second submit - but it IS rotated by
 * session_regenerate_id() on a successful login.
 */

function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * True when the request is safe to process. POST requests must carry a token
 * matching the session; anything else is rejected.
 */
function csrf_valid(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $sent = $_POST['csrf_token'] ?? '';
    $held = $_SESSION['csrf_token'] ?? '';

    return is_string($sent)
        && $sent !== ''
        && $held !== ''
        && hash_equals($held, $sent);
}
