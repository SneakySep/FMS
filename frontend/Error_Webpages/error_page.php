<?php
/**
 * Shared renderer for the custom HTTP error screens in this folder.
 *
 * The six status pages - 400, 401, 403, 404, 500 and 503 - are thin wrappers
 * that set $err_code and require this file, so the design lives in exactly one
 * place.
 *
 * These screens sit on the same stylesheet stack as login.php and logout.php
 * (style.css -> theme.css "Priority Navy" tokens -> auth.css) and reuse the
 * .auth-* component classes, which is what keeps them visually locked to the
 * rest of the portal. error.css adds only what an error page needs and nothing
 * else has: the status badge, the oversized numeral, the requested-address
 * chip and the two-up action row.
 *
 * head.php is deliberately NOT included: it hardcodes absolute /assets/...
 * paths, which only resolve when the app is served from the web root. Error
 * pages must render correctly from any install depth, so the same tag set is
 * emitted below with a computed prefix instead.
 *
 * A wrapper may set any of these before requiring this file:
 *   $err_code    int     HTTP status to send and render (required)
 *   $err_title   string  override the panel headline
 *   $err_sub     string  override the panel explanation
 *   $err_detail  string  override the safe technical note in the alert
 */

/* Read the session when one already exists but never mint a new one: Apache
   ErrorDocument rewrites and crawler traffic hit these pages constantly, and a
   fresh cookie per 404 is pure noise. */
if (session_status() === PHP_SESSION_NONE && !empty($_COOKIE[session_name()])) {
    session_start();
}

require_once __DIR__ . '/../src/helpers/auth_flow.php';

/* --------------------------------------------------------------------------
   1. COPY
   --------------------------------------------------------------------------
   One entry per status. `points` render on the navy brand panel (what
   happened / what to do), `tips` render as the checklist under the actions.
   `tone` picks the badge fill: info = navy, warn = amber, danger = red -
   the same three fills the .crm-badge-* family already uses.
   -------------------------------------------------------------------------- */
