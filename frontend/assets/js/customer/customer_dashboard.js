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

  /* ---------- Booking Shipment Modal (ported from Booking Shipment.html) ---------- */
  // Matches the base used by the chat widget (config API_BASE_URL default).
  var BOOKING_API_BASE = 'http://127.0.0.1:8000/api/v1';

  window.openBookingModal = function () {
    var modal = byId('bookingModal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
  };

  window.closeBookingModal = function () {
    var modal = byId('bookingModal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  };

  // Close on ESC key for accessibility
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var modal = byId('bookingModal');
      if (modal && !modal.classList.contains('hidden')) closeBookingModal();
    }
  });

  // Collect checked radio/checkbox values into a comma string
  function checkedValues(nameAttr) {
    var els = document.querySelectorAll('input[name="' + nameAttr + '"]:checked');
    var out = [];
    els.forEach(function (el) { if (el.value) out.push(el.value); });
    return out.join(', ');
  }

  function val(id) { var el = byId(id); return el ? (el.value || '').trim() : ''; }

  window.submitBookingForm = function () {
    var form = byId('bookingForm');
    if (!form) return;
    var cid = val('bookingCustomerId');

    // Service type (selected "Type of Service" checkboxes)
    var serviceType = checkedValues('service') || 'Freight Shipping';

    // Build origin / destination from consignor / consignee blocks
    var origin = [val('senderAddress'), val('senderCity'), val('senderCountry')]
      .filter(Boolean).join(', ') || 'N/A';
    var destination = [val('consigneeAddress'), val('consigneeCity'), val('consigneeCountry')]
      .filter(Boolean).join(', ') || 'N/A';

    // Pickup datetime from courier date + time
    var pickup = val('courier_date');
    if (val('courier_time')) pickup += ' ' + val('courier_time');
    if (pickup) {
      // Ensure ISO-ish format for the API (append seconds if only date+time)
      pickup = pickup.replace('T', ' ');
    }

    // Declared amount -> agreed_amount (numeric)
    var amountRaw = val('declared_amount').replace(/[^0-9.]/g, '');
    var agreedAmount = amountRaw ? parseFloat(amountRaw) : 0;

    // Cargo details
    var cargo = val('goodsDesc') ||
      ('Shipment: ' + (checkedValues('shipment_type') || 'N/A') +
       ' / Package: ' + (checkedValues('pkg_size') || 'N/A'));

    // Extra consignment details preserved in notes so nothing is lost
    var notes = [
      'Consignor: ' + val('senderName') + ' | Tel: ' + val('senderTel'),
      'Consignee: ' + val('consigneeCompany') + ' (' + val('consigneeAttention') + ') | Tel: ' + val('consigneeTel'),
      'Shipment type: ' + checkedValues('shipment_type'),
      'Package size: ' + checkedValues('pkg_size'),
      'Currency/Insurance: USD=' + (byId('currency_usd') && byId('currency_usd').checked) +
        ', PHP=' + (byId('currency_php') && byId('currency_php').checked) +
        ', Insurance=' + (byId('insurance') && byId('insurance').checked),
      'Other charges: ' + checkedValues('charge'),
      'Qty: ' + val('qty_pcs') + ' pcs | Weight: ' + val('wt_kilos') + ' kg ' + val('wt_grams') + ' g',
      'Dimensions (cm): L' + val('dim_length') + ' x W' + val('dim_width') + ' x H' + val('dim_height')
    ].join(' | ');

    var payload = {
      customer_id: cid,
      service_type: serviceType,
      origin: origin,
      destination: destination,
      pickup_datetime: pickup || null,
      agreed_amount: agreedAmount,
      cargo_details: cargo,
      booking_status: 'New Booking',
      notes: notes
    };

    var btn = byId('bookingSubmitBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'SUBMITTING...'; }

    fetch(BOOKING_API_BASE + '/customers/' + encodeURIComponent(cid) + '/bookings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (res) {
        return res.json().then(function (data) { return { status: res.status, data: data }; });
      })
      .then(function (result) {
        if (result.status >= 200 && result.status < 300) {
          alert('Booking created successfully!\nWaybill: ' +
            ((result.data && (result.data.data && result.data.data.booking_id)) || 'N/A'));
          form.reset();
          closeBookingModal();
        } else {
          var detail = (result.data && (result.data.detail || result.data.message)) || 'Unknown error';
          alert('Failed to create booking (' + result.status + '):\n' + detail);
        }
      })
      .catch(function (err) {
        alert('Could not reach the booking service.\n' + (err && err.message ? err.message : err));
      })
      .finally(function () {
        if (btn) { btn.disabled = false; btn.textContent = 'SUBMIT BOOKING'; }
      });
  };

  /* ---------- Settings: tab switching ---------- */
  window.switchSettingsTab = function (tabName) {
    // Hide all panels
    document.querySelectorAll('.settings-panel').forEach(function (panel) {
      panel.classList.add('hidden');
    });
    // Show the target panel
    var targetPanel = document.querySelector('[data-panel="' + tabName + '"]');
    if (targetPanel) {
      targetPanel.classList.remove('hidden');
    }

    // Update button styles
    document.querySelectorAll('.settings-tab').forEach(function (tab) {
      tab.classList.remove('bg-slate-100', 'dark:bg-slate-800/60');
    });
    var activeTab = document.querySelector('[data-tab="' + tabName + '"]');
    if (activeTab) {
      activeTab.classList.add('bg-slate-100', 'dark:bg-slate-800/60');
    }
  };


  document.addEventListener('DOMContentLoaded', function () {
    if (byId('trackingMap')) setTimeout(initTrackMap, 50);
  });
})();
