<?php
/* ==========================================================================
    BOOK.PHP  —  book a courier (module: book / BookingModule)
    --------------------------------------------------------------------------
    Thin entry file, same shape as every New_dash page: the registry resolves
    this script to BookingModule, the module prepares the fare/form data from
    $_GET (?service=, ?vehicle=), and views/book.php renders the form. The
    top-bar "Book Delivery" hero action on every page links here.
    ========================================================================== */

require_once __DIR__ . '/classes/bootstrap.php';

App\NewDash\ModuleRegistry::renderPage(__FILE__, $_GET);
