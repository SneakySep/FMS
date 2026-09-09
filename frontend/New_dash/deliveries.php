<?php
/* ==========================================================================
    DELIVERIES.PHP  —  the waybill table (module: deliveries / DeliveriesModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to DeliveriesModule,
    which filters on ?vehicle= and ?status= (top-bar search box included) and
    views/deliveries.php renders the table. Each row's "Track" button links to
    tracking.php?wb=<id>; the sidebar badge count comes from the registry.
    ========================================================================== */

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