$err_copy = [
    400 => [
        'status'    => 'Bad Request',
        'tone'      => 'warn',
        'icon'      => 'fa-solid fa-triangle-exclamation',
        'headline'  => "That request<br>didn't parse.",
        'brand_sub' => 'The console expects a well-formed request. Nothing was saved, so you can correct it and send it again.',
        'title'     => 'Bad request',
        'sub'       => 'We could not understand what was sent, so nothing was saved. No shipment, lead or account record was changed.',
        'points'    => [
            ['fa-solid fa-signature', 'Check the form', 'A required field may be empty, or hold a value the field cannot store.'],
            ['fa-solid fa-rotate', 'Send it again', 'Resubmit from the page you came from rather than a bookmark or history entry.'],
            ['fa-solid fa-headset', 'Still stuck?', 'Write to our desk with the address below and we will trace the request.'],
        ],
        'tips'      => [
            'Your draft was not submitted, so re-enter the details on the original page.',
            'Very long reference numbers and stray quotation marks are the usual culprits.',
        ],
    ],
    401 => [
        'status'    => 'Unauthorized',
        'tone'      => 'warn',
        'icon'      => 'fa-solid fa-user-lock',
        'headline'  => 'Your session<br>has ended.',
        'brand_sub' => 'For your security the portal drops idle sessions. Signing in again restores everything exactly where you left it.',
        'title'     => 'Sign in required',
        'sub'       => 'We could not verify your credentials for this page. Your session may have expired, or been signed out on another device.',
        'points'    => [
            ['fa-solid fa-arrow-right-to-bracket', 'Sign in again', 'Two fields and, if enabled, a six-digit code sent to your mailbox.'],
            ['fa-solid fa-clock', 'Idle timeout', 'The console closes inactive sessions so a shared machine cannot replay yours.'],
            ['fa-solid fa-shield-halved', 'Nothing changed', 'No data was read or written while you were unauthenticated.'],
        ],
        'tips'      => [
            'You can return to the page you came from as soon as you are signed in.',
            'If you were not trying to reach this page, close this tab and sign in again.',
        ],
    ],
    403 => [
        'status'    => 'Forbidden',
        'tone'      => 'danger',
        'icon'      => 'fa-solid fa-ban',
        'headline'  => 'This area<br>is restricted.',
        'brand_sub' => 'Every portal role sees a different set of screens. Your account is active - it simply is not cleared for this one.',
        'title'     => 'Access denied',
        'sub'       => 'You do not have permission to open this page. If you believe this is a mistake, ask your account administrator to review your role.',
        'points'    => [
            ['fa-solid fa-user-shield', 'Roles, not logins', 'Access follows the role on your account, not the device you sign in from.'],
            ['fa-solid fa-key', 'Ask for access', 'An administrator can grant the module you need in a single edit.'],
            ['fa-solid fa-envelope', 'Need it now?', 'Write to cs@priority-ph.com and copy your administrator on the request.'],
        ],
        'tips'      => [
            'Signing in with a different account will not grant access to this page.',
            'Administrators manage permissions under Admin, then Agents, then role.',
        ],
    ],
    404 => [
        'status'    => 'Not Found',
        'tone'      => 'info',
        'icon'      => 'fa-solid fa-map-location-dot',
        'headline'  => 'Off the<br>delivery route.',
        'brand_sub' => 'The address you requested is not part of this portal. It may have been renamed, moved, or typed incorrectly.',
        'title'     => 'Page not found',
        'sub'       => 'We could not find that page. Check the address, or head back to your dashboard and navigate from the menu instead.',
        'points'    => [
            ['fa-solid fa-magnifying-glass-location', 'Check the address', 'A single stray character in the URL is enough to land you here.'],
            ['fa-solid fa-compass', 'Use the menu', 'The sidebar always shows every screen your account can open.'],
            ['fa-solid fa-clock-rotate-left', 'It may have moved', 'Old report links stop working after a release; navigate fresh instead.'],
        ],
        'tips'      => [
            'Consignment tracking numbers belong on the Tracking page, not in the address bar.',
            'Bookmarks made before a release are the most common cause of this screen.',
        ],
    ],
    500 => [
        'status'    => 'Internal Server Error',
        'tone'      => 'danger',
        'icon'      => 'fa-solid fa-server',
        'headline'  => 'Something broke<br>on our side.',
        'brand_sub' => 'The request reached us but the console failed while handling it. Our engineering desk is alerted automatically.',
        'title'     => 'Internal server error',
        'sub'       => 'An unexpected error stopped this page from loading. Nothing you did caused it, and no data was lost.',
        'points'    => [
            ['fa-solid fa-rotate', 'Try once more', 'Most of these are a momentary upstream blip and clear on the next attempt.'],
            ['fa-solid fa-clock', 'Give it a minute', 'Retries are safe: a failed request never completed, so nothing was written.'],
            ['fa-solid fa-headset', 'Keep the reference', 'The code below is what lets us find your entry in the server log.'],
        ],
        'tips'      => [
            'Your work is safe - the failed request never reached the database.',
            'Quote the reference code when you contact support so we can find the log entry.',
        ],
    ],
    503 => [
        'status'    => 'Service Unavailable',
        'tone'      => 'warn',
        'icon'      => 'fa-solid fa-screwdriver-wrench',
        'headline'  => "We'll be<br>right back.",
        'brand_sub' => 'The console is briefly unavailable while we maintain or scale the service. Shipments already in transit are unaffected.',
        'title'     => 'Service unavailable',
        'sub'       => 'We are temporarily unable to serve this page. This is usually short - please try again in a few minutes.',
        'points'    => [
            ['fa-solid fa-truck-fast', 'Operations continue', 'Confirmed bookings keep moving through the network while the console is down.'],
            ['fa-solid fa-rotate', 'Try again shortly', 'Refresh in a few minutes; no need to resubmit anything you already sent.'],
            ['fa-solid fa-phone', 'Urgent shipment?', 'Call (632) 843-7484 and our operations desk will handle it manually.'],
        ],
        'tips'      => [
            'Bookings already confirmed continue to move through the network.',
            'For time-critical consignments, phone the Makati desk rather than waiting.',
        ],
    ],
];


