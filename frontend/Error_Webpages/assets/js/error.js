/* ==========================================================================
   ERROR SCREEN BEHAVIOUR  (frontend/Error_Webpages)
   --------------------------------------------------------------------------
   Deliberately tiny. auth.js already owns the legal modals, the countdown and
   the reveal toggles, so this file adds the one thing an error page needs and
   auth screens never had: a back button that knows when there is nothing to go
   back to.

   Loaded with `defer` after auth.js, so both run on DOMContentLoaded in order.
   ========================================================================== */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function () {
        var back = document.querySelector('[data-err-back]');
        if (!back) {
            return;
        }

        /* history.length <= 1 means the error page is the first entry in this
           tab: a typed URL, a bookmark, a crawler, or an Apache ErrorDocument
           rewrite. history.back() would leave the visitor staring at this same
           screen, so swap the button for a plain link to the fallback target. */
        var hasHistory = window.history.length > 1 && document.referrer !== '';

        if (!hasHistory) {
            var fallback = back.getAttribute('data-fallback');
            if (!fallback) {
                back.setAttribute('hidden', '');
                return;
            }

            var link = document.createElement('a');
            link.className = back.className;
            link.setAttribute('href', fallback);
            link.innerHTML = back.innerHTML;
            back.parentNode.replaceChild(link, back);
            return;
        }

        back.addEventListener('click', function () {
            window.history.back();
        });
    });
})();
