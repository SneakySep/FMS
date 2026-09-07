<?php
/**
 * HTTP 404 - Not Found.
 *
 * The default catch-all: Apache ErrorDocument for a URL with no file behind it,
 * and the fallback for any wrapper that asks for an unknown status. Signed-in
 * users are sent to their own dashboard via dashboard_for_role(); anonymous
 * ones to login.php.
 */
$err_code = 404;
require __DIR__ . '/error_page.php';
