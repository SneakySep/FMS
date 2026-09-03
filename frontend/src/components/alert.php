<?php
/**
 * Shared SweetAlert2 + toast helpers.
 *
 * Include once per page:
 *   <?php include_once '../../components/alert.php'; ?>
 *
 * Provides window.SwiftAlert, showToast() and showAlert() from
 * assets/js/components/alert.js. Guarded so a second include (directly or
 * via another partial) never re-declares the `const SwiftAlert` binding.
 */
if (!empty($GLOBALS['__alert_assets_loaded'])) {
    return;
}
$GLOBALS['__alert_assets_loaded'] = true;
?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="/assets/js/components/alert.js"></script>
