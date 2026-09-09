<?php
/* ==========================================================================
    SAVED-PREFERENCE BOOTSTRAP  (shared by every New_dash page via PageLayout)
    --------------------------------------------------------------------------
    Same synchronous pre-paint script src/includes/header.php runs, reading the
    shared 'crm_customer_prefs' record so the dark scheme and accent picked in
    the customer settings carry into these demo screens. It has to stay inline
    and un-deferred: a linked file would paint one frame in the wrong theme.
    ========================================================================== */
?>
    <script>
    (function () {
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            if (prefs.dark_mode === true) document.documentElement.classList.add('dark');
            var accent = (typeof prefs.accent_color === 'string') ? prefs.accent_color : '';
            if (accent) document.documentElement.setAttribute('data-accent', accent);
            if (prefs.density === 'compact') document.documentElement.setAttribute('data-density', 'compact');
        } catch (e) { /* localStorage unavailable: fall back to defaults */ }
    })();

    /* Toggle the dark scheme and persist it in the same 'crm_customer_prefs'
       record the customer settings page writes (mirrors header.php). */
    window.crmSetDarkMode = function (on) {
        document.documentElement.classList.toggle('dark', on);
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', on ? '#080d1f' : '#f2f4f9');
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            prefs.dark_mode = !!on;
            window.localStorage.setItem('crm_customer_prefs', JSON.stringify(prefs));
        } catch (e) { /* the toggle still applies for this page */ }
        document.dispatchEvent(new CustomEvent('crm:theme-change', { detail: { dark: !!on } }));
    };

    /* Accent + density companions of crmSetDarkMode, used by settings.php.
       They write the exact same record, so the live portals pick the values up
       on the next load without any extra plumbing. */
    window.crmSetAccent = function (color) {
        var root = document.documentElement;
        if (color) { root.setAttribute('data-accent', color); } else { root.removeAttribute('data-accent'); }
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            prefs.accent_color = color || 'navy';
            window.localStorage.setItem('crm_customer_prefs', JSON.stringify(prefs));
        } catch (e) { /* applies for this page only */ }
        document.dispatchEvent(new CustomEvent('crm:theme-change', { detail: { accent: color } }));
    };

    window.crmSetDensity = function (compact) {
        var root = document.documentElement;
        if (compact) { root.setAttribute('data-density', 'compact'); } else { root.removeAttribute('data-density'); }
        try {
            var raw = window.localStorage.getItem('crm_customer_prefs');
            var prefs = raw ? JSON.parse(raw) : {};
            prefs.density = compact ? 'compact' : 'comfortable';
            window.localStorage.setItem('crm_customer_prefs', JSON.stringify(prefs));
        } catch (e) { /* applies for this page only */ }
    };
    </script>
</head>
