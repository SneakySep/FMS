<?php
/* ==========================================================================
    DELIVERIES.PHP  —  the waybill table (module: deliveries / DeliveriesModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to DeliveriesModule,
    which filters on ?vehicle= and ?status= (top-bar search box included) and
    views/deliveries.php renders the table. Each row's "Track" button links to
    tracking.php?wb=<id>; the sidebar badge count comes from the registry.
    ========================================================================== */

require_once __DIR__ . '/../../helpers/portal_access.php';
// Segment guard: courier (C2B) portal only. Sends anonymous users to login,
// staff to their own dashboard, and business customers to the B2B portal.
// See src/helpers/portal_access.php.
require_customer_portal(CUSTOMER_SEGMENT_INDIVIDUAL);

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
