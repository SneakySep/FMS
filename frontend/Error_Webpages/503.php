<?php
/**
 * HTTP 503 - Service Unavailable.
 *
 * For planned maintenance windows and backend outages. error_page.php adds a
 * Retry-After: 300 header for proxies and crawlers, and a shorter on-page
 * countdown that forwards the visitor to a page that can actually serve rather
 * than re-hitting the one that failed.
 */
$err_code = 503;
require __DIR__ . '/error_page.php';
