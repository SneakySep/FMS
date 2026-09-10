<?php
/* ==========================================================================
    SETTINGS  —  views/settings.php  (SettingsModule::render())
    --------------------------------------------------------------------------
    The one genuinely new screen in the refactor: before, the dark scheme /
    accent / density were only READ from the shared 'crm_customer_prefs'
    record by the pre-paint bootstrap; there was no way to change them from
    the delivery desk. This page writes them back through the exact helpers
    that bootstrap defines (window.crmSetDarkMode / crmSetAccent /
    crmSetDensity, views/partials/prefs_bootstrap.php), so the live portals
    pick the same values up on their next load.

    Reads (from SettingsModule::prepare()):
      $accents      array  token => ['label' =>, 'color' =>] - the swatch
                           radios, keyed by the data-accent tokens theme.css
                           understands.
      $storageKeys  array  [{key, label}] - the localStorage records the demo
                           owns, listed in the "Saved on this device" panel so
                           clearing is explicit, not a mystery.

    Still zero backend: no session, no fetch(). The inline script at the
    bottom only mirrors localStorage back into the form on load; every write
    goes through the shared helpers above.
    -------------------------------------------------------------------------- */
?>

        <!-- APPEARANCE -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- THEME + DENSITY -->
            <div class="lg:col-span-7 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Appearance</h2>
                        <span class="crm-panel-sub">Applies instantly and is remembered on this device</span>
                    </div>
                    <span class="crm-badge crm-badge-blue"><i class="fa-solid fa-flask text-[9px]"></i> Demo only</span>
                </div>
                <div class="crm-panel-body space-y-6">

                    <!-- Dark scheme -->
                    <label class="flex items-center justify-between gap-4 cursor-pointer">
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Dark scheme</span>
                            <span class="block text-[11px] font-semibold" style="color: var(--fg-muted);">Writes crm_customer_prefs.dark_mode, the same record the customer portal uses</span>
                        </span>
                        <input type="checkbox" id="prefDarkMode" class="sr-only">
                        <span class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full border px-0.5 transition-colors"
                              style="border-color: var(--line); background: var(--surface-muted);" aria-hidden="true">
                            <span id="prefDarkKnob" class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform" style="transform: translateX(0);"></span>
                        </span>
                    </label>

                    <!-- Density -->
                    <div>
                        <span class="crm-label">Density</span>
                        <div class="grid grid-cols-2 gap-3 mt-1" role="radiogroup" aria-label="Density">
                            <label class="dlv-vehicle cursor-pointer">
                                <input type="radio" name="prefDensity" value="comfortable" class="sr-only" checked>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold" style="color: var(--fg-heading);">Comfortable</span>
                                    <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Default spacing</span>
                                </span>
                            </label>
                            <label class="dlv-vehicle cursor-pointer">
                                <input type="radio" name="prefDensity" value="compact" class="sr-only">
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold" style="color: var(--fg-heading);">Compact</span>
                                    <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Denser tables, data-density=compact</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Accent -->
                    <div>
                        <span class="crm-label">Accent colour</span>
                        <div class="grid grid-cols-2 gap-3 mt-1" role="radiogroup" aria-label="Accent colour">
                            <?php foreach ($accents as $a_key => $a): ?>
                                <label class="dlv-vehicle cursor-pointer">
                                    <input type="radio" name="prefAccent" value="<?= htmlspecialchars($a_key) ?>" class="sr-only" <?= $a_key === 'navy' ? 'checked' : '' ?>>
                                    <span class="h-5 w-5 shrink-0 rounded-full" style="background: <?= htmlspecialchars($a['color']) ?>;" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($a['label']) ?></span>
                                        <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">data-accent=&quot;<?= htmlspecialchars($a_key) ?>&quot;</span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NOTIFICATIONS -->
            <div class="lg:col-span-5 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Notifications</h2>
                        <span class="crm-panel-sub">Only shapes the bell list on this desk</span>
                    </div>
                </div>
                <div class="crm-panel-body space-y-4">
                    <?php
                    /* Three switches rendered from one list so the labels and the
                       storage field names live in exactly one place. */
                    $toggles = [
                        ['prefNotifyDelivered', 'Deliveries completed', 'Ping when a courier marks a drop done', 'notify_delivered'],
                        ['prefNotifyExceptions', 'Exceptions &amp; delays', 'Warning when a job slips past its window', 'notify_exceptions'],
                        ['prefNotifyDigest',     'End-of-day digest',    'A quiet summary once the desk closes', 'notify_digest'],
                    ];
                    foreach ($toggles as [$t_id, $t_label, $t_note, $t_key]): ?>
                        <label class="flex items-center justify-between gap-4 cursor-pointer">
                            <span>
                                <span class="block text-xs font-bold" style="color: var(--fg-heading);"><?= $t_label ?></span>
                                <span class="block text-[11px] font-semibold" style="color: var(--fg-muted);"><?= $t_note ?></span>
                            </span>
                            <input type="checkbox" id="<?= $t_id ?>" class="sr-only" data-pref="<?= htmlspecialchars($t_key) ?>">
                            <span class="pref-switch relative inline-flex h-6 w-11 shrink-0 items-center rounded-full border px-0.5 transition-colors"
                                  style="border-color: var(--line); background: var(--surface-muted);" aria-hidden="true">
                                <span class="pref-knob inline-block h-4 w-4 rounded-full bg-white shadow transition-transform" style="transform: translateX(0);"></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>


        <!-- SAVED DATA -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Saved on this device</h2>
                        <span class="crm-panel-sub">What the demo keeps in localStorage &mdash; nothing reaches a server</span>
                    </div>
                </div>
                <div class="crm-panel-body space-y-3">
                    <?php foreach ($storageKeys as $k): ?>
                        <div class="dlv-vehicle !py-2.5">
                            <span class="dlv-vehicle-ico !w-7 !h-7"><i class="fa-solid fa-database text-[10px]"></i></span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($k['label']) ?></span>
                                <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">localStorage key &quot;<?= htmlspecialchars($k['key']) ?>&quot;</span>
                            </div>
                            <span class="text-[11px] font-extrabold" style="color: var(--fg-muted);" data-key-count="<?= htmlspecialchars($k['key']) ?>">0</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RESET -->
            <div class="lg:col-span-5 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Reset</h2>
                        <span class="crm-panel-sub">Clears only the records listed on the left</span>
                    </div>
                </div>
                <div class="crm-panel-body space-y-4">
                    <p class="text-xs leading-relaxed" style="color: var(--fg-body);">
                        Deliveries and vehicles come from
                        <span class="font-bold" style="color: var(--fg-heading);">DemoData</span> on every page view, so
                        there is nothing to sync. What a reset removes are the things you created in this browser:
                        bookings, star ratings, and the theme picked on this page.
                    </p>
                    <button type="button" id="clearDemoData" class="crm-btn crm-btn-danger w-full !justify-center">
                        <i class="fa-solid fa-broom text-[10px]"></i> Clear demo data on this device
                    </button>
                    <p class="text-[11px]" style="color: var(--fg-muted);">
                        <i class="fa-solid fa-circle-info text-[10px]"></i>
                        Your account preferences in the customer portal are not touched.
                    </p>
                </div>

        <script>
        /* This script only mirrors what is already in localStorage back into the
           form on load, and wires the controls to the shared crmSet* helpers
           defined by views/partials/prefs_bootstrap.php. No new storage format
           is introduced, and the crm_customer_prefs record itself is never
           dropped - a reset only edits the fields this page owns. */
        (function () {
            var STORE = 'crm_customer_prefs';
            var DEMO_KEYS = ['newdash_bookings', 'newdash_ratings'];
            function prefs() {
                try { return JSON.parse(localStorage.getItem(STORE) || '{}') || {}; }
                catch (e) { return {}; }
            }
            function knob(box, on) {
                var el = box.closest('label').querySelector('.pref-knob');
                if (el) el.style.transform = on ? 'translateX(20px)' : 'translateX(0)';
            }
            function setSwitch(id, on) {
                var box = document.getElementById(id);
                if (!box) return;
                box.checked = !!on;
                knob(box, on);
            }

            var p = prefs();

            /* dark scheme - the helper also repaints meta theme-color + event */
            setSwitch('prefDarkMode', document.documentElement.classList.contains('dark'));
            var dark = document.getElementById('prefDarkMode');
            if (dark) dark.addEventListener('change', function () {
                window.crmSetDarkMode(this.checked);
                knob(this, this.checked);
            });

            /* density: comfortable (default) | compact */
            var dens = p.density === 'compact' ? 'compact' : 'comfortable';
            document.querySelectorAll('input[name="prefDensity"]').forEach(function (r) {
                r.checked = r.value === dens;
                r.addEventListener('change', function () {
                    if (this.checked) window.crmSetDensity(this.value === 'compact');
                });
            });

            /* accent: one of the data-accent tokens theme.css understands */
            var acc = typeof p.accent_color === 'string' && p.accent_color ? p.accent_color : 'navy';
            document.querySelectorAll('input[name="prefAccent"]').forEach(function (r) {
                r.checked = r.value === acc;
                r.addEventListener('change', function () {
                    if (this.checked) window.crmSetAccent(this.value);
                });
            });

            /* notification switches: same record, one boolean each, on by default */
            document.querySelectorAll('[data-pref]').forEach(function (box) {
                var key = box.getAttribute('data-pref');
                setSwitch(box.id, p[key] !== false);
                box.addEventListener('change', function () {
                    try {
                        var cur = prefs();
                        cur[key] = this.checked;
                        localStorage.setItem(STORE, JSON.stringify(cur));
                    } catch (e) { /* this page only */ }
                    knob(this, this.checked);
                });
            });

            /* per-key record counts next to the storage list */
            function count(key) {
                try {
                    var raw = JSON.parse(localStorage.getItem(key) || 'null');
                    if (Array.isArray(raw)) return raw.length;
                    if (raw && typeof raw === 'object') return Object.keys(raw).length;
                } catch (e) { /* unreadable counts as empty */ }
                return 0;
            }
            document.querySelectorAll('[data-key-count]').forEach(function (el) {
                el.textContent = count(el.getAttribute('data-key-count'));
            });

            /* reset: remove exactly the demo-owned keys, then edit the prefs
               record down to the fields this page does not own. */
            var btn = document.getElementById('clearDemoData');
            if (btn) btn.addEventListener('click', function () {
                if (!window.confirm('Clear bookings, ratings and theme choices saved on this device?')) return;
                DEMO_KEYS.forEach(function (k) { try { localStorage.removeItem(k); } catch (e) {} });
                try {
                    var cur = prefs();
                    ['dark_mode', 'accent_color', 'density',
                     'notify_delivered', 'notify_exceptions', 'notify_digest'].forEach(function (f) { delete cur[f]; });
                    localStorage.setItem(STORE, JSON.stringify(cur));
                } catch (e) { /* nothing else to clear */ }
                window.location.reload();
            });
        })();
        </script>

            </div>
        </section>

