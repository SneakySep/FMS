<?php
/**
 * HTTP 401 - Unauthorized.
 *
 * Rendered when a request arrives with no credentials, or with credentials the
 * backend rejected: an expired JWT, a session closed by idle timeout, or a
 * token revoked by a sign-out on another device. The primary action is a link
 * straight back to login.php.
 */
$err_code = 401;
require __DIR__ . '/error_page.php';
