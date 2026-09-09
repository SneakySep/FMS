<?php
/* ==========================================================================
    SETTINGS.PHP  —  saved preferences (module: settings / SettingsModule)
    --------------------------------------------------------------------------
    Thin entry file: the registry resolves this script to SettingsModule, which
    supplies the accent palette + storage-key list views/settings.php renders.
    Every control writes the same 'crm_customer_prefs' localStorage record the
    live portals use (see views/partials/prefs_bootstrap.php); nothing here
    touches a backend.
    ========================================================================== */

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
