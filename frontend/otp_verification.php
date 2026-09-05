<?php
session_start();
require_once 'src/helpers/api_helper.php';
require_once 'src/helpers/csrf.php';
require_once 'src/helpers/auth_flow.php';

/* --------------------------------------------------------------------------
   The OTP challenge lives in `temp_email`. It must expire: without a TTL an
   abandoned login left the value in the session indefinitely, so the page
   stayed reachable and postable long after the code had rolled over.
   -------------------------------------------------------------------------- */
$challenge_age = isset($_SESSION["temp_email_sent"])
    ? time() - (int) $_SESSION["temp_email_sent"]
    : PHP_INT_MAX;

if (!isset($_SESSION["temp_email"]) || $challenge_age > OTP_CHALLENGE_TTL) {
    unset($_SESSION["temp_email"], $_SESSION["temp_email_sent"], $_SESSION["otp_attempts"]);
    header("Location: login.php");
    exit();
}

define('OTP_MAX_ATTEMPTS', 5);

$email        = $_SESSION["temp_email"];
$error        = "";
$errors       = [];
$email_masked = mask_email($email);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!csrf_valid()) {
        $error = "Your session expired. Please sign in again.";
        unset($_SESSION["temp_email"], $_SESSION["temp_email_sent"], $_SESSION["otp_attempts"]);
    } else {
        $attempts = (int) ($_SESSION["otp_attempts"] ?? 0);

        if ($attempts >= OTP_MAX_ATTEMPTS) {
            $error = "Too many incorrect codes. Please sign in again to request a new one.";
            unset($_SESSION["temp_email"], $_SESSION["temp_email_sent"], $_SESSION["otp_attempts"]);
        } else {
            /* Read the hidden master field; the six visible boxes are mirrored
               into it by auth.js. Non-digits are stripped so a pasted code with
               spaces or dashes still works. */
            $otp_code = preg_replace('/\D+/', '', (string) ($_POST["otp_code"] ?? ''));

            if ($otp_code === '') {
                $errors['otp_code'] = "Enter the 6-digit code from your email.";
            } elseif (strlen($otp_code) !== 6) {
                $errors['otp_code'] = "The code must be exactly 6 digits.";
            } else {
                $_SESSION["otp_attempts"] = $attempts + 1;

                // 1. Build query string para sa FastAPI router
                $query_string = http_build_query([
                    'email'    => $email,
                    'otp_code' => $otp_code,
                ]);

                $response = make_api_request('/api/auth/verify-otp?' . $query_string, 'POST', null, false);

                if ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
                    $data  = $response['data'];
                    $token = $data['access_token'];

                    /* Privilege escalation guard: this is the point where the
                       session becomes authenticated, so the pre-login id is
                       discarded first. */
                    session_regenerate_id(true);

                    $_SESSION["access_token"]  = $token;
                    $_SESSION["refresh_token"] = $data["refresh_token"] ?? null;
                    $_SESSION["user_id"]       = $data["user_id"] ?? null;
                    $_SESSION["email"]         = $email;
                    $_SESSION["role"]          = !empty($data["role"])
                        ? strtolower($data["role"])
                        : (role_from_jwt($token) ?? 'customer');

                    // 2. Fetch Profile sa Supabase gamit ang Header
                    $userId = $_SESSION["user_id"];

                    if ($userId) {
                        $profile_res = make_api_request(
                            '/api/v1/portal/profile',
                            'GET',
                            null,
                            false,
                            ['x-user-id: ' . $userId]
                        );

                        if ($profile_res['status_code'] == 200 && !empty($profile_res['data'])) {
                            $profile = $profile_res['data'];

                            $_SESSION['first_name'] = $profile['first_name'] ?? '';
                            $_SESSION['last_name']  = $profile['last_name'] ?? '';
                            $_SESSION['user_name']  = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) ?: 'Sales Agent';
                            $_SESSION['agent_id']   = $profile['agent_id'] ?? $profile['id'] ?? 'SA-014';

                            // The profile record is the more authoritative role source.
                            if (!empty($profile['role'])) {
                                $_SESSION['role'] = strtolower($profile['role']);
                            }
                        }
                    }

                    // 3. Linisin ang temporary session email
                    unset($_SESSION["temp_email"], $_SESSION["temp_email_sent"], $_SESSION["otp_attempts"]);

                    // 4. Dynamic Redirect
                    $target = dashboard_for_role($_SESSION["role"]);
                    if ($target === null) {
                        $error = "Your account isn't authorised for this portal. Please contact support.";
                    } else {
                        header("Location: $target");
                        exit();
                    }
                } else {
                    /* Never echo the upstream string: the backend leaks raw
                       SQL/DB detail on failure, which has no business being in
                       the page. auth_error_message() maps it to a safe line. */
                    $error = auth_error_message(
                        $response['error'] ?? $response['data']['detail'] ?? null,
                        'That code is incorrect or has expired. Check your email and try again.'
                    );
                }
            }
        }
    }
}

