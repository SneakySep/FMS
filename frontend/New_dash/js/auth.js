/* ==========================================================================
   AUTH  —  behaviour for New_dash/register.php
   --------------------------------------------------------------------------
   Self-contained copy of frontend/assets/js/auth.js, trimmed to the features
   this screen uses and extended with the register-only password meter.

   Progressive enhancement only: every feature below has a working no-JS
   fallback (plain form post; the server still validates every rule the
   client checks).
   ========================================================================== */
(function () {
    'use strict';

    /* ----------------------------------------------------------------------
       1. PASSWORD REVEAL
       ---------------------------------------------------------------------- */
    document.querySelectorAll('[data-reveal-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.getAttribute('data-reveal-toggle'));
            if (!input) return;

            var showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.setAttribute('aria-pressed', String(!showing));
            btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            btn.querySelector('i').className = showing
                ? 'fa-solid fa-eye'
                : 'fa-solid fa-eye-slash';

            // Keep the caret at the end of what the user has typed.
            var len = input.value.length;
            input.focus();
            try { input.setSelectionRange(len, len); } catch (e) { /* type change */ }
        });
    });

    /* ----------------------------------------------------------------------
       2. SUBMIT STATE
       ----------------------------------------------------------------------
       Guards against double-posting and gives feedback on a submit that can
       take seconds to resolve. */
    document.querySelectorAll('[data-auth-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.pending === '1') {
                e.preventDefault();
                return;
            }
            // Let native validation surface before locking the button.
            if (form.checkValidity && !form.checkValidity()) return;

            form.dataset.pending = '1';
            form.querySelectorAll('[data-auth-submit]').forEach(function (btn) {
                btn.classList.add('is-busy');
                btn.disabled = true;
                // Unlock if the request never comes back, so the user is not
                // stranded on a dead form.
                setTimeout(function () {
                    btn.classList.remove('is-busy');
                    btn.disabled = false;
                    delete form.dataset.pending;
                }, 15000);
            });
        });
    });

    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.querySelectorAll('[data-auth-form]').forEach(function (form) {
            delete form.dataset.pending;
            form.querySelectorAll('[data-auth-submit]').forEach(function (btn) {
                btn.classList.remove('is-busy');
                btn.disabled = false;
            });
        });
    });

    /* ----------------------------------------------------------------------
       3. PASSWORD STRENGTH + CONFIRM MATCH
       ----------------------------------------------------------------------
       Mirrors the server-side rules so the two can never disagree about what
       counts as acceptable: >= 8 chars, and a mix of letter classes. The meter
       is advisory only - it never blocks a submit that native validation and
       the backend accept. */
    var password = document.getElementById('password');
    var confirm  = document.getElementById('password_confirm');
    var meter    = document.querySelector('[data-strength-meter]');
    var meterLabel = meter ? meter.querySelector('.auth-strength-label') : null;

    var LABELS = ['Too short', 'Weak', 'Fair', 'Good', 'Strong'];

    function score(value) {
        if (value.length < 8) return 0;
        var s = 1;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) s += 1;
        if (/\d/.test(value)) s += 1;
        if (/[^A-Za-z0-9]/.test(value)) s += 1;
        return Math.min(s, 4);
    }

    function renderStrength() {
        if (!meter || !password) return;
        var s = score(password.value);
        meter.setAttribute('data-score', String(s));
        if (meterLabel) meterLabel.textContent = LABELS[s];
    }

    /* Confirm-field state is reported through aria-invalid + the same
       .auth-hint--error treatment the server-rendered errors use. */
    function renderMatch() {
        if (!password || !confirm) return;
        var hint = document.getElementById('confirm-help');
        var filled = confirm.value !== '';
        var mismatch = filled && confirm.value !== password.value;

        confirm.setAttribute('aria-invalid', mismatch ? 'true' : 'false');
        if (hint) {
            hint.textContent = mismatch
                ? 'The two passwords do not match yet.'
                : 'Re-enter your password to confirm it.';
            hint.classList.toggle('auth-hint--error', mismatch);
        }
    }

    if (password) {
        password.addEventListener('input', function () {
            renderStrength();
            renderMatch();
        });
        renderStrength();
    }

    if (confirm) {
        confirm.addEventListener('input', renderMatch);
        confirm.addEventListener('blur', renderMatch);
    }


    /* ----------------------------------------------------------------------
       4. LEGAL MODALS
       ----------------------------------------------------------------------
       The dialogs ship inside a wrapper carrying `inert`, which keeps them out
       of the tab order and the accessibility tree. Opening one drops `inert`,
       traps Tab inside the sheet, closes on Escape or backdrop click, and
       restores focus to the trigger on the way out. */
    var legalRoot = document.querySelector('.legal-root');
    if (legalRoot) {
        var closeTimer = null;

        function focusables(scope) {
            return Array.prototype.slice.call(scope.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            )).filter(function (el) { return !el.disabled && el.offsetParent !== null; });
        }

        function trapTab(e) {
            if (e.key !== 'Tab') return;
            var items = focusables(e.currentTarget);
            if (!items.length) return;
            var first = items[0];
            var last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault(); last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault(); first.focus();
            }
        }

        function openLegal(id, trigger) {
            var overlay = document.getElementById(id);
            if (!overlay) return;

            clearTimeout(closeTimer);
            legalRoot.removeAttribute('inert');
            overlay.hidden = false;
            document.body.style.overflow = 'hidden';
            overlay._trigger = trigger || null;

            void overlay.offsetWidth;          // flush layout so the transition runs
            overlay.classList.add('is-open');

            var items = focusables(overlay);
            if (items.length) items[items.length - 1].focus();
            overlay.addEventListener('keydown', trapTab);
        }

        function closeLegal(overlay) {
            if (!overlay) return;
            overlay.removeEventListener('keydown', trapTab);
            overlay.classList.remove('is-open');
            document.body.style.overflow = '';

            var trigger = overlay._trigger;
            closeTimer = setTimeout(function () {
                overlay.hidden = true;
                if (!document.querySelector('.legal-overlay.is-open')) {
                    legalRoot.setAttribute('inert', '');
                }
                if (trigger && document.contains(trigger)) trigger.focus();
            }, 220);
        }

        document.querySelectorAll('[data-legal-open]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openLegal(btn.getAttribute('data-legal-open'), btn);
            });
        });

        document.querySelectorAll('[data-legal-close]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeLegal(document.getElementById(btn.getAttribute('data-legal-close')));
            });
        });

        document.querySelectorAll('.legal-overlay').forEach(function (overlay) {
            overlay.addEventListener('mousedown', function (e) {
                if (e.target === overlay) closeLegal(overlay);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLegal(document.querySelector('.legal-overlay.is-open'));
        });
    }

})();
