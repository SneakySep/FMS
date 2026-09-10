<?php
/* ==========================================================================
    BOOK.PHP  —  book a courier (module: book / BookingModule)
    --------------------------------------------------------------------------
    Thin entry file, same shape as every New_dash page: the registry resolves
    this script to BookingModule, the module prepares the fare/form data from
    $_GET (?service=, ?vehicle=), and views/book.php renders the form. The
    top-bar "Book Delivery" hero action on every page links here.
    ========================================================================== */

require_once __DIR__ . '/../../helpers/portal_access.php';
// Segment guard: courier (C2B) portal only. Sends anonymous users to login,
// staff to their own dashboard, and business customers to the B2B portal.
// See src/helpers/portal_access.php.
require_customer_portal(CUSTOMER_SEGMENT_INDIVIDUAL);

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