$attempts_used = (int) ($_SESSION["otp_attempts"] ?? 0);
$attempts_left = max(0, OTP_MAX_ATTEMPTS - $attempts_used);
$challenge_ttl = max(1, (int) ceil(OTP_CHALLENGE_TTL - $challenge_age));
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
    <title>Security verification &middot; Priority Handling Logistics</title>
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
            <span class="auth-topbar-tag">Step 2 of 2</span>
        </div>
    </div>

    <main class="auth-main">
        <div class="auth-shell">

            <!-- ==================== BRAND PANEL ==================== -->
            <aside class="auth-brand">
                <div>
                    <a class="auth-logo" href="login.php" aria-label="Priority Handling Logistics - home">
                        <img class="auth-logo-img auth-logo-img--on-navy"
                             src="assets/image/logo-mark.png" alt="" width="44" height="44">
                        <span>
                            <span class="auth-logo-name">Priority <em>Handling</em></span>
                            <span class="auth-logo-meta">Logistics Inc. &middot; Since 2005</span>
                        </span>
                    </a>

                    <p class="auth-brand-eyebrow auth-brand-eyebrow--spaced">Two-factor authentication</p>
                    <h1 class="auth-brand-title">One more step<br>to keep your account safe.</h1>
                    <p class="auth-brand-sub">
                        We sent a six-digit code to your registered email address. It confirms
                        it is really you signing in to the logistics console.
                    </p>

                    <ul class="auth-features">
                        <li class="auth-feature">
                            <span class="auth-feature-ico" aria-hidden="true"><i class="fa-solid fa-clock"></i></span>
                            <span class="auth-feature-txt">
                                <strong>Codes expire after 10 minutes</strong>
                                <span>Request a fresh one from the sign-in page if this one lapses.</span>
                            </span>
                        </li>
                        <li class="auth-feature">
                            <span class="auth-feature-ico" aria-hidden="true"><i class="fa-solid fa-user-shield"></i></span>
                            <span class="auth-feature-txt">
                                <strong>Never share a code</strong>
                                <span>Priority staff will not ask you for it, by phone or email.</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="auth-brand-foot">
                    <a href="tel:+6328437484"><i class="fa-solid fa-phone" aria-hidden="true"></i> (632) 843-7484</a>
                    <a href="mailto:cs@priority-ph.com"><i class="fa-solid fa-envelope" aria-hidden="true"></i> cs@priority-ph.com</a>
                    <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Makati City, Philippines</span>
                </div>
            </aside>


            <!-- ==================== FORM PANEL ==================== -->
            <section class="auth-panel">
                <div class="auth-panel-head">
                    <img class="auth-logo-img" src="assets/image/logo-mark.png" alt="" width="44" height="44">
                    <div>
                        <p class="auth-logo-name">Priority <em>Handling</em></p>
                        <p class="auth-logo-meta">Security Verification</p>
                    </div>
                </div>

                <div>
                    <p class="auth-eyebrow">Step 2 of 2</p>
                    <h2 class="auth-title">Enter your verification code</h2>
                    <p class="auth-sub">
                        We sent a 6-digit code to
                        <strong style="color:var(--fg-heading);font-weight:600"><?= htmlspecialchars($email_masked) ?></strong>.
                        Check your inbox and spam folder.
                    </p>
                </div>

                <?php if ($error !== ""): ?>
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="otp_verification.php" class="auth-form" data-auth-form>
                    <?= csrf_field() ?>

                    <div class="auth-field">
                        <span class="auth-label" id="otp-label">Verification code</span>

                        <div class="auth-otp<?= $errors ? ' is-invalid' : '' ?>"
                             data-otp data-otp-master="otp_code"
                             role="group" aria-labelledby="otp-label"
                             aria-describedby="otp-help">

                            <!-- The field that actually posts, and the one SMS /
                                 email autofill targets. Kept in the DOM but
                                 visually hidden; auth.js fans its value out to
                                 the six boxes below. -->
                            <input class="auth-otp-master"
                                   type="text"
                                   id="otp_code"
                                   name="otp_code"
                                   value="<?= htmlspecialchars($_POST['otp_code'] ?? '') ?>"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="6"
                                   autocomplete="one-time-code"
                                   aria-label="6-digit verification code">

                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <input class="auth-otp-box"
                                       type="text"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       maxlength="1"
                                       autocomplete="off"
                                       aria-label="Digit <?= $i ?> of 6">
                            <?php endfor; ?>
                        </div>

                        <?php if (!empty($errors['otp_code'])): ?>
                            <p class="auth-hint" style="color:var(--danger)"><?= htmlspecialchars($errors['otp_code']) ?></p>
                        <?php else: ?>
                            <p class="auth-hint" id="otp-help">
                                Paste the full code at once, or type it digit by digit.
                            </p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="auth-btn auth-btn--primary" data-auth-submit>
                        <i class="fa-solid fa-circle-notch fa-spin auth-btn-spin" aria-hidden="true"></i>
                        <span class="auth-btn-idle">
                            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                            Verify and sign in
                        </span>
                    </button>
                </form>

                <div class="auth-meta">
                    <a class="auth-link" href="logout.php">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Use a different account
                    </a>
                    <span>
                        <?php if ($attempts_used > 0 && $attempts_left > 0): ?>
                            <span class="tabular"><?= $attempts_left ?></span> attempt<?= $attempts_left === 1 ? '' : 's' ?> remaining
                        <?php elseif ($attempts_left > 0): ?>
                            Code expires in
                            <span class="tabular" data-countdown="<?= $challenge_ttl ?>" data-countdown-format="clock">10:00</span>
                        <?php endif; ?>
                    </span>
                </div>
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

