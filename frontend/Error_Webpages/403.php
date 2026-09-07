<?php
/**
 * HTTP 403 - Forbidden.
 *
 * Rendered when the visitor is authenticated but their role does not cover the
 * requested screen. Distinct from 401 on purpose: the account is valid, the
 * permission is not, so the copy points at an administrator rather than at the
 * login form.
 */
$err_code = 403;
require __DIR__ . '/error_page.php';
