<?php
/**
 * HTTP 500 - Internal Server Error.
 *
 * The safety net for an uncaught PHP failure. Wire it up with
 * set_exception_handler() / set_error_handler() pointing at this file, or with
 * Apache's ErrorDocument 500. Only a random reference code is shown - never the
 * exception message, which can name the backend host, port or a file path.
 */
$err_code = 500;
require __DIR__ . '/error_page.php';
