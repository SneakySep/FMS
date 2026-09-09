<?php

namespace App\NewDash;

/* ==========================================================================
    PAGE LAYOUT  —  the single chrome every New_dash page is poured into
    --------------------------------------------------------------------------
    Everything dashboard.php used to own outside its sections lives here once:
    the <head> asset stack, the saved-preference bootstrap, the sidebar, the
    shared top bar, the module's own template, the star modal, the rating seed
    and the script order. A page file therefore never repeats a <link> tag.

    Asset selection is driven by the module: cssFiles() / jsFiles() decide what
    loads, needsMap() decides whether Leaflet is worth the bytes, and
    needsRatingModal() decides whether components/rating_modal.php is included.

    FRONTEND ONLY / NO BACKEND, same as before: no session, no API helper.
    ========================================================================== */

class PageLayout
{
    public function __construct(private string $root = __DIR__ . '/..')
    {
    }

    /**
     * Emit a complete document for $module.
     *
     * @param DashboardModule $module
     * @param array           $extra ['title' => override, 'pageTitle' => ...,
     *                               'subtitle' => ..., 'headerSearch' => [...]]
     */
    public function render(DashboardModule $module, array $extra = []): void
    {
        $title     = $extra['title'] ?? ($module->title() . ' - Delivery Ops');
        $pageTitle = $extra['pageTitle'] ?? $module->title();

        /* top_header.php + sidebar.php read these locals. */
        $pageSubtitle  = $extra['subtitle'] ?? $module->subtitle();
        $headerSearch  = $extra['headerSearch'] ?? $module->headerSearch();
        $headerActions = $extra['headerActions'] ?? $module->headerAction();
        $headerBell    = [
            'store' => 'newdash_read_notifs',
            'count' => count(DemoData::notifications()),
            'items' => DemoData::notifications(),
        ];
        $activePage = $module->key();
        $body       = $module->render();
        ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>

    <!-- Tailwind CSS CDN + the shared palette config (copied markup from
         src/includes/header.php so the utility classes used by the sidebar,
         topbar and cards resolve to the same colours as the live portals). -->
    <script src="https://cdn.tailwindcss.com"></script>
    <?php include_once $this->root . '/../src/includes/tailwind_config.php'; ?>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/image/logo.png">

    <!-- Shared design layer: base styles, then the token/component layer.
         theme.css must load AFTER style.css so its html.dark rules win. -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/theme.css">

    <!-- Delivery-only additions: the dashboard's own rules first, then any
         extra stylesheet the module declared through cssFiles(). dashboard.css
         is loaded on every page because the shared shell (topbar, sidebar)
         depends on it; the module sheets come last so they can refine a rule
         without !important. -->
    <link rel="stylesheet" href="css/dashboard.css">
    <?php foreach ($module->cssFiles() as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>

    <?php if ($module->needsMap()): ?>
    <!-- Leaflet is loaded only by the modules that render a map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <?php endif; ?>

    <!-- SAVED-PREFERENCE BOOTSTRAP: inline on purpose, see the partial -->
    <?php include __DIR__ . '/../views/partials/prefs_bootstrap.php'; ?>
</head>
<body class="crm-body bg-canvas text-navy-600 font-sans antialiased min-h-screen flex" data-module="<?= htmlspecialchars($module->key()) ?>">

<!-- SIDEBAR (static local copy of src/includes/sidebar.php) -->
<?php include_once $this->root . '/components/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

    <?php
    // Shared top bar: title + subtitle, search, notification bell, actions.
    include_once $this->root . '/components/top_header.php'; ?>

    <!-- MODULE CONTENT BODY -->
    <div class="p-6 lg:p-8 2xl:px-10 space-y-8 w-full">
<?= $body ?>
    </div><!-- /MODULE CONTENT BODY -->
</main><!-- /MAIN CONTENT AREA -->

<?php if ($module->needsRatingModal()): ?>
<!-- RATING MODAL (star picker + feedback, localStorage only) -->
<?php include_once $this->root . '/components/rating_modal.php'; ?>
<?php endif; ?>

<!-- LEGAL MODALS are intentionally not included: components/legal_modals.php
     renders #legal-privacy / #legal-terms, which only open through the
     data-legal-open triggers wired in js/auth.js, and nothing on these screens
     links to them. The file stays in place for register.php.

     SHARED BEHAVIOUR: assets/js/dashboard.js owns the sidebar toggle (it
     defines toggleSidebar, used by the topbar's mobile button) and the
     notification bell dropdown driven by #notifBellWrap in
     components/top_header.php. Both guard on element presence, so they are
     inert where the markup is absent. logout.js / footer.js are deliberately
     NOT loaded: these demos have no session, #logoutModal is not rendered, and
     footer.js's privacy/terms helpers target #privacyModal/#termsModal, not
     legal_modals.php. NOTE: there is no assets/js/main.js in this project. -->
<script src="../assets/js/dashboard.js" defer></script>
<script src="../assets/js/customer/notification_bell.js" defer></script>

<?php if ($module->needsRatingSeed()): ?>
<!-- SEED
     The rating seed is inlined as JSON so the ratings module can fold saved
     localStorage ratings on top of the PHP-rendered averages without a fetch.
     It is emitted on every page that reads ratings (the overview KPI card
     included) and on no others. -->
<script id="ratingSeed" type="application/json"><?= json_encode(DemoData::ratingSeed(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php endif; ?>

<!-- MODULE BEHAVIOUR: js/dashboard.js is one self-contained, deferred IIFE.
     Its init() binds every section (tracking, filters, booking, rating modal)
     and each binder returns immediately when its markup is absent, so the same
     file drives the overview, the table, the map and the booking form without
     a per-page script. It must load last: it reads the #ratingSeed JSON above
     and expects the DeliveryDash global the topbar search box calls into. -->
<script src="js/dashboard.js" defer></script>
</body>
</html>
        <?php
    }
}

