<?php
/* ==========================================================================
    TRACKING.PHP  —  the live map (module: live / TrackingModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to TrackingModule, the
    only module that pulls Leaflet. ?wb=WB-90412 opens the map on a specific
    waybill (that is what the deliveries table links to); unknown or missing
    values fall back to the first open job.
    ========================================================================== */

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
