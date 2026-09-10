<?php
/* ==========================================================================
    DASHBOARD.PHP  —  the overview page (module: dashboard / OverviewModule)
    --------------------------------------------------------------------------
    Every New_dash page is now this thin: the registry knows which module class
    belongs to this script, the module gathers its data, PageLayout pours the
    chrome around the view. The welcome banner, KPI cards, "moving right now"
    table and quick actions all live in views/overview.php; the former 63KB
    monolith is archived at _reference/dashboard_monolith.php.txt.

    FRONTEND ONLY / NO BACKEND: no session, no API helper, localStorage prefs
    only - see classes/bootstrap.php and views/partials/prefs_bootstrap.php.
    ========================================================================== */

require_once __DIR__ . '/../../helpers/portal_access.php';
// Segment guard: courier (C2B) portal only. Sends anonymous users to login,
// staff to their own dashboard, and business customers to the B2B portal.
// See src/helpers/portal_access.php.
require_customer_portal(CUSTOMER_SEGMENT_INDIVIDUAL);

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
