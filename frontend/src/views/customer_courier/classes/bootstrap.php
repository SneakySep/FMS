<?php

/* ==========================================================================
    CUSTOMER_COURIER BOOTSTRAP
    --------------------------------------------------------------------------
    A page file requires this one line and gets every class it needs. Ordered
    by dependency: data, base module, registry, layout, then the modules.

    Deliberately plain require_once instead of a Composer autoloader: there is
    no composer.json in this project, and the classes must resolve the same way
    whether a page is opened directly or reached through the router. This folder
    now lives at src/views/customer_courier/, so a page is served from
    /CRM/customer_relationship/frontend/src/views/customer_courier/dashboard.php.
    The App\NewDash namespace is kept as-is (it collides with nothing; src/ uses
    App\Services) so the class files stay untouched by the move.
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
