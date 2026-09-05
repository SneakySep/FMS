<?php
// ---------------------------------------------------------------------------
// Customer-portal wrapper around components/top_header.php.
//
// Renders the shared bar with the standard customer action group
// (Help Desk + Book Shipment). A view overrides any slot by setting
// $pageTitle / $pageSubtitle / $headerSearch / $headerBell / $headerActions
// before including this file. Set $headerSearch = false to drop the search
// field entirely.
// ---------------------------------------------------------------------------

if (!isset($pageTitle)) {
    $pageTitle = 'Priority Handling';
}

// Default action group: Help Desk + Book Shipment.
// openBookingModal() only ships with the dashboard bundle, so fall back to the
// booking form prompt on pages that do not load it.
if (!isset($headerActions)) {
    ob_start(); ?>
    <button onclick="toggleChat()" class="crm-btn crm-btn-ghost !h-9 !px-3.5 !text-xs">
        <span class="hidden sm:inline">Help Desk</span>
        <i class="fa-solid fa-headset text-xs"></i>
    </button>
    <button onclick="window.openBookingModal ? openBookingModal() : alert('Opening Freight Booking Form...')" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
        <i class="fa-solid fa-plus text-[10px]"></i>
        <span class="hidden sm:inline">Book Shipment</span>
    </button>
    <?php $headerActions = ob_get_clean();
}

include_once __DIR__ . '/top_header.php';