/* --------------------------------------------------------------------------
   2. RESOLVE THE STATUS
   -------------------------------------------------------------------------- */
$err_code = isset($err_code) ? (int) $err_code : 404;
if (!isset($err_copy[$err_code])) {
    $err_code = 500; // unknown wrapper code: fail closed onto the generic page
}
$err       = $err_copy[$err_code];
$err_title = $err_title ?? $err['title'];
$err_sub   = $err_sub   ?? $err['sub'];

/* --------------------------------------------------------------------------
   3. HEADERS
   --------------------------------------------------------------------------
   Always send the real status - an error page served with 200 breaks monitoring
   and lets search engines index a dead URL. no-store everywhere because these
   screens are session-dependent: a cached 401 would hide a working session and
   a cached 403 would outlive a permission grant.
   -------------------------------------------------------------------------- */
if (!headers_sent()) {
    http_response_code($err_code);
    header('Cache-Control: no-store, max-age=0');
    header('X-Content-Type-Options: nosniff');
    if ($err_code === 503) {
        /* Tell proxies and crawlers when to come back; matches the on-page
           countdown, which is deliberately shorter for humans. */
        header('Retry-After: 300');
    }
}

/* --------------------------------------------------------------------------
   4. BASE PATH
   --------------------------------------------------------------------------
   Everything here links out of the error folder into the rest of the portal
   (login.php, assets/, src/views/...), and those targets only resolve if the
   prefix is right. head.php sidesteps this by assuming the app is the web root,
   which it is not under this XAMPP layout. Compare this file's location against
   DOCUMENT_ROOT to recover the URL path of the frontend folder.
   -------------------------------------------------------------------------- */
$err_root = str_replace('\\', '/', (realpath(__DIR__ . '/..') ?: ''));
$err_base = '';
if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $err_docroot = str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT']) ?: '', '/'));
    if ($err_docroot !== '' && strpos($err_root, $err_docroot) === 0) {
        $err_base = substr($err_root, strlen($err_docroot));
    }
}
/* CLI and odd virtual-root setups leave $err_base empty, which is exactly the
   "served from the web root" case, so the same links still resolve. */
$err_base = rtrim($err_base, '/');

/** Prefix a portal-relative path with the resolved base. */
$err_url = static function (string $path) use ($err_base): string {
    return $err_base . '/' . ltrim($path, '/');
};

/* --------------------------------------------------------------------------
   5. CONTEXT FOR THE COPY
   --------------------------------------------------------------------------
   The requested address is echoed back so the user can see what went wrong,
   which means it must be treated as hostile input: strip control characters,
   cap the length, and escape on output. Query strings are dropped - that is
   where tokens and one-time codes leak into a support screenshot.
   -------------------------------------------------------------------------- */
$err_uri_raw = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
$err_uri     = preg_replace('/[\x00-\x1F\x7F]/', '', $err_uri_raw);
$err_uri     = strtok((string) $err_uri, '?');
if ($err_uri === false || $err_uri === '') {
    $err_uri = '/';
}
if (strlen($err_uri) > 180) {
    $err_uri = substr($err_uri, 0, 177) . '...';
}

/* A signed-in user gets dashboard links; an anonymous one gets login links.
   dashboard_for_role() returns null for a role with no view, so the fallback
   chain never points at a page that would itself 404. */
$err_signed_in = !empty($_SESSION['access_token']);
$err_home      = $err_url('login.php');
if ($err_signed_in) {
    $err_dash = dashboard_for_role($_SESSION['role'] ?? '');
    if ($err_dash !== null) {
        $err_home = $err_url($err_dash);
    }
}

