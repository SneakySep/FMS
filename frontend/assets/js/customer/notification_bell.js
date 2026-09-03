/**
 * Customer Dashboard - Notification Bell Dropdown
 * Makes the top-right bell functional:
 *   - Toggle the dropdown on bell click.
 *   - Close on outside click or Escape.
 *   - Mark items as read on click (persisted in localStorage per customer),
 *     dimming the item and decrementing the badge / "new" pill.
 *
 * Expects markup rendered by dashboard.php:
 *   #notifBellWrap[data-notif-store]  -> container + localStorage key
 *   #notifBellBtn                     -> bell toggle
 *   #notifDropdown .notif-item[data-notif-id] -> notification links
 *   #notifBadge / #notifUnreadPill    -> counters
 */
(function () {
    'use strict';

    var wrap = document.getElementById('notifBellWrap');
    if (!wrap) return;

    var btn   = document.getElementById('notifBellBtn');
    var panel = document.getElementById('notifDropdown');
    var badge = document.getElementById('notifBadge');
    var pill  = document.getElementById('notifUnreadPill');
    var storeKey = wrap.getAttribute('data-notif-store') || 'crm_read_notifications';

    function readIds() {
        try {
            var raw = JSON.parse(localStorage.getItem(storeKey));
            return Array.isArray(raw) ? raw : [];
        } catch (e) {
            return [];
        }
    }

    function saveIds(ids) {
        try { localStorage.setItem(storeKey, JSON.stringify(ids)); } catch (e) { /* private mode */ }
    }

    function items() {
        return Array.prototype.slice.call(panel.querySelectorAll('.notif-item'));
    }

    function refreshCounts() {
        var unread = items().filter(function (el) {
            return !el.classList.contains('notif-read');
        }).length;

        if (badge) {
            badge.textContent = unread;
            badge.classList.toggle('hidden', unread === 0);
            badge.classList.toggle('flex', unread > 0);
        }
        if (pill) {
            pill.textContent = unread + ' new';
            pill.classList.toggle('hidden', unread === 0);
        }
    }

    function markRead(el) {
        if (el.classList.contains('notif-read')) return;
        el.classList.add('notif-read', 'opacity-40');
        var id = el.getAttribute('data-notif-id');
        if (id !== null) {
            var ids = readIds();
            if (ids.indexOf(id) === -1) {
                ids.push(id);
                saveIds(ids);
            }
        }
        refreshCounts();
    }

    function setOpen(open) {
        panel.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    // Apply persisted read state on load
    var stored = readIds();
    items().forEach(function (el) {
        if (stored.indexOf(el.getAttribute('data-notif-id')) !== -1) {
            el.classList.add('notif-read', 'opacity-40');
        }
    });
    refreshCounts();

    // Toggle on bell click
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(panel.classList.contains('hidden'));
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!panel.classList.contains('hidden') && !wrap.contains(e.target)) {
            setOpen(false);
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
    });

    // Mark item as read when clicked (navigation still happens via href)
    panel.addEventListener('click', function (e) {
        var item = e.target.closest ? e.target.closest('.notif-item') : null;
        if (item) markRead(item);
    });
})();
