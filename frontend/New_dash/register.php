<?php
/* ==========================================================================
   REGISTER  —  New_dash
   --------------------------------------------------------------------------
   Visual clone of frontend/login.php. Intentionally NOT connected to the
   backend API: there is no session_start(), no CSRF helper and no
   make_api_request() call anywhere in this file. Submitting the form runs
   local validation only and re-renders the page.

   Field names match the FastAPI schema that will eventually back this form
   (backend-api/app/schemas/auth.py -> UserRegister: email, password,
   first_name, last_name), so wiring it up later is a single POST call plus
   the redirect. See the "BACKEND WIRING" note above the submit handler.
   ========================================================================== */

$error   = "";
$success = "";
$errors  = [];

/* Remember what was typed so a failed submit does not wipe the form.
   Passwords are deliberately NOT echoed back - retype is cheap, leaking a
   secret into the HTML is not. */
$old = [
    'first_name'       => '',
    'last_name'        => '',
    'email'            => '',
    'password'         => '',
    'password_confirm' => '',
    'terms'            => false,
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $old['first_name']       = trim($_POST['first_name'] ?? '');
    $old['last_name']        = trim($_POST['last_name'] ?? '');
    $old['email']            = trim($_POST['email'] ?? '');
    $old['password']         = (string) ($_POST['password'] ?? '');
    $old['password_confirm'] = (string) ($_POST['password_confirm'] ?? '');
    $old['terms']            = isset($_POST['terms']);

    /* Password is intentionally NOT trimmed. Leading/trailing spaces are
       valid characters in a passphrase, and trimming silently rejected
       anyone whose password actually has them. */

    if ($old['first_name'] === '') {
        $errors['first_name'] = "Enter your first name.";
    } elseif (mb_strlen($old['first_name']) > 100) {
        $errors['first_name'] = "Keep your first name under 100 characters.";
    }

    if ($old['last_name'] === '') {
        $errors['last_name'] = "Enter your last name.";
    } elseif (mb_strlen($old['last_name']) > 100) {
        $errors['last_name'] = "Keep your last name under 100 characters.";
    }

    if ($old['email'] === '') {
        $errors['email'] = "Enter your work email address.";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "That does not look like a valid email address.";
    }

    /* min 8 mirrors the backend rule (Field(..., min_length=8)); keeping the
       same floor here means a form that passes the browser will not fail the
       API once it is connected. */
    if ($old['password'] === '') {
        $errors['password'] = "Choose a password.";
    } elseif (strlen($old['password']) < 8) {
        $errors['password'] = "Use at least 8 characters.";
    }

    if ($old['password_confirm'] === '') {
        $errors['password_confirm'] = "Re-enter your password to confirm it.";
    } elseif ($old['password_confirm'] !== $old['password']) {
        $errors['password_confirm'] = "The two passwords do not match.";
    }

    if (!$old['terms']) {
        $errors['terms'] = "Accept the Privacy Policy and Terms of Service to continue.";
    }

    /* ----------------------------------------------------------------------
       BACKEND WIRING  (not implemented on purpose)

       When this screen is ready to go live, replace the block below with:

           $response = make_api_request('/api/auth/signup', 'POST', [
               'email'      => $old['email'],
               'password'   => $old['password'],
               'first_name' => $old['first_name'],
               'last_name'  => $old['last_name'],
           ], false);

           if ($response['status_code'] === 201) { ... header('Location: ../login.php'); }
           else { $error = $response['error'] ?? $response['data']['detail'] ?? '...'; }

       That also requires src/helpers/api_helper.php, and - because a signup
       mutates no session state but a CSRF token does - src/helpers/csrf.php
       plus a csrf_field() hidden input rendered inside the form.
       ---------------------------------------------------------------------- */
    if (empty($errors)) {
        $success = "Your details check out. Account creation is not connected to the "
                 . "backend yet, so nothing was saved - wire the signup endpoint to "
                 . "finish this flow.";
        // Clear the secrets from the rendered page now that they validated.
        $old['password']         = '';
        $old['password_confirm'] = '';
    } else {
        $error = "Check the highlighted fields and try again.";
    }
}

