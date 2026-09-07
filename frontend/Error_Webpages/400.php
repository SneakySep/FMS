<?php
/**
 * HTTP 400 - Bad Request.
 *
 * Rendered when the portal receives a request it cannot parse or validate: a
 * malformed form payload, a truncated body, or a hand-edited query string.
 * Copy, layout and headers all come from error_page.php.
 */
$err_code = 400;
require __DIR__ . '/error_page.php';
