<?php
/* ==========================================================================
    FLEET.PHP  —  the vehicle board (module: FleetModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to FleetModule. Two
    sidebar rows share it - fleet.php?vehicle=motorcycle and
    ?vehicle=van - and FleetModule::key() reports the matching row so the rail
    lights up the right one. Unknown or absent ?vehicle= shows the whole board.
    ========================================================================== */

require_once __DIR__ . '/../../helpers/portal_access.php';
// Segment guard: courier (C2B) portal only. Sends anonymous users to login,
// staff to their own dashboard, and business customers to the B2B portal.
// See src/helpers/portal_access.php.
require_customer_portal(CUSTOMER_SEGMENT_INDIVIDUAL);

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
