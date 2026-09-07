<?php
/**
 * Preview hub for the custom error screens.
 *
 * Development convenience only: open this once to eyeball all six statuses and
 * confirm the styling matches login.php. It sends no error status and links to
 * the real pages rather than framing them, so each screen renders exactly as
 * Apache would serve it. Delete it before a public deploy if you would rather
 * not advertise which status pages exist.
 */

/* Same base-path recovery as error_page.php - see the note there about
   head.php assuming the app is the web root. */
$err_root = str_replace('\\', '/', (realpath(__DIR__ . '/..') ?: ''));
$err_base = '';
if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $err_docroot = str_replace('\\', '/', rtrim(realpath($_SERVER['DOCUMENT_ROOT']) ?: '', '/'));
    if ($err_docroot !== '' && strpos($err_root, $err_docroot) === 0) {
        $err_base = substr($err_root, strlen($err_docroot));
    }
}
$err_base = rtrim($err_base, '/');
$err_url  = static function (string $path) use ($err_base): string {
    return $err_base . '/' . ltrim($path, '/');
};
$err_e = static fn (?string $s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$err_hub = [
    ['400', 'Bad Request', 'fa-triangle-exclamation', 'Malformed or unparseable request. Nothing reached the database.'],
    ['401', 'Unauthorized', 'fa-user-lock', 'No valid credentials: expired session, idle timeout, or revoked token.'],
    ['403', 'Forbidden', 'fa-ban', 'Signed in, but the role does not cover this screen.'],
    ['404', 'Not Found', 'fa-map-location-dot', 'Default catch-all for any address with no page behind it.'],
    ['500', 'Server Error', 'fa-server', 'Uncaught PHP failure. Shows a reference code, never the exception.'],
    ['503', 'Unavailable', 'fa-screwdriver-wrench', 'Maintenance window. Sends Retry-After: 300 for crawlers.'],
];
?><!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Error screens &middot; Priority Handling Logistics</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= $err_e($err_url('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= $err_e($err_url('assets/css/theme.css')) ?>">
    <link rel="stylesheet" href="<?= $err_e($err_url('assets/css/auth.css')) ?>">
    <link rel="stylesheet" href="<?= $err_e($err_url('Error_Webpages/assets/css/error.css')) ?>">

    <?php include_once __DIR__ . '/../src/includes/tailwind_config.php'; ?>
</head>
<body class="auth-body">

    <div class="auth-topbar">
        <div class="auth-topbar-inner">
            <a class="auth-logo" href="<?= $err_e($err_url('login.php')) ?>" aria-label="Priority Handling Logistics - home">
                <img class="auth-logo-img" src="<?= $err_e($err_url('assets/image/logo-mark.png')) ?>" alt="" width="44" height="44">
                <span>
                    <span class="auth-logo-name">Priority <em>Handling</em></span>
                    <span class="auth-logo-meta">Logistics Inc.</span>
                </span>
            </a>
            <span class="auth-topbar-tag">Preview</span>
        </div>
    </div>

    <main class="err-hub">
        <p class="auth-brand-eyebrow">Portal Components</p>
        <h1 class="auth-title" style="font-size:1.875rem">Error screens</h1>
        <p class="auth-sub" style="max-width:44rem">
            Six custom status pages sharing one renderer
            (<code class="err-ref">error_page.php</code>) on the same
            <code class="err-ref">theme.css</code> + <code class="err-ref">auth.css</code>
            stack as the sign-in screen. Each card below opens the real page, with
            its real HTTP status, exactly as Apache would serve it.
        </p>

        <div class="err-hub-grid">
            <?php foreach ($err_hub as $err_card): ?>
                <a class="err-hub-card" href="<?= $err_e($err_url('Error_Webpages/' . $err_card[0] . '.php')) ?>">
                    <span class="err-hub-name">
                        <i class="fa-solid <?= $err_e($err_card[2]) ?>" aria-hidden="true"></i>
                        <?= $err_e($err_card[1]) ?>
                    </span>
                    <span class="err-hub-code"><?= $err_e($err_card[0]) ?></span>
                    <span class="err-hub-desc"><?= $err_e($err_card[3]) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="auth-foot">
        <span>&copy; <?= date('Y') ?> Priority Handling Logistics Inc. All rights reserved.</span>
        <span class="auth-foot-links">
            <a class="auth-link" href="<?= $err_e($err_url('login.php')) ?>">Back to sign in</a>
        </span>
    </footer>
</body>
</html>