/* Safe technical note. Never surface a raw upstream error or stack trace:
   make_api_request() can return cURL strings naming the internal backend host
   and port. 5xx gets a short reference code instead, which the user can quote
   to support while we match it against the server log. */
if ($err_code === 500) {
    $err_ref    = strtoupper(bin2hex(random_bytes(4)));
    $err_detail = $err_detail ?? ('Reference ' . $err_ref . ' - recorded '
                   . gmdate('Y-m-d H:i') . ' UTC. Our engineering desk has been notified.');
    $err_alert  = 'error';
} elseif ($err_code === 503) {
    $err_detail = $err_detail ?? 'Maintenance or capacity limits are the usual cause. Nothing you submitted has been lost - pick up again once the console is reachable.';
    $err_alert  = 'info';
} elseif ($err_code === 400) {
    $err_detail = $err_detail ?? 'The request was rejected before it reached the database, so no record was created or modified.';
    $err_alert  = 'error';
} elseif ($err_code === 401) {
    $err_detail = $err_detail ?? 'For your security the portal does not continue past an unverified session.';
    $err_alert  = 'error';
} elseif ($err_code === 403) {
    $err_detail = $err_detail ?? 'Your account is active. This screen is simply outside the permissions attached to your role.';
    $err_alert  = 'error';
} else {
    $err_detail = $err_detail ?? 'The address was resolved by the web server, but no page is published there.';
    $err_alert  = 'info';
}

/* Primary action per status. 'retry' leans on history.back(), 'signin' and
   'home' are real links. A back button is useless when there is no history
   (typed URL, crawler, bookmark), so it is only offered where the user is
   guaranteed to have come from somewhere - a form or a failed request. */
$err_primary = [
    400 => ['retry',  'fa-rotate-right',           'Go back and correct it'],
    401 => ['signin', 'fa-arrow-right-to-bracket', 'Sign in again'],
    403 => ['home',   'fa-gauge-high',             'Back to dashboard'],
    404 => ['home',   'fa-gauge-high',             'Back to dashboard'],
    500 => ['retry',  'fa-rotate-right',           'Try again'],
    /* 503 deliberately does NOT retry the failed URI: if the console is still
       down that is an infinite loop. It forwards to a page that can serve. */
    503 => ['home',   'fa-gauge-high',             'Continue to the portal'],
][$err_code];

/* The 'home' action resolves to the dashboard for a signed-in visitor and to
   login.php for an anonymous one (see $err_home above). Relabel it to match the
   target it actually points at, otherwise a logged-out 404 advertises a
   dashboard it will never show. */
if ($err_primary[0] === 'home' && !$err_signed_in && $err_primary[2] === 'Back to dashboard') {
    $err_primary[2] = 'Go to sign in';
}

/* Seconds before a 503 self-heals; drives auth.js's [data-countdown]. */
$err_autoload = ($err_code === 503) ? 30 : 0;

/* --------------------------------------------------------------------------
   6. MARKUP
   --------------------------------------------------------------------------
   Mirrors login.php node for node so the two are indistinguishable apart from
   the content: same topbar, same navy brand panel with the CSS blueprint grid,
   same white panel, same footer and legal modals.
   -------------------------------------------------------------------------- */