/**
 * Escape a value for safe output inside an HTML attribute.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account &middot; Priority Handling Logistics</title>

    <!-- Google Fonts & FontAwesome - same pair src/components/head.php loads,
         inlined here so New_dash/ stays independent of frontend/src. -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Self-contained auth layer: design tokens + split-panel rules. -->
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-body">

    <!-- Compact brand bar: carries identity on mobile, where the navy panel
         is hidden. Hidden from lg upward. -->
    <div class="auth-topbar">
        <div class="auth-topbar-inner">
            <a class="auth-logo" href="../login.php" aria-label="Priority Handling Logistics - home">
                <img class="auth-logo-img" src="image/logo-mark.png" alt="" width="44" height="44">
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
                    <a class="auth-logo" href="../login.php" aria-label="Priority Handling Logistics - home">
                        <img class="auth-logo-img auth-logo-img--on-navy"
                             src="image/logo-mark.png" alt="" width="44" height="44">
                        <span>
                            <span class="auth-logo-name">Priority <em>Handling</em></span>
                            <span class="auth-logo-meta">Logistics Inc. &middot; Since 2005</span>
                        </span>
                    </a>

                    <p class="auth-brand-eyebrow auth-brand-eyebrow--spaced">
                        Freight &amp; Courier Console
                    </p>
                    <h1 class="auth-brand-title">Start shipping<br>in minutes.</h1>
                    <p class="auth-brand-sub">
                        Create your portal account to book consignments, follow shipments in real
                        time, and keep customs documentation in one place across our domestic and
                        international network.
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
                    <img class="auth-logo-img" src="image/logo-mark.png" alt="" width="44" height="44">
                    <div>
                        <p class="auth-logo-name">Priority <em>Handling</em></p>
                        <p class="auth-logo-meta">Agent &amp; Customer Portal</p>
                    </div>
                </div>

                <div>
                    <p class="auth-eyebrow">Create account</p>
                    <h2 class="auth-title">Get started</h2>
                    <p class="auth-sub">Tell us who you are to open your portal access.</p>
                </div>

                <?php if ($success !== ""): ?>
                    <div class="auth-alert auth-alert--success" style="margin-top:1.25rem" role="status">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span><?= e($success) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($error !== ""): ?>
                    <div class="auth-alert auth-alert--error" style="margin-top:1.25rem" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <span><?= e($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" class="auth-form" data-auth-form novalidate>
                    <div class="auth-grid-2">
                        <div class="auth-field">
                            <label class="auth-label" for="first_name">First name</label>
                            <div class="auth-input-wrap">
                                <input class="auth-input"
                                       type="text"
                                       id="first_name"
                                       name="first_name"
                                       value="<?= e($old['first_name']) ?>"
                                       placeholder="Juan"
                                       autocomplete="given-name"
                                       maxlength="100"
                                       required>
                                <span class="auth-input-ico" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
                            </div>
                            <?php if (!empty($errors['first_name'])): ?>
                                <p class="auth-hint auth-hint--error"><?= e($errors['first_name']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="auth-field">
                            <label class="auth-label" for="last_name">Last name</label>
                            <div class="auth-input-wrap">
                                <input class="auth-input"
                                       type="text"
                                       id="last_name"
                                       name="last_name"
                                       value="<?= e($old['last_name']) ?>"
                                       placeholder="Dela Cruz"
                                       autocomplete="family-name"
                                       maxlength="100"
                                       required>
                                <span class="auth-input-ico" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
                            </div>
                            <?php if (!empty($errors['last_name'])): ?>
                                <p class="auth-hint auth-hint--error"><?= e($errors['last_name']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="email">Email address</label>
                        <div class="auth-input-wrap">
                            <input class="auth-input"
                                   type="email"
                                   id="email"
                                   name="email"
                                   value="<?= e($old['email']) ?>"
                                   placeholder="name@priority-ph.com"
                                   autocomplete="username"
                                   inputmode="email"
                                   spellcheck="false"
                                   required>
                            <span class="auth-input-ico" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <?php if (!empty($errors['email'])): ?>
                            <p class="auth-hint auth-hint--error"><?= e($errors['email']) ?></p>
                        <?php else: ?>
                            <p class="auth-hint">We send booking updates and a verification link here.</p>
                        <?php endif; ?>
                    </div>


                    <div class="auth-field">
                        <div class="auth-label-row">
                            <label class="auth-label" for="password">Password</label>
                            <span class="auth-hint">8 characters minimum</span>
                        </div>
                        <div class="auth-input-wrap">
                            <input class="auth-input"
                                   type="password"
                                   id="password"
                                   name="password"
                                   placeholder="Create a password"
                                   autocomplete="new-password"
                                   minlength="8"
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

                        <div class="auth-strength" data-strength-meter data-score="0">
                            <span class="auth-strength-bars" aria-hidden="true">
                                <span class="auth-strength-bar"></span>
                                <span class="auth-strength-bar"></span>
                                <span class="auth-strength-bar"></span>
                                <span class="auth-strength-bar"></span>
                            </span>
                            <span class="auth-strength-label">Too short</span>
                        </div>

                        <?php if (!empty($errors['password'])): ?>
                            <p class="auth-hint auth-hint--error"><?= e($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password_confirm">Confirm password</label>
                        <div class="auth-input-wrap">
                            <input class="auth-input"
                                   type="password"
                                   id="password_confirm"
                                   name="password_confirm"
                                   placeholder="Re-enter your password"
                                   autocomplete="new-password"
                                   minlength="8"
                                   required>
                            <span class="auth-input-ico" aria-hidden="true"><i class="fa-solid fa-lock"></i></span>
                            <button type="button"
                                    class="auth-reveal"
                                    data-reveal-toggle="password_confirm"
                                    aria-controls="password_confirm"
                                    aria-pressed="false"
                                    aria-label="Show password">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <?php if (!empty($errors['password_confirm'])): ?>
                            <p class="auth-hint auth-hint--error"><?= e($errors['password_confirm']) ?></p>
                        <?php else: ?>
                            <p class="auth-hint" id="confirm-help">Re-enter your password to confirm it.</p>
                        <?php endif; ?>
                    </div>

                    <div class="auth-field">
                        <div class="auth-check">
                            <input class="auth-check-input"
                                   type="checkbox"
                                   id="terms"
                                   name="terms"
                                   value="1"
                                   <?= $old['terms'] ? 'checked' : '' ?>
                                   required>
                            <label class="auth-check-label" for="terms">
                                I agree to the
                                <button type="button" class="auth-btn-link" data-legal-open="legal-privacy">Privacy Policy (RA 10173)</button>
                                and
                                <button type="button" class="auth-btn-link" data-legal-open="legal-terms">Terms of Service</button>.
                            </label>
                        </div>
                        <?php if (!empty($errors['terms'])): ?>
                            <p class="auth-hint auth-hint--error"><?= e($errors['terms']) ?></p>
                        <?php endif; ?>
                    </div>


                    <button type="submit" class="auth-btn auth-btn--primary" data-auth-submit>
                        <i class="fa-solid fa-circle-notch fa-spin auth-btn-spin" aria-hidden="true"></i>
                        <span class="auth-btn-idle">
                            <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                            Create account
                        </span>
                    </button>

                    <p class="auth-hint">
                        Already registered?
                        <a class="auth-link" href="../login.php">Sign in instead</a>
                        or contact
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

    <?php include_once __DIR__ . '/components/legal_modals.php'; ?>

    <script src="js/auth.js" defer></script>
</body>
</html>

