/**
 * Customer Dashboard - Campaign Countdown
 * Live countdown for campaign/promo cards rendered in dashboard.php.
 *
 * Targets:
 *   .campaign-timer[data-expires]        -> countdown pill inside the card
 *   article.campaign-card                -> card wrapper (hidden when expired)
 *
 * Behaviour:
 *   - Updates every second.
 *   - Green (>24h) / amber (<=24h) / rose (<=1h) urgency styling.
 *   - Expired cards fade out and are removed; active-card badge is refreshed.
 */
(function () {
    'use strict';

    var REFRESH_INTERVAL_MS = 60000; // re-poll active-posts every 60s
    var API_ENDPOINT = ((window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL) || '') + '/api/v1/campaigns/active-posts';

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function formatRemaining(totalSeconds) {
        if (totalSeconds <= 0) return 'Expired';
        var days = Math.floor(totalSeconds / 86400);
        var hours = Math.floor((totalSeconds % 86400) / 3600);
        var mins = Math.floor((totalSeconds % 3600) / 60);
        var secs = totalSeconds % 60;

        if (days > 0) return days + 'd ' + pad(hours) + 'h ' + pad(mins) + 'm';
        if (hours > 0) return hours + 'h ' + pad(mins) + 'm ' + pad(secs) + 's';
        return mins + 'm ' + pad(secs) + 's';
    }

    function applyUrgency(timerEl, totalSeconds) {
        var base = 'campaign-timer inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 transition-colors';
        var palette;
        if (totalSeconds <= 3600) {
            palette = 'bg-rose-100 text-rose-700 border-rose-300';
        } else if (totalSeconds <= 86400) {
            palette = 'bg-amber-100 text-amber-700 border-amber-300';
        } else {
            palette = 'bg-emerald-100 text-emerald-700 border-emerald-300';
        }
        timerEl.className = base + ' ' + palette;
    }

    function removeCard(card, timerEl) {
        if (card.dataset.removing === '1') return;
        card.dataset.removing = '1';
        card.style.transition = 'opacity .4s ease, transform .4s ease';
        card.style.opacity = '0';
        card.style.transform = 'scale(.96)';
        if (timerEl) {
            var label = timerEl.querySelector('.campaign-timer-text');
            if (label) label.textContent = 'Expired';
        }
        window.setTimeout(function () {
            if (card.parentNode) card.parentNode.removeChild(card);
            updateActiveCount();
        }, 420);
    }

    function updateActiveCount() {
        var badge = document.getElementById('campaignCountBadge');
        if (!badge) return;
        var visible = document.querySelectorAll('#campaignGrid article.campaign-card:not([data-removing="1"])').length;
        badge.textContent = visible + ' Active';
    }

    function tick() {
        var timers = document.querySelectorAll('.campaign-timer[data-expires]');
        Array.prototype.forEach.call(timers, function (timerEl) {
            var raw = (timerEl.getAttribute('data-expires') || '').trim();
            var label = timerEl.querySelector('.campaign-timer-text');
            if (!raw) {
                if (label) label.textContent = 'Active';
                return;
            }
            var expiresAt = Date.parse(raw);
            if (isNaN(expiresAt)) {
                if (label) label.textContent = 'Active';
                return;
            }
            var remaining = Math.floor((expiresAt - Date.now()) / 1000);
            var card = timerEl.closest('article.campaign-card');
            if (remaining <= 0) {
                removeCard(card, timerEl);
                return;
            }
            if (label) label.textContent = formatRemaining(remaining);
            applyUrgency(timerEl, remaining);
        });
    }

    /**
     * Periodic refresh: re-poll the endpoint and drop cards that are no longer
     * returned by the backend (e.g. agent unpublished the campaign).
     */
    function refreshCampaigns() {
        if (!window.fetch) return;
        fetch(API_ENDPOINT, { credentials: 'same-origin' })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (payload) {
                var list = Array.isArray(payload) ? payload
                    : (payload && Array.isArray(payload.data) ? payload.data : null);
                if (!list) return;

                var liveTitles = {};
                list.forEach(function (item) {
                    if (item && item.title) liveTitles[item.title.trim().toLowerCase()] = true;
                });

                var cards = document.querySelectorAll('#campaignGrid article.campaign-card');
                Array.prototype.forEach.call(cards, function (card) {
                    var heading = card.querySelector('h3');
                    if (!heading) return;
                    var title = (heading.textContent || '').trim().toLowerCase();
                    if (title && !liveTitles[title]) {
                        removeCard(card, card.querySelector('.campaign-timer'));
                    }
                });
                updateActiveCount();
            })
            .catch(function () {
                /* silent - countdown keeps running on stale data */
            });
    }

    function init() {
        var grid = document.getElementById('campaignGrid');
        if (!grid) return;
        tick();
        window.setInterval(tick, 1000);
        window.setInterval(refreshCampaigns, REFRESH_INTERVAL_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
