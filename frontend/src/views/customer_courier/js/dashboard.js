/* ==========================================================================
   DELIVERY DASHBOARD  —  New_dash/js/dashboard.js
   --------------------------------------------------------------------------
   Behaviour for New_dash/dashboard.php:
     1. Live tracking map (Leaflet) with an animated courier along a fixed
        Makati CDC -> BGC route, plus ETA / speed / progress / milestones.
     2. Courier booking form: inline validation, live fare quote, and a
        localStorage-only submit that prepends a row to the deliveries table.
     3. Deliveries table filtering: status pills, vehicle select, header search.
     4. Star rating modal: writes to localStorage and re-paints the rating
        summary (average, distribution bars, review list) without a reload.

   FRONTEND ONLY. There is no fetch(), no XMLHttpRequest and no api_helper.php
   anywhere in this file. Anything the user creates is stored in localStorage
   under the two keys below. The commented "BACKEND WIRING" blocks mark the
   single place each network call would go once POST /api/v1/portal/shipments
   and POST /api/v1/portal/shipments/{id}/rating exist.
   ========================================================================== */

(function () {
    'use strict';

    /* ------------------------------------------------------------------
       Storage keys + tiny JSON helpers
       ------------------------------------------------------------------ */
    var BOOKING_KEY = 'newdash_bookings';
    var RATING_KEY  = 'newdash_ratings';

    function readStore(key) {
        try {
            var raw = window.localStorage.getItem(key);
            var parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function writeStore(key, rows) {
        try {
            window.localStorage.setItem(key, JSON.stringify(rows));
        } catch (e) {
            /* private mode / quota: the page still works for this session */
        }
    }

    function el(id) { return document.getElementById(id); }
    function on(node, type, handler) { if (node) node.addEventListener(type, handler); }
    function money(value) { return '\u20B1' + Number(value).toFixed(2); }

    /* Vehicle pricing mirror of $vehicle_meta in dashboard.php. Kept as data so
       the quote, the pickup window and the new table row never disagree with
       the PHP-rendered fleet cards. */
    var VEHICLES = {
        motorcycle: { label: 'Motorcycle', icon: 'fa-motorcycle', rate: 79,  maxKg: 5,    freeKg: 5,    perKg: 6,  window: 25 },
        tricycle:   { label: 'Tricycle',   icon: 'fa-bicycle',    rate: 110, maxKg: 25,   freeKg: 20,   perKg: 4,  window: 35 },
        van:        { label: 'L300 Van',   icon: 'fa-truck',      rate: 420, maxKg: 600,  freeKg: 300,  perKg: 1,  window: 60 },
        pickup:     { label: 'Pickup',     icon: 'fa-truck-pickup', rate: 680, maxKg: 1200, freeKg: 800, perKg: 0.8, window: 90 }
    };

    var SERVICE_MULTIPLIER = { 'Express': 1.45, 'Same-day': 1.15, 'Scheduled': 1 };

    /* Status badge classes mirror $status_badges in dashboard.php. */
    var STATUS_BADGES = {
        scheduled:        'crm-badge-slate',
        picked_up:        'crm-badge-violet',
        in_transit:       'crm-badge-blue',
        out_for_delivery: 'crm-badge-amber',
        delivered:        'crm-badge-green'
    };
    var STATUS_LABELS = {
        scheduled: 'Scheduled', picked_up: 'Picked up', in_transit: 'In transit',
        out_for_delivery: 'Out for delivery', delivered: 'Delivered'
    };

    /* ------------------------------------------------------------------
       Toast  —  the only "did it work" signal, since nothing is sent
       ------------------------------------------------------------------ */
    var toastTimer = null;
    function toast(title, body, icon) {
        var existing = el('dlvToast');
        if (existing) existing.remove();

        var node = document.createElement('div');
        node.id = 'dlvToast';
        node.className = 'dlv-toast flex items-start gap-3';
        node.setAttribute('role', 'status');
        node.innerHTML =
            '<span class="crm-kpi-ico !w-8 !h-8 shrink-0"><i class="fa-solid ' + (icon || 'fa-circle-check') + ' text-[13px]"></i></span>' +
            '<span class="min-w-0">' +
                '<span class="block text-xs font-bold" style="color: var(--fg-heading);"></span>' +
                '<span class="block text-[11px] font-semibold mt-0.5" style="color: var(--fg-muted);"></span>' +
            '</span>';
        node.children[1].children[0].textContent = title;
        node.children[1].children[1].textContent = body;
        document.body.appendChild(node);

        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { node.remove(); }, 4200);
    }

    /* ==================================================================
       1. LIVE TRACKING MAP
       ------------------------------------------------------------------
       A fixed Makati CDC -> BGC polyline is walked by the courier marker.
       The animation is a plain requestAnimationFrame loop over the polyline
       so the ETA, speed, progress rail and milestone dots all read from the
       same "distance travelled" value and can never drift apart.
       ================================================================== */
    var ROUTE = [
        [14.5685, 121.0286], // Makati CDC
        [14.5630, 121.0340],
        [14.5560, 121.0395],
        [14.5510, 121.0440],
        [14.5460, 121.0490], // EDSA / Ayala exchange
        [14.5400, 121.0540],
        [14.5340, 121.0580],
        [14.5280, 121.0620], // McKinley West
        [14.5210, 121.0660],
        [14.5150, 121.0700], // BGC
        [14.5100, 121.0730]
    ];
    var ROUTE_KM_TOTAL = 6.4;   // demo distance for the whole polyline
    var ROUTE_MINUTES  = 34;    // demo drive time at the seeded pace

    var trackMap = null, courierMarker = null, routeLine = null;
    var travelled = 0.78;       // starts at 78% to match the PHP-rendered rail
    var paused = false, rafId = null, lastStamp = null;

    function distanceMeters(a, b) {
        var R = 6371000, toRad = Math.PI / 180;
        var dLat = (b[0] - a[0]) * toRad, dLng = (b[1] - a[1]) * toRad;
        var s = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(a[0] * toRad) * Math.cos(b[0] * toRad) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return 2 * R * Math.asin(Math.sqrt(s));
    }

    /* Cumulative metre offsets per vertex, so a 0..1 position maps to a point
       on the line instead of snapping between vertices. */
    function routeLengths() {
        var acc = [0];
        for (var i = 1; i < ROUTE.length; i++) {
            acc.push(acc[i - 1] + distanceMeters(ROUTE[i - 1], ROUTE[i]));
        }
        return acc;
    }

    function pointAt(fraction) {
        var acc = routeLengths(), total = acc[acc.length - 1];
        var target = Math.max(0, Math.min(1, fraction)) * total;
        for (var i = 1; i < acc.length; i++) {
            if (target <= acc[i]) {
                var span = acc[i] - acc[i - 1] || 1;
                var t = (target - acc[i - 1]) / span;
                return [
                    ROUTE[i - 1][0] + (ROUTE[i][0] - ROUTE[i - 1][0]) * t,
                    ROUTE[i - 1][1] + (ROUTE[i][1] - ROUTE[i - 1][1]) * t
                ];
            }
        }
        return ROUTE[ROUTE.length - 1];
    }

    function courierIcon() {
        return L.divIcon({
            className: '',
            html: '<span class="dlv-courier-pin"></span>',
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });
    }

    function setText(id, value) {
        var node = el(id);
        if (node) node.textContent = value;
    }

    function initTrackingMap() {
        var host = el('trackingMap');
        if (!host || typeof L === 'undefined') return;

        trackMap = L.map(host, { scrollWheelZoom: false }).setView(ROUTE[0], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18, attribution: '&copy; OpenStreetMap'
        }).addTo(trackMap);

        routeLine = L.polyline(ROUTE, { color: '#2563eb', weight: 4, opacity: .85, dashArray: '1, 8', lineCap: 'round' }).addTo(trackMap);

        L.marker(ROUTE[0]).addTo(trackMap).bindPopup('<b>Pickup</b><br>Makati CDC');
        L.marker(ROUTE[ROUTE.length - 1]).addTo(trackMap).bindPopup('<b>Drop</b><br>BGC, Taguig');

        courierMarker = L.marker(pointAt(travelled), { icon: courierIcon(), zIndexOffset: 500 }).addTo(trackMap);
        courierMarker.bindPopup('<b>J. Ramos</b><br>Motorcycle &middot; NKA-4471');
        trackMap.fitBounds(routeLine.getBounds(), { padding: [34, 34] });

        paintTracking();
        startTrackingLoop();
    }

    /* One place that writes every tracking readout, so the rail, the ETA chip
       and the telemetry tiles can never disagree with the marker. */
    function paintTracking() {
        var pct = Math.round(travelled * 100);
        var minutesLeft = Math.max(0, Math.round(ROUTE_MINUTES * (1 - travelled)));
        var kmLeft = Math.max(0, ROUTE_KM_TOTAL * (1 - travelled));

        var bar = el('trackProgressBar');
        if (bar) bar.style.width = pct + '%';
        /* The markup already prints the "%" next to this node, so keep it bare. */
        setText('trackProgressPct', String(pct));
        setText('trackDistance', kmLeft.toFixed(1) + ' km');
        setText('trackEta', travelled >= 1 ? 'Arrived' : minutesLeft + ' min');
        setText('trackSpeed', paused ? '0' : String(24 + Math.round(Math.abs(Math.sin(travelled * 9)) * 12)));
        /* Markup prints "of 3" beside this node, so only the ordinal goes in. */
        setText('trackStop', travelled >= 1 ? '3' : String(1 + Math.floor(travelled * 3)));

        if (courierMarker) courierMarker.setLatLng(pointAt(travelled));
        paintMilestones();
    }

    /* Milestone dots light up in order as the courier passes each leg. */
    function paintMilestones() {
        var items = document.querySelectorAll('#trackTimeline .dlv-ms-item');
        var reached = travelled >= 1 ? items.length : 1 + Math.floor(travelled * items.length);
        for (var i = 0; i < items.length; i++) {
            var dot = items[i].querySelector('.dlv-ms-dot');
            var line = items[i].querySelector('.dlv-ms-line');
            var done = i < reached - 1;
            var active = i === reached - 1;
            if (!dot) continue;
            dot.classList.toggle('is-done', done);
            dot.classList.toggle('is-active', active);
            items[i].setAttribute('data-ms-state', done ? 'done' : (active ? 'active' : 'todo'));
            if (line) line.classList.toggle('is-done', done);
        }
    }

    function startTrackingLoop() {
        if (rafId !== null || travelled >= 1) return;
        lastStamp = null;
        rafId = window.requestAnimationFrame(stepTracking);
    }

    function stepTracking(stamp) {
        if (paused) { rafId = window.requestAnimationFrame(stepTracking); return; }
        if (lastStamp === null) lastStamp = stamp;
        var dt = (stamp - lastStamp) / 1000;
        lastStamp = stamp;

        /* The lane takes ROUTE_MINUTES in reality; 8x keeps the demo readable
           without a half-hour wait for the marker to move. */
        travelled += (dt / (ROUTE_MINUTES * 60)) * 8;
        if (travelled >= 1) {
            travelled = 1;
            rafId = null;
            paintTracking();
            onArrived();
            return;
        }
        paintTracking();
        rafId = window.requestAnimationFrame(stepTracking);
    }

    function onArrived() {
        var btn = el('trackPauseBtn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class=\"fa-solid fa-check text-[10px]\"></i> Completed'; }
        toast('Arrived at BGC, Taguig', 'Waybill WB-90412 handed over. Rate the courier to close it out.', 'fa-flag-checkered');
    }

    function bindTrackingControls() {
        on(el('trackPauseBtn'), 'click', function () {
            paused = !paused;
            this.innerHTML = paused
                ? '<i class="fa-solid fa-play text-[10px]"></i> Resume'
                : '<i class="fa-solid fa-pause text-[10px]"></i> Pause';
            this.classList.toggle('is-active', paused);
            if (!paused) startTrackingLoop();
        });

        on(el('trackRecenterBtn'), 'click', function () {
            if (trackMap && courierMarker) {
                trackMap.setView(courierMarker.getLatLng(), Math.max(trackMap.getZoom(), 14));
            }
        });

        /* "Track" buttons on the open rows point the map at that waybill.
           Delegated on document so rows added later (bookings restored from
           localStorage) are covered without re-binding. */
        document.addEventListener('click', function (event) {
            var btn = event.target.closest ? event.target.closest('[data-track]') : null;
            if (!btn) return;
            var waybill = btn.getAttribute('data-track');
            setText('trackWaybill', waybill);
            var section = el('live-tracking');
            if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            toast('Now tracking ' + waybill, 'The map follows the courier on this lane.', 'fa-location-crosshairs');
        });
    }

    /* ==================================================================
       2. COURIER BOOKING  —  validation, live quote, localStorage submit
       ================================================================== */
    function selectedVehicle() {
        var checked = document.querySelector('#bookingForm input[name="vehicle"]:checked');
        return checked ? checked.value : 'motorcycle';
    }

    function fieldValue(id) {
        var node = el(id);
        return node ? node.value.trim() : '';
    }

    function fieldNumber(id) {
        var node = el(id);
        return node ? parseFloat(node.value) : NaN;
    }

    function showFieldError(id, isVisible) {
        var node = errorNode(id);
        if (node) node.classList.toggle('is-shown', isVisible);
        var input = el(id);
        if (input) input.classList.toggle('dlv-input-invalid', isVisible);
        return !isVisible;
    }

    function errorNode(id) { return document.querySelector('[data-error-for="' + id + '"]'); }

    var quoteFare = 79; // kept in sync by computeQuote(), read on submit

    function setErrorMessage(id, text) {
        var node = errorNode(id);
        if (node) node.textContent = text;
    }

    /* Fare = base rate for the vehicle + surcharge over the free weight
       allowance, multiplied by the service tier. Mirrors the PHP fleet cards. */
    function computeQuote() {
        var v = VEHICLES[selectedVehicle()] || VEHICLES.motorcycle;
        var weight = fieldNumber('bookWeight');
        if (isNaN(weight) || weight < 0) weight = 0;

        var extraKg = Math.max(0, weight - v.freeKg);
        var fare = (v.rate + extraKg * v.perKg) * (SERVICE_MULTIPLIER[fieldValue('bookService')] || 1);
        quoteFare = fare;

        var quote = el('bookQuote');
        if (quote) quote.textContent = money(fare);
        var win = el('bookWindow');
        if (win) win.textContent = 'within ' + v.window + ' min';
    }

    function validateBooking() {
        var ok = true;
        ok = showFieldError('bookSender', fieldValue('bookSender').length < 3) && ok;
        ok = showFieldError('bookRecipient', fieldValue('bookRecipient').length < 3) && ok;
        ok = showFieldError('bookName', fieldValue('bookName').length < 2) && ok;
        ok = showFieldError('bookContact', fieldValue('bookContact').replace(/\D/g, '').length < 7) && ok;

        var weight = fieldNumber('bookWeight');
        var slots = fieldNumber('bookSlots');
        var v = VEHICLES[selectedVehicle()];

        if (!isNaN(weight) && v && weight > v.maxKg) {
            setErrorMessage('bookWeight', v.label + ' tops out at ' + v.maxKg + ' kg \u2014 pick a bigger vehicle.');
            ok = showFieldError('bookWeight', true) && ok;
        } else {
            setErrorMessage('bookWeight', 'Enter a weight above 0.');
            ok = showFieldError('bookWeight', isNaN(weight) || weight <= 0) && ok;
        }
        ok = showFieldError('bookSlots', isNaN(slots) || slots < 1) && ok;
        return ok;
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }

    function nextWaybill() {
        var max = 90412;
        readStore(BOOKING_KEY).forEach(function (b) {
            var n = parseInt(String(b.id).replace(/\D/g, ''), 10);
            if (!isNaN(n) && n > max) max = n;
        });
        return 'WB-' + (max + 1);
    }

    function etaFromMinutes(minutes) {
        var d = new Date(Date.now() + minutes * 60000);
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }

    /* Builds the same markup the PHP loop renders, so a new row is visually
       identical to the seeded ones. */
    function bookingRowHtml(booking) {
        var v = VEHICLES[booking.vehicle] || VEHICLES.motorcycle;
        var search = [booking.id, booking.from, booking.to, booking.recipient, booking.courier].join(' ').toLowerCase();
        var slots = parseInt(booking.slots, 10) || 1;
        return '' +
            '<tr class="dlv-row" data-status="' + escapeHtml(booking.status) + '" data-vehicle="' + escapeHtml(booking.vehicle) + '" data-waybill="' + escapeHtml(booking.id) + '" data-search="' + escapeHtml(search) + '">' +
                '<td><span class="cell-strong font-mono !text-[11px]">' + escapeHtml(booking.id) + '</span>' +
                    '<span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">' + escapeHtml(booking.service) + ' &middot; just now</span></td>' +
                '<td><span class="block text-xs font-semibold" style="color: var(--fg-heading);">' + escapeHtml(booking.to) + '</span>' +
                    '<span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">from ' + escapeHtml(booking.from) + ' &middot; 6.4 km</span></td>' +
                '<td><span class="inline-flex items-center gap-2 text-xs font-semibold">' +
                    '<i class="fa-solid ' + escapeHtml(v.icon) + ' text-[11px]" style="color: var(--navy-400);"></i> ' + escapeHtml(v.label) + '</span></td>' +
                '<td><span class="block text-xs font-semibold" style="color: var(--fg-heading);">' + escapeHtml(booking.courier) + '</span>' +
                    '<span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">to ' + escapeHtml(booking.recipient) + '</span></td>' +
                '<td><span class="crm-badge ' + (STATUS_BADGES[booking.status] || 'crm-badge-slate') + '"><span class="crm-badge-dot"></span> ' + escapeHtml(STATUS_LABELS[booking.status] || booking.status) + '</span></td>' +
                '<td><span class="text-xs font-bold whitespace-nowrap" style="color: var(--fg-heading);">' + escapeHtml(booking.eta) + '</span>' +
                    '<div class="dlv-bar w-24 mt-1.5"><div class="dlv-bar-fill !bg-brand-blue" style="width: 4%;"></div></div></td>' +
                '<td class="text-right whitespace-nowrap"><span class="text-xs font-bold" style="color: var(--fg-heading);">' + escapeHtml(booking.weight) + ' kg</span>' +
                    '<span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">' + slots + ' parcel' + (slots === 1 ? '' : 's') + '</span></td>' +
                '<td class="text-right whitespace-nowrap"><button type="button" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]" data-track="' + escapeHtml(booking.id) + '">' +
                    '<i class="fa-solid fa-location-crosshairs text-[10px]"></i> Track</button></td>' +
            '</tr>';
    }

    function bindBooking() {
        var form = el('bookingForm');
        if (!form) return;

        form.addEventListener('input', computeQuote);
        form.addEventListener('change', computeQuote);

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!validateBooking()) {
                toast('Check the highlighted fields', 'The booking stays on this page until every field is valid.', 'fa-triangle-exclamation');
                return;
            }

            var v = VEHICLES[selectedVehicle()];
            var booking = {
                id: nextWaybill(),
                from: fieldValue('bookSender'),
                to: fieldValue('bookRecipient'),
                recipient: fieldValue('bookName'),
                contact: fieldValue('bookContact'),
                courier: 'Awaiting dispatch',
                vehicle: selectedVehicle(),
                service: fieldValue('bookService'),
                weight: fieldNumber('bookWeight'),
                slots: fieldNumber('bookSlots'),
                note: fieldValue('bookNote'),
                fare: quoteFare,
                status: 'scheduled',
                eta: etaFromMinutes(v.window + 30),
                created_at: new Date().toISOString()
            };

            /* BACKEND WIRING: swap the next two statements for
               make_api_request('/api/v1/portal/shipments', 'POST', booking)
               once that endpoint exists. */
            var bookings = readStore(BOOKING_KEY);
            bookings.unshift(booking);
            writeStore(BOOKING_KEY, bookings);

            prependBookingRow(booking);
            applyFilters();
            updateLocalCounters();
            resetBookingForm(form);
            toast('Courier booked for ' + booking.id,
                money(booking.fare) + ' by ' + v.label + ' \u00b7 pickup within ' + v.window + ' min.', 'fa-bolt');
        });

        on(el('clearLocalBtn'), 'click', function () {
            writeStore(BOOKING_KEY, []);
            writeStore(RATING_KEY, []);
            Array.prototype.forEach.call(document.querySelectorAll('.dlv-row[data-local="1"]'), function (row) { row.remove(); });
            renderRatings();
            applyFilters();
            updateLocalCounters();
            toast('Local data cleared', 'The table and the ratings are back to the seeded demo values.', 'fa-broom');
        });

        computeQuote();
    }

    function resetBookingForm(form) {
        form.reset();
        Array.prototype.forEach.call(document.querySelectorAll('.dlv-field-error'), function (node) {
            node.classList.remove('is-shown');
        });
        Array.prototype.forEach.call(document.querySelectorAll('.dlv-input-invalid'), function (node) {
            node.classList.remove('dlv-input-invalid');
        });
        computeQuote();
    }

    function prependBookingRow(booking) {
        var body = el('deliveriesBody');
        if (!body) return;
        var holder = document.createElement('tbody');
        holder.innerHTML = bookingRowHtml(booking);
        var row = holder.firstElementChild;
        row.setAttribute('data-local', '1');
        row.classList.add('dlv-row-enter');
        body.insertBefore(row, body.firstElementChild);
    }

    /* Replays saved bookings on load so a refresh keeps the desk's work. */
    function restoreBookings() {
        var body = el('deliveriesBody');
        var bookings = readStore(BOOKING_KEY);
        if (!body || !bookings.length) { updateLocalCounters(); return; }
        bookings.slice().reverse().forEach(function (booking) {
            if (body.querySelector('[data-waybill="' + booking.id + '"]')) return;
            var holder = document.createElement('tbody');
            holder.innerHTML = bookingRowHtml(booking);
            var row = holder.firstElementChild;
            row.setAttribute('data-local', '1');
            body.insertBefore(row, body.firstElementChild);
        });
        updateLocalCounters();
    }

    function updateLocalCounters() {
        setText('localBookingsCount', String(readStore(BOOKING_KEY).length));
        setText('localRatingsCount', String(readStore(RATING_KEY).length));
    }

    /* ==================================================================
       3. DELIVERIES TABLE FILTERING
       ------------------------------------------------------------------
       Three inputs feed one predicate: the header search box, the vehicle
       select, and the status pills. Each row carries data-search /
       data-vehicle / data-status so the filter never touches the DOM text.
       ================================================================== */
    var activeStatus = 'all';
    var activeVehicle = 'all';
    var searchTerm = '';

    function rowMatches(row) {
        if (activeStatus !== 'all' && row.getAttribute('data-status') !== activeStatus) return false;
        if (activeVehicle !== 'all' && row.getAttribute('data-vehicle') !== activeVehicle) return false;
        if (searchTerm && (row.getAttribute('data-search') || '').indexOf(searchTerm) === -1) return false;
        return true;
    }

    function applyFilters() {
        var rows = document.querySelectorAll('#deliveriesBody .dlv-row');
        var visible = 0;
        Array.prototype.forEach.call(rows, function (row) {
            var show = rowMatches(row);
            row.hidden = !show;
            row.classList.toggle('dlv-row-hidden', !show);
            if (show) visible++;
        });

        setText('deliveryVisibleCount', String(visible));
        var empty = el('deliveriesEmpty');
        var table = el('deliveriesTable');
        if (empty) empty.style.display = visible ? 'none' : '';
        if (table) table.style.display = visible ? '' : 'none';
    }

    function bindFilters() {
        var pills = el('statusPills');
        if (pills) {
            pills.addEventListener('click', function (event) {
                var btn = event.target.closest('[data-status]');
                if (!btn) return;
                activeStatus = btn.getAttribute('data-status');
                Array.prototype.forEach.call(pills.querySelectorAll('[data-status]'), function (node) {
                    node.classList.toggle('is-active', node === btn);
                });
                applyFilters();
            });
        }

        on(el('vehicleFilter'), 'change', function () {
            activeVehicle = this.value;
            applyFilters();
        });

        on(el('clearFiltersBtn'), 'click', function () {
            activeStatus = 'all';
            activeVehicle = 'all';
            searchTerm = '';
            var vehicle = el('vehicleFilter');
            if (vehicle) vehicle.value = 'all';
            var search = el('deliverySearch');
            if (search) search.value = '';
            Array.prototype.forEach.call(document.querySelectorAll('#statusPills [data-status]'), function (node) {
                node.classList.toggle('is-active', node.getAttribute('data-status') === 'all');
            });
            applyFilters();
        });
    }

    /* Called from the topbar search input's onkeyup (see top_header.php). */
    function filterTable(value) {
        searchTerm = String(value || '').trim().toLowerCase();
        applyFilters();
    }

    /* ==================================================================
       4. STAR RATING MODAL + FEEDBACK ROLL-UP
       ------------------------------------------------------------------
       The PHP-rendered averages are the baseline; every localStorage rating
       is folded on top of them and the summary is re-painted. Re-rating the
       same waybill replaces the earlier entry rather than double counting.
       ================================================================== */
    var RATING_WORDS = ['', 'Poor', 'Fair', 'Good', 'Very good', 'Excellent'];
    var ratingOverlay = null, pickedStars = 0, ratingTarget = null;

    function openRatingModal(waybill, courier, route) {
        ratingTarget = { id: waybill || 'Manual entry', courier: courier || 'Courier', route: route || 'This lane' };
        pickedStars = 0;
        setText('ratingWaybill', ratingTarget.id);
        setText('ratingRoute', ratingTarget.route);
        var title = el('ratingModalTitle');
        if (title) title.textContent = waybill ? 'Rate this delivery' : 'Rate a recent delivery';

        var comment = el('ratingComment');
        if (comment) comment.value = '';
        var error = el('ratingError');
        if (error) error.classList.add('hidden');
        Array.prototype.forEach.call(document.querySelectorAll('#ratingTags [data-tag]'), function (tag) {
            tag.classList.remove('is-active');
            tag.setAttribute('aria-pressed', 'false');
        });
        paintStars(0);
        if (ratingOverlay) {
            ratingOverlay.classList.remove('pointer-events-none', 'opacity-0');
            var firstStar = document.querySelector('#ratingStars .dlv-star-btn');
            if (firstStar) firstStar.focus();
        }
    }

    function closeRatingModal() {
        if (!ratingOverlay) return;
        ratingOverlay.classList.add('opacity-0', 'pointer-events-none');
    }

    function paintStars(count) {
        Array.prototype.forEach.call(document.querySelectorAll('#ratingStars .dlv-star-btn'), function (btn) {
            var value = parseInt(btn.getAttribute('data-star'), 10);
            btn.classList.toggle('is-on', value <= count);
            btn.setAttribute('aria-checked', value === count ? 'true' : 'false');
        });
        setText('ratingWord', count ? RATING_WORDS[count] : 'Select a score');
    }

    function bindRatingModal() {
        ratingOverlay = el('ratingOverlay');
        if (!ratingOverlay) return;

        var stars = el('ratingStars');
        if (stars) {
            stars.addEventListener('click', function (event) {
                var btn = event.target.closest('[data-star]');
                if (!btn) return;
                pickedStars = parseInt(btn.getAttribute('data-star'), 10);
                paintStars(pickedStars);
                var error = el('ratingError');
                if (error) error.classList.add('hidden');
            });
            stars.addEventListener('mouseover', function (event) {
                var btn = event.target.closest('[data-star]');
                if (btn) paintStars(parseInt(btn.getAttribute('data-star'), 10));
            });
            stars.addEventListener('mouseleave', function () { paintStars(pickedStars); });
        }

        var tags = el('ratingTags');
        if (tags) {
            tags.addEventListener('click', function (event) {
                var tag = event.target.closest('[data-tag]');
                if (!tag) return;
                var isActive = tag.classList.toggle('is-active');
                tag.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        Array.prototype.forEach.call(document.querySelectorAll('[data-rating-close]'), function (node) {
            on(node, 'click', closeRatingModal);
        });
        on(ratingOverlay, 'click', function (event) { if (event.target === ratingOverlay) closeRatingModal(); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !ratingOverlay.classList.contains('pointer-events-none')) closeRatingModal();
        });

        on(el('openRatingBtn'), 'click', function () { openRatingModal(null, null, null); });
        on(el('openRatingBtnAlt'), 'click', function () { openRatingModal(null, null, null); });

        /* Row-level "Rate courier" buttons (delegated for the same reason). */
        document.addEventListener('click', function (event) {
            var btn = event.target.closest ? event.target.closest('[data-rate]') : null;
            if (!btn) return;
            openRatingModal(btn.getAttribute('data-rate'), btn.getAttribute('data-courier'), btn.getAttribute('data-route'));
        });

        on(el('ratingSubmitBtn'), 'click', submitRating);
    }

    function submitRating() {
        if (!pickedStars) {
            var error = el('ratingError');
            if (error) error.classList.remove('hidden');
            return;
        }

        var chosenTags = [];
        Array.prototype.forEach.call(document.querySelectorAll('#ratingTags [data-tag].is-active'), function (tag) {
            chosenTags.push(tag.getAttribute('data-tag'));
        });

        var comment = el('ratingComment');
        var rating = {
            id: ratingTarget.id,
            courier: ratingTarget.courier,
            route: ratingTarget.route,
            stars: pickedStars,
            tags: chosenTags,
            comment: comment ? comment.value.trim() : '',
            created_at: new Date().toISOString()
        };

        /* BACKEND WIRING: swap the store write below for
           make_api_request('/api/v1/portal/shipments/' + rating.id + '/rating',
           'POST', rating) once that endpoint exists. */
        var stored = readStore(RATING_KEY).filter(function (row) { return row.id !== rating.id; });
        stored.unshift(rating);
        writeStore(RATING_KEY, stored);

        paintRowStars(rating.id, rating.stars);
        renderRatings();
        updateLocalCounters();
        closeRatingModal();
        toast('Thanks for the ' + rating.stars + '-star rating', 'Saved for ' + rating.id + ' on this device only.', 'fa-star');
    }

    /* Turns a row's "Rate courier" button into the static star display. */
    function paintRowStars(waybill, stars) {
        var button = document.querySelector('[data-rate="' + waybill + '"]');
        if (!button || !button.parentElement) return;
        var cell = button.parentElement;
        var holder = document.createElement('span');
        holder.className = 'dlv-stars';
        holder.setAttribute('title', 'Rated ' + stars + ' out of 5');
        holder.setAttribute('data-rating-for', waybill);
        for (var i = 1; i <= 5; i++) {
            var icon = document.createElement('i');
            icon.className = 'fa-solid fa-star' + (i <= stars ? ' is-on' : '');
            holder.appendChild(icon);
        }
        var caption = document.createElement('span');
        caption.className = 'block text-[10px] font-semibold';
        caption.style.color = 'var(--fg-muted)';
        caption.textContent = 'Rated';
        cell.replaceChild(holder, button);
        cell.appendChild(caption);
    }

    /* Baseline breakdown from the inlined JSON + everything in localStorage. */
    function ratingSnapshot() {
        var node = el('ratingSeed');
        var seed = { breakdown: {}, reviews: [] };
        try { seed = (node && JSON.parse(node.textContent)) || seed; } catch (e) { /* keep defaults */ }

        var breakdown = {};
        [1, 2, 3, 4, 5].forEach(function (n) { breakdown[n] = (seed.breakdown && seed.breakdown[n]) || 0; });

        var reviews = (seed.reviews || []).slice();
        readStore(RATING_KEY).forEach(function (row) {
            var stars = Math.max(1, Math.min(5, parseInt(row.stars, 10) || 0));
            breakdown[stars] += 1;
            reviews.unshift({
                id: row.id, rating: stars, courier: row.courier,
                to: row.route, comment: row.comment, tags: row.tags || []
            });
        });
        return { breakdown: breakdown, reviews: reviews };
    }

    function renderRatings() {
        var snap = ratingSnapshot();
        var total = 0, sum = 0;
        [1, 2, 3, 4, 5].forEach(function (n) { total += snap.breakdown[n]; sum += n * snap.breakdown[n]; });
        var average = total ? sum / total : 0;

        setText('ratingAverage', average.toFixed(1));
        setText('ratingTotalCount', String(total));
        setText('ratingPositive', String(total ? Math.round((snap.breakdown[4] + snap.breakdown[5]) / total * 100) : 0));
        /* The KPI card up top reads the same numbers. */
        setText('kpiRating', average.toFixed(1));
        setText('kpiRatingCount', total + ' ratings');

        var avgStars = el('ratingAverageStars');
        if (avgStars) {
            Array.prototype.forEach.call(avgStars.querySelectorAll('i'), function (icon, index) {
                icon.classList.toggle('is-on', index < Math.round(average));
            });
        }

        [1, 2, 3, 4, 5].forEach(function (n) {
            var fill = document.querySelector('[data-dist-fill="' + n + '"]');
            var count = document.querySelector('[data-dist-count="' + n + '"]');
            if (fill) fill.style.width = (total ? Math.round(snap.breakdown[n] / total * 100) : 0) + '%';
            if (count) count.textContent = String(snap.breakdown[n]);
        });

        renderReviews(snap.reviews);
    }

    function renderReviews(reviews) {
        var list = el('reviewList');
        if (!list) return;
        list.innerHTML = '';
        reviews.slice(0, 6).forEach(function (rv) {
            var stars = parseInt(rv.rating, 10) || 0;
            var where = rv.to || 'the drop point';
            var body = rv.comment || ('Handover at ' + where + ' went smoothly \u2014 ' +
                (stars >= 5 ? 'courier was early and careful with the parcels.' : 'delivery completed as scheduled.'));
            var tagLine = (rv.tags && rv.tags.length)
                ? '<span class="dlv-review-tags block text-[10px] font-bold mt-1" style="color: var(--brand);"></span>'
                : '';

            var li = document.createElement('li');
            li.className = 'flex items-start gap-3';
            li.setAttribute('data-review-for', rv.id || '');
            li.innerHTML =
                '<span class="crm-avatar !w-8 !h-8 !rounded-lg !text-[10px]"></span>' +
                '<div class="min-w-0 flex-1">' +
                    '<div class="flex items-center justify-between gap-2">' +
                        '<span class="text-xs font-bold truncate" style="color: var(--fg-heading);"></span>' +
                        '<span class="dlv-stars shrink-0"></span>' +
                    '</div>' +
                    '<p class="text-[11px] leading-snug mt-0.5" style="color: var(--fg-body);"></p>' + tagLine +
                '</div>';

            li.children[0].textContent = (rv.courier || 'CU').replace(/\s/g, '').slice(0, 2).toUpperCase();
            li.querySelector('.truncate').textContent = (rv.courier || 'You') + ' \u00b7 ' + (rv.id || '');
            li.querySelector('p').textContent = body;

            var starWrap = li.querySelector('.dlv-stars');
            starWrap.setAttribute('aria-label', stars + ' out of 5');
            for (var i = 1; i <= 5; i++) {
                var icon = document.createElement('i');
                icon.className = 'fa-solid fa-star' + (i <= stars ? ' is-on' : '');
                starWrap.appendChild(icon);
            }
            if (rv.tags && rv.tags.length) {
                var tagNode = li.querySelector('.dlv-review-tags');
                if (tagNode) tagNode.textContent = rv.tags.join(' \u00b7 ');
            }
            list.appendChild(li);
        });
    }

    /* ==================================================================
       5. BOOT
       ================================================================== */
    function init() {
        bindTrackingControls();
        bindFilters();
        bindBooking();
        bindRatingModal();

        restoreBookings();
        applyFilters();
        renderRatings();
        updateLocalCounters();
        initTrackingMap();
    }

    /* The script tag is deferred, but guard anyway so the file stays safe to
       copy into a non-deferred context. */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    /* Exposed for the topbar search box's inline onkeyup handler. */
    window.DeliveryDash = {
        filterTable: filterTable,
        openRating: openRatingModal,
        refresh: function () { applyFilters(); renderRatings(); }
    };
})();



