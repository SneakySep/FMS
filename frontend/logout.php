<?php
/**
 * Sign-out endpoint + confirmation screen.
 *
 * Reached from the dashboard logout modal (assets/js/logout.js) and from the
 * OTP page's "use a different account" link. The confirmation prompt itself
 * lives in that modal, so this page's job is to tear the session down and
 * tell the user it worked.
 *
 * The dashboard session is the only state we own: the access token is a
 * server-side JWT held by FastAPI and there is no revocation endpoint, so it
 * stays valid upstream until it expires. Dropping it here plus regenerating
 * the id is what stops a shared or unattended machine from replaying the
 * pre-logout session.
 */
session_start();

$had_session = isset($_SESSION["access_token"]) || isset($_SESSION["temp_email"]);

/* Clear the challenge flags before the redirect so a stale `temp_email`
   cannot bounce the user back into the OTP step on their next sign-in. */
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

/* The session cookie was deleted above, so the next request that calls
   session_start() (login.php) opens a brand-new session with a new id and a
   new CSRF token. Nothing is inherited from the signed-out session. */

/* Nothing to confirm if the user was never signed in - send them straight to
   the form instead of showing a screen about a session that did not exist. */
if (!$had_session) {
    header("Location: login.php?logged_out=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
    <title>Signed out &middot; Priority Handling Logistics</title>
    <!-- No-store: pressing Back must not present a page that implies a live
         session, and the cached copy must not be reusable. -->
    <meta http-equiv="Cache-Control" content="no-store, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
</head>
<body class="auth-body">

    <div class="auth-topbar">
        <div class="auth-topbar-inner">
            <a class="auth-logo" href="login.php" aria-label="Priority Handling Logistics - home">
                <img class="auth-logo-img" src="assets/image/logo-mark.png" alt="" width="44" height="44">
                <span>
                    <span class="auth-logo-name">Priority <em>Handling</em></span>
                    <span class="auth-logo-meta">Logistics Inc.</span>
                </span>
            </a>
            <span class="auth-topbar-tag">Session ended</span>
        </div>
    </div>

    <main class="auth-main">
        <div class="auth-shell auth-shell--narrow">
            <section class="auth-panel auth-panel--center">
                <div class="auth-badge auth-badge--success" aria-hidden="true">
                    <i class="fa-solid fa-check"></i>
                </div>

                <div style="text-align:center">
                    <p class="auth-eyebrow">Session ended</p>
                    <h1 class="auth-title">You are signed out</h1>
                    <p class="auth-sub">
                        Your Priority Handling Logistics session has been closed on this device.
                        For your security, close this browser tab if you are on a shared or
                        public computer.
                    </p>
                </div>

                <div class="auth-alert auth-alert--info" role="status">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                    <span>
                        Returning to the sign-in page in
                        <span class="tabular" data-countdown="8" data-countdown-redirect="login.php?logged_out=1">8s</span>.
                    </span>
                </div>

                <a class="auth-btn auth-btn--primary" href="login.php">
                    <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>
                    Sign in again
                </a>

                <ul class="auth-tips">
                    <li>
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span>Authentication tokens were cleared from this browser session.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span>No personal or shipment data remains cached on this page.</span>
                    </li>
                </ul>
            </section>
        </div>
    </main>

    <footer class="auth-foot">
        <span>&copy; <?= date('Y') ?> Priority Handling Logistics Inc. All rights reserved.</span>
        <span class="auth-foot-links">
            <button type="button" data-legal-open="legal-privacy">Privacy Policy (RA 10173)</button>
            <button type="button" data-legal-open="legal-terms">Terms of Service</button>
        </span>
    </footer>

    <?php include_once 'src/components/legal_modals.php'; ?>

    <script src="assets/js/auth.js" defer></script>
</body>
</html>