$err_e = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?><!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title><?= $err_e($err['status']) ?> (<?= $err_code ?>) &middot; Priority Handling Logistics</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= $err_e($err_url('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= $err_e($err_url('assets/css/theme.css')) ?>">
    <link rel="stylesheet" href="<?= $err_e($err_url('assets/css/auth.css')) ?>">
    <!-- Loads last: it consumes the theme tokens and auth components above and
         only adds the pieces unique to an error screen. -->
    <link rel="stylesheet" href="<?= $err_e($err_url('Error_Webpages/assets/css/error.css')) ?>">

    <?php include_once __DIR__ . '/../src/includes/tailwind_config.php'; ?>

    <!-- Apply the visitor's stored theme before first paint, the same way
         src/includes/header.php does, so a signed-in user who hit a 403 or a
         stale 404 does not get a white flash. Guarded: these pages are reached
         anonymously all the time and have no settings UI of their own. -->
    <script>
    (function () {
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            if (prefs.dark_mode === true) {
                document.documentElement.classList.add('dark');
            }
            if (typeof prefs.accent_color === 'string' && prefs.accent_color) {
                document.documentElement.setAttribute('data-accent', prefs.accent_color);
            }
        } catch (e) { /* private mode / disabled storage: stay on the default */ }
    })();
    </script>
</head>
<body class="auth-body">

    <!-- Compact brand bar: carries identity on mobile, where the navy panel is
         hidden. The right-hand tag states the status code outright. -->
    <div class="auth-topbar">
        <div class="auth-topbar-inner">
            <a class="auth-logo" href="<?= $err_e($err_home) ?>" aria-label="Priority Handling Logistics - home">
                <img class="auth-logo-img" src="<?= $err_e($err_url('assets/image/logo-mark.png')) ?>" alt="" width="44" height="44">
                <span>
                    <span class="auth-logo-name">Priority <em>Handling</em></span>
                    <span class="auth-logo-meta">Logistics Inc.</span>
                </span>
            </a>
            <span class="auth-topbar-tag">HTTP <?= $err_code ?></span>
        </div>
    </div>

    <main class="auth-main">
        <div class="auth-shell">

            <!-- ==================== BRAND PANEL ==================== -->
            <aside class="auth-brand">
                <div>
                    <a class="auth-logo" href="<?= $err_e($err_home) ?>" aria-label="Priority Handling Logistics - home">
                        <img class="auth-logo-img auth-logo-img--on-navy"
                             src="<?= $err_e($err_url('assets/image/logo-mark.png')) ?>" alt="" width="44" height="44">
                        <span>
                            <span class="auth-logo-name">Priority <em>Handling</em></span>
                            <span class="auth-logo-meta">Logistics Inc. &middot; Since 2005</span>
                        </span>
                    </a>

                    <p class="auth-brand-eyebrow auth-brand-eyebrow--spaced">
                        Status &middot; <?= $err_e($err['status']) ?>
                    </p>
                    <h1 class="auth-brand-title"><?= $err['headline'] /* trusted, authored copy */ ?></h1>
                    <p class="auth-brand-sub"><?= $err_e($err['brand_sub']) ?></p>

                    <ul class="auth-features">
                        <?php foreach ($err['points'] as $err_point): ?>
                            <li class="auth-feature">
                                <span class="auth-feature-ico" aria-hidden="true"><i class="<?= $err_e($err_point[0]) ?>"></i></span>
                                <span class="auth-feature-txt">
                                    <strong><?= $err_e($err_point[1]) ?></strong>
                                    <span><?= $err_e($err_point[2]) ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="auth-brand-foot">
                    <a href="tel:+6328437484"><i class="fa-solid fa-phone" aria-hidden="true"></i> (632) 843-7484</a>
                    <a href="mailto:cs@priority-ph.com"><i class="fa-solid fa-envelope" aria-hidden="true"></i> cs@priority-ph.com</a>
                    <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Makati City, Philippines</span>
                </div>
            </aside>



            <!-- ==================== CONTENT PANEL ==================== -->
            <section class="auth-panel" aria-labelledby="err-title">
                <div class="auth-panel-head">
                    <img class="auth-logo-img" src="<?= $err_e($err_url('assets/image/logo-mark.png')) ?>" alt="" width="44" height="44">
                    <div>
                        <p class="auth-logo-name">Priority <em>Handling</em></p>
                        <p class="auth-logo-meta">Agent &amp; Customer Portal</p>
                    </div>
                </div>

                <div class="err-badge err-badge--<?= $err_e($err['tone']) ?>" aria-hidden="true">
                    <i class="<?= $err_e($err['icon']) ?>"></i>
                </div>

                <div>
                    <p class="auth-eyebrow">Error <?= $err_code ?></p>
                    <h2 class="auth-title" id="err-title"><?= $err_e($err_title) ?></h2>
                    <p class="auth-sub"><?= $err_e($err_sub) ?></p>
                </div>

                <!-- The address the server could not serve. Escaped, query string
                     stripped, length capped - see section 5. -->
                <p class="err-addr" title="<?= $err_e($err_uri) ?>">
                    <i class="fa-solid fa-location-crosshairs" aria-hidden="true"></i>
                    <span class="err-addr-txt"><?= $err_e($err_uri) ?></span>
                </p>

                <div class="auth-alert auth-alert--<?= $err_e($err_alert) ?>" role="alert" aria-live="polite">
                    <i class="fa-solid <?= $err_alert === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info' ?>" aria-hidden="true"></i>
                    <span>
                        <?= $err_e($err_detail) ?>
                        <?php if ($err_autoload): ?>
                            <br>This page moves you on in
                            <span class="tabular"
                                  data-countdown="<?= (int) $err_autoload ?>"
                                  data-countdown-redirect="<?= $err_e($err_home) ?>"><?= (int) $err_autoload ?>s</span>.
                        <?php endif; ?>
                    </span>
                </div>

                <div class="err-actions">
                    <?php
                    /* Primary: a real link for signin/home, a history back button
                       for retry. The back button is disabled by error_page's own
                       script when there is no history to go back to. */
                    if ($err_primary[0] === 'retry'): ?>
                        <button type="button" class="auth-btn auth-btn--primary"
                                data-err-back
                                data-fallback="<?= $err_e($err_home) ?>">
                            <i class="fa-solid <?= $err_e($err_primary[1]) ?>" aria-hidden="true"></i>
                            <?= $err_e($err_primary[2]) ?>
                        </button>
                        <a class="auth-btn auth-btn--ghost" href="<?= $err_e($err_home) ?>">
                            <i class="fa-solid fa-gauge-high" aria-hidden="true"></i>
                            <?= $err_signed_in ? 'Back to dashboard' : 'Go to sign in' ?>
                        </a>
                    <?php else: ?>
                        <a class="auth-btn auth-btn--primary"
                           href="<?= $err_e($err_primary[0] === 'signin' ? $err_url('login.php') : $err_home) ?>">
                            <i class="fa-solid <?= $err_e($err_primary[1]) ?>" aria-hidden="true"></i>
                            <?= $err_e($err_primary[2]) ?>
                        </a>
                        <a class="auth-btn auth-btn--ghost" href="mailto:cs@priority-ph.com">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                            Contact support
                        </a>
                    <?php endif; ?>
                </div>

                <ul class="auth-tips">
                    <?php foreach ($err['tips'] as $err_tip): ?>
                        <li>
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            <span><?= $err_e($err_tip) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($err_code === 500 || $err_code === 503): ?>
                    <p class="auth-hint">
                        Quote reference
                        <strong class="err-ref"><?= $err_e($err_code . '-' . ($err_ref ?? 'MAINT')) ?></strong>
                        when you write to
                        <a class="auth-link" href="mailto:cs@priority-ph.com">cs@priority-ph.com</a>.
                    </p>
                <?php else: ?>
                    <p class="auth-hint">
                        Need a hand? Contact
                        <a class="auth-link" href="mailto:cs@priority-ph.com">cs@priority-ph.com</a>
                        or call (632) 843-7484 - our operations desk is staffed 24/7.
                    </p>
                <?php endif; ?>
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

    <?php include_once __DIR__ . '/../src/components/legal_modals.php'; ?>

    <script src="<?= $err_e($err_url('assets/js/auth.js')) ?>" defer></script>
    <script src="<?= $err_e($err_url('Error_Webpages/assets/js/error.js')) ?>" defer></script>
</body>
</html>
