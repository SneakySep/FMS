<?php
/* ==========================================================================
    RATINGS.PHP  —  ratings and reviews (module: ratings / RatingsModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to RatingsModule, which
    folds localStorage scores on top of the PHP-rendered seed (PageLayout only
    emits #ratingSeed for modules that report needsRatingSeed()).
    ========================================================================== */

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
