<?php

/* ==========================================================================
    NEW_DASH BOOTSTRAP
    --------------------------------------------------------------------------
    A page file requires this one line and gets every class it needs. Ordered
    by dependency: data, base module, registry, layout, then the modules.

    Deliberately plain require_once instead of a Composer autoloader: New_dash
    must keep working when the folder is dropped into XAMPP's htdocs and opened
    straight at /New_dash/dashboard.php, exactly as before. The App\NewDash
    namespace matches App\Services in src/ so the naming convention is shared
    if these classes are ever pulled up into the main app.
    ========================================================================== */

require_once __DIR__ . '/DemoData.php';
require_once __DIR__ . '/DashboardModule.php';
require_once __DIR__ . '/ModuleRegistry.php';
require_once __DIR__ . '/PageLayout.php';

require_once __DIR__ . '/modules/OverviewModule.php';
require_once __DIR__ . '/modules/BookingModule.php';
require_once __DIR__ . '/modules/DeliveriesModule.php';
require_once __DIR__ . '/modules/FleetModule.php';
require_once __DIR__ . '/modules/TrackingModule.php';
require_once __DIR__ . '/modules/RatingsModule.php';
require_once __DIR__ . '/modules/SettingsModule.php';
