/*
 * Shared customer-portal script for pages ported from Frontend-CustomerDash.
 * Replaces the original js/main.js + js/store-bridge.js (which do not exist
 * in this project) and implements the interactions those pages expect.
 */
(function () {
  'use strict';

  function byId(id) { return document.getElementById(id); }
  function textOf(el) { return (el.textContent || el.innerText || '').toLowerCase(); }

  /* ---------- Chat widget (from src/components/chat_widget.php) ---------- */
  window.toggleChat = function () {
    var modal = byId('chatModal');
    if (modal) modal.classList.toggle('hidden');
  };

  /* ---------- Documents: search + category filter ---------- */
  window.searchDocVault = function () {
    var input = byId('docSearchInput');
    if (!input) return;
    var q = input.value.toLowerCase();
    document.querySelectorAll('.doc-item').forEach(function (row) {
      row.style.display = textOf(row).indexOf(q) > -1 ? '' : 'none';
    });
  };

  window.filterDocuments = function (category, btn) {
    document.querySelectorAll('.doc-filter-tab').forEach(function (t) {
      t.className = 'doc-filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all';
    });
    if (btn) btn.className = 'doc-filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm transition-all';
    document.querySelectorAll('.doc-item').forEach(function (row) {
      row.style.display = (category === 'all' || row.getAttribute('data-category') === category) ? '' : 'none';
    });
  };

  window.handleFileSelected = function (event) {
    var file = event.target.files && event.target.files[0];
    if (file) alert('Selected file: ' + file.name + '\n(Upload simulation)');
  };

  /* ---------- Shipments: search + status filter ---------- */
  window.searchShipmentsTable = function () {
    var input = byId('shipmentSearchInput');
    if (!input) return;
    var q = input.value.toLowerCase();
    document.querySelectorAll('.shipment-row').forEach(function (row) {
      row.style.display = textOf(row).indexOf(q) > -1 ? '' : 'none';
    });
  };

  window.filterShipments = function (status, btn) {
    document.querySelectorAll('.filter-tab').forEach(function (t) {
      t.className = 'filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all';
    });
    if (btn) btn.className = 'filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm transition-all';
    document.querySelectorAll('.shipment-row').forEach(function (row) {
      row.style.display = (status === 'all' || row.getAttribute('data-status') === status) ? '' : 'none';
    });
  };

  /* ---------- Invoices: search ---------- */
  window.searchInvoicesTable = function () {
    var input = byId('invoiceSearchInput');
    if (!input) return;
    var q = input.value.toLowerCase();
    document.querySelectorAll('.invoice-row').forEach(function (row) {
      row.style.display = textOf(row).indexOf(q) > -1 ? '' : 'none';
    });
  };

  /* ---------- Tickets: search + new ticket ---------- */
  window.searchTicketsList = function () {
    var input = byId('ticketSearchInput');
    if (!input) return;
    var q = input.value.toLowerCase();
    document.querySelectorAll('.ticket-item').forEach(function (row) {
      row.style.display = textOf(row).indexOf(q) > -1 ? '' : 'none';
    });
  };

  window.createNewTicket = function () {
    alert('New ticket form would open here. (Demo)');
  };

  /* ---------- Settings: staged apply bar ---------- */
  var staged = {};
  function showApplyBar(show) {
    var bar = byId('applyBar');
    if (bar) bar.classList.toggle('hidden', !show);
  }
  window.stageAppearanceDark = function (checked) { staged.dark = checked; showApplyBar(true); };
  window.stageNotificationSound = function (val) { staged.sound = val; showApplyBar(true); };
  window.previewNotificationSound = function () {
    console.log('Preview sound:', staged.sound || '(default)');
  };
  window.applySettings = function () {
    alert('Settings applied:\nDark mode: ' + (staged.dark ? 'ON' : 'OFF') +
          '\nSound: ' + (staged.sound || 'default'));
    showApplyBar(false);
  };
  window.discardSettings = function () {
    staged = {};
    showApplyBar(false);
  };

  /* ---------- Live Tracking (Leaflet) ---------- */
  var trackMap = null, trackMarker = null;
  var routePositions = {
    'PH-WB-208841': [10.3157, 123.8854], // Cebu
    'PH-WB-208835': [14.5995, 120.9842], // Manila
    'PH-WB-208790': [7.1907, 125.4553]   // Davao
  };

  function initTrackMap() {
    var el = byId('trackingMap');
    if (!el || typeof L === 'undefined') return;
    trackMap = L.map(el).setView([12.8797, 121.7740], 6); // Philippines centre
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18, attribution: '&copy; OpenStreetMap'
    }).addTo(trackMap);
    trackMarker = L.marker([14.5995, 120.9842]).addTo(trackMap)
      .bindPopup('Manila Hub').openPopup();
  }

  window.switchTrackWaybill = function (value) {
    if (!trackMap) initTrackMap();
    if (!trackMap) return;
    var pos = routePositions[value] || [14.5995, 120.9842];
    if (trackMarker) trackMap.removeLayer(trackMarker);
    trackMarker = L.marker(pos).addTo(trackMap)
      .bindPopup('Waybill ' + value).openPopup();
    trackMap.setView(pos, 7);
  };

  document.addEventListener('DOMContentLoaded', function () {
    if (byId('trackingMap')) setTimeout(initTrackMap, 50);
  });
})();
