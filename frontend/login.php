<?php
session_start();
require_once 'src/helpers/api_helper.php';
require_once 'src/helpers/csrf.php';
require_once 'src/helpers/auth_flow.php';

$error      = "";
$logged_out = isset($_GET['logged_out']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!csrf_valid()) {
        $error = "Your session expired. Please try again.";
    } else {
        $email = trim($_POST["email"] ?? '');

        /* Password is intentionally NOT trimmed. Leading/trailing spaces are
           valid characters in a passphrase, and trimming silently rejected
           anyone whose password actually has them. */
        $password = $_POST["password"] ?? '';

        if ($email === '' || $password === '') {
            $error = "Enter both your email and password.";
        } else {
            // 1. I-post sa tamang FastAPI router endpoint gamit ang JSON false
            $response = make_api_request('/api/auth/login', 'POST', [
                'email'    => $email,
                'password' => $password,
            ], false);

            // 2. Case A: Kung OTP flow ang setup (Step 1 Complete)
            if ($response['status_code'] == 200
                && isset($response['data']['status'])
                && $response['data']['status'] === 'otp_sent') {

                $_SESSION["temp_email"]      = $email;
                $_SESSION["temp_email_sent"] = time();
                header("Location: otp_verification.php");
                exit();
            }
            // 3. Case B: Direct Token Response Kung walang OTP
            elseif ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
                $token = $response['data']['access_token'];

                /* Privilege escalation guard: the session is about to be
                   promoted with values taken from the token, so regenerate the
                   id first to stop session fixation carrying a pre-login id
                   forward. */
                session_regenerate_id(true);

                $_SESSION["access_token"]  = $token;
                $_SESSION["refresh_token"] = $response['data']['refresh_token'] ?? null;
                $_SESSION["user_id"]       = $response['data']['user_id'] ?? null;
                $_SESSION["email"]         = $email;
                $_SESSION["role"]          = !empty($response['data']['role'])
                    ? strtolower($response['data']['role'])
                    : (role_from_jwt($token) ?? 'customer');

                unset($_SESSION["temp_email"], $_SESSION["temp_email_sent"]);

                $target = dashboard_for_role($_SESSION["role"]);
                if ($target === null) {
                    $error = "Your account isn't authorised for this portal. Please contact support.";
                } else {
                    header("Location: $target");
                    exit();
                }
            }
            // 4. Handling ng Error
            else {
                $error = auth_error_message(
                    $response['error'] ?? $response['data']['detail'] ?? null,
                    'Incorrect email or password. Check your credentials and try again.'
                );
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
    <title>Sign in &middot; Priority Handling Logistics</title>
</head>
<body class="auth-body">

    <!-- Compact brand bar: carries identity on mobile, where the navy panel
         is hidden. Hidden from lg upward. -->
    <div class="auth-topbar">
        <div class="auth-topbar-inner">
            <a class="auth-logo" href="login.php" aria-label="Priority Handling Logistics - home">
                <img class="auth-logo-img" src="assets/image/logo-mark.png" alt="" width="44" height="44">
                <span>
                    <span class="auth-logo-name">Priority <em>Handling</em></span>
                    <span class="auth-logo-meta">Logistics Inc.</span>
                </span>
            </a>
            <span class="auth-topbar-tag">Secure Portal</span>
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

                    <p class="auth-brand-eyebrow auth-brand-eyebrow--spaced">
                        Freight &amp; Courier Console
                    </p>
                    <h1 class="auth-brand-title">Every shipment,<br>tracked to the door.</h1>
                    <p class="auth-brand-sub">
                        Sign in to manage bookings, follow consignments in real time, and handle
                        customs documentation across our domestic and international network.
                    </p>

                    <ul class="auth-features">
                        <li class="auth-feature">
                            <span class="auth-feature-ico" aria-hidden="true"><i class="fa-solid fa-route"></i></span>
                            <span class="auth-feature-txt">
                                <strong>Live consignment tracking</strong>
                                <span>Stage-by-stage status from pickup to proof of delivery.</span>
                            </span>
                        </li>
                        <li class="auth-feature">
                            <span class="auth-feature-ico" aria-hidden="true"><i class="fa-solid fa-file-invoice"></i></span>
                            <span class="auth-feature-txt">
                                <strong>Bookings and documentation</strong>
                                <span>Generate airway bills and customs paperwork in one place.</span>
                            </span>
                        </li>
                        <li class="auth-feature">
                            <span class="auth-feature-ico" aria-hidden="true"><i class="fa-solid fa-headset"></i></span>
                            <span class="auth-feature-txt">
                                <strong>Support around the clock</strong>
                                <span>Our operations desk is staffed 24/7 in Makati City.</span>
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
                        <p class="auth-logo-meta">Agent &amp; Customer Portal</p>
                    </div>
                </div>

                <div>
                    <p class="auth-eyebrow">Sign in</p>
                    <h2 class="auth-title">Welcome back</h2>
                    <p class="auth-sub">Use your portal credentials to continue.</p>
                </div>

                <?php if ($logged_out): ?>
                    <div class="auth-alert auth-alert--success" style="margin-top:1.25rem" role="status">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span>You have been signed out securely.</span>
                    </div>
                <?php endif; ?>

                <?php if ($error !== ""): ?>
                    <div class="auth-alert auth-alert--error" style="margin-top:1.25rem" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="auth-form" data-auth-form>
                    <?= csrf_field() ?>

                    <div class="auth-field">
                        <label class="auth-label" for="email">Email address</label>
                        <div class="auth-input-wrap">
                            <input class="auth-input"
                                   type="email"
                                   id="email"
                                   name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="name@priority-ph.com"
                                   autocomplete="username"
                                   inputmode="email"
                                   spellcheck="false"
                                   required>
                            <span class="auth-input-ico" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                    </div>

                    <div class="auth-field">
                        <div class="auth-label-row">
                            <label class="auth-label" for="password">Password</label>
                        </div>
                        <div class="auth-input-wrap">
                            <input class="auth-input"
                                   type="password"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   autocomplete="current-password"
                                   required>
                            <span class="auth-input-ico" aria-hidden="true"><i class="fa-solid fa-key"></i></span>
                            <button type="button"
                                    class="auth-reveal"
                                    data-reveal-toggle="password"
                                    aria-controls="password"
                                    aria-pressed="false"
                                    aria-label="Show password">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn auth-btn--primary" data-auth-submit>
                        <i class="fa-solid fa-circle-notch fa-spin auth-btn-spin" aria-hidden="true"></i>
                        <span class="auth-btn-idle">
                            <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>
                            Sign in
                        </span>
                    </button>

                    <p class="auth-hint">
                        Trouble signing in? Contact
                        <a class="auth-link" href="mailto:cs@priority-ph.com">cs@priority-ph.com</a>.
                    </p>
                </form>
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

