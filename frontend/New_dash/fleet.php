<?php
/* ==========================================================================
    FLEET.PHP  —  the vehicle board (module: FleetModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to FleetModule. Two
    sidebar rows share it - fleet.php?vehicle=motorcycle and
    ?vehicle=van - and FleetModule::key() reports the matching row so the rail
    lights up the right one. Unknown or absent ?vehicle= shows the whole board.
    ========================================================================== */

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
