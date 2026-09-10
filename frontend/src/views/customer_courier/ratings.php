<?php
/* ==========================================================================
    RATINGS.PHP  —  ratings and reviews (module: ratings / RatingsModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to RatingsModule, which
    folds localStorage scores on top of the PHP-rendered seed (PageLayout only
    emits #ratingSeed for modules that report needsRatingSeed()).
    ========================================================================== */

require_once __DIR__ . '/../../helpers/portal_access.php';
// Segment guard: courier (C2B) portal only. Sends anonymous users to login,
// staff to their own dashboard, and business customers to the B2B portal.
// See src/helpers/portal_access.php.
require_customer_portal(CUSTOMER_SEGMENT_INDIVIDUAL);

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
