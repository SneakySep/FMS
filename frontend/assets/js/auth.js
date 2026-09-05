/* ==========================================================================
   AUTH  —  behaviour for login.php / otp_verification.php / logout.php
   Progressive enhancement only: every feature below has a working no-JS
   fallback (plain form post, single OTP field still submits server-side).
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
       Guards against double-posting (an expensive login attempt) and gives
       feedback on a link that can take seconds to resolve. */
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
       3. SEGMENTED OTP
       ----------------------------------------------------------------------
       Six single-character boxes drive the UI; a visually-hidden master input
       named `otp_code` is what actually posts, because that is the element
       password managers and SMS autofill understand (autocomplete=
       "one-time-code" only resolves on a real, fillable field).

       Handled: auto-advance, Backspace to previous, arrow/Home/End
       navigation, full-string paste, autofill fan-out, auto-submit. */
    var otp = document.querySelector('[data-otp]');
    if (otp) {
        var boxes = Array.prototype.slice.call(otp.querySelectorAll('.auth-otp-box'));
        var master = document.getElementById(otp.getAttribute('data-otp-master'));
        var form = otp.closest('form');
        var LENGTH = boxes.length;
        var submitted = false;

        function onlyDigits(str) { return (str || '').replace(/\D+/g, ''); }

        function sync() {
            var code = boxes.map(function (b) { return b.value; }).join('');
            if (master) master.value = code;
            boxes.forEach(function (b) { b.classList.toggle('is-filled', b.value !== ''); });
            otp.classList.remove('is-invalid');
            return code;
        }

        // Spread a digit string across the boxes starting at `from`.
        function distribute(str, from) {
            var digits = onlyDigits(str);
            var i = from;
            digits.split('').forEach(function (d) {
                if (i >= LENGTH) return;
                boxes[i].value = d;
                i += 1;
            });
            return i;
        }

        function submitOtp() {
            if (submitted || !form) return;
            submitted = true;
            // requestSubmit() fires the submit event so the button spinner and
            // double-post guard still run; fall back to submit() on old engines.
            if (typeof form.requestSubmit === 'function') form.requestSubmit();
            else form.submit();
        }

        boxes.forEach(function (box, idx) {
            box.addEventListener('input', function () {
                var digits = onlyDigits(box.value);
                if (digits.length > 1) {
                    // Autofill or an IME delivered the whole code into one box.
                    var next = distribute(digits, idx);
                    box.value = digits.charAt(0);
                    boxes[Math.min(next, LENGTH - 1)].focus();
                } else {
                    box.value = digits;
                    if (digits) boxes[Math.min(idx + 1, LENGTH - 1)].focus();
                }

                if (sync().length === LENGTH) submitOtp();
            });

            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    e.preventDefault();
                    boxes[idx - 1].value = '';
                    boxes[idx - 1].focus();
                    sync();
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    e.preventDefault(); boxes[idx - 1].focus();
                } else if (e.key === 'ArrowRight' && idx < LENGTH - 1) {
                    e.preventDefault(); boxes[idx + 1].focus();
                } else if (e.key === 'Home') {
                    e.preventDefault(); boxes[0].focus();
                } else if (e.key === 'End') {
                    e.preventDefault(); boxes[LENGTH - 1].focus();
                }
            });

            box.addEventListener('paste', function (e) {
                e.preventDefault();
                var text = (e.clipboardData || window.clipboardData).getData('text');
                var next = distribute(onlyDigits(text), idx);
                boxes[Math.min(next, LENGTH - 1)].focus();
                if (sync().length === LENGTH) submitOtp();
            });

            // Normalise a stray multi-char value as soon as the box loses focus.
            box.addEventListener('blur', function () {
                box.value = onlyDigits(box.value).charAt(0);
                sync();
            });
        });

        // Autofill lands on the hidden master field; fan it out to the boxes.
        if (master) {
            master.addEventListener('input', function () {
                var digits = onlyDigits(master.value);
                boxes.forEach(function (b) { b.value = ''; });
                var next = distribute(digits, 0);
                boxes[Math.min(next, LENGTH - 1)].focus();
                if (digits.length >= LENGTH) submitOtp(); else sync();
            });
        }

        // A server-side rejection re-renders with the submitted code echoed
        // back into the master field; spread it across the boxes so the user
        // sees what they typed and can correct a single digit.
        if (master && master.value) {
            distribute(master.value, 0);
            sync();
        }

        var firstEmpty = boxes.filter(function (b) { return !b.value; })[0];
        if (firstEmpty) firstEmpty.focus();
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

    /* ----------------------------------------------------------------------
       5. COUNTDOWNS  (resend cooldown · logout auto-redirect)
       ---------------------------------------------------------------------- */
    document.querySelectorAll('[data-countdown]').forEach(function (el) {
        var remaining = parseInt(el.getAttribute('data-countdown'), 10);
        var btn = document.getElementById(el.getAttribute('data-countdown-target') || '');
        var redirect = el.getAttribute('data-countdown-redirect');
        var isClock = el.getAttribute('data-countdown-format') === 'clock';

        if (isNaN(remaining) || remaining <= 0) return;

        function pad(n) { return (n < 10 ? '0' : '') + n; }

        function render() {
            if (isClock) {
                el.textContent = pad(Math.floor(remaining / 60)) + ':' + pad(remaining % 60);
            } else {
                el.textContent = remaining + 's';
            }
        }

        if (btn) btn.disabled = true;
        render();

        var tick = setInterval(function () {
            remaining -= 1;
            render();
            if (remaining <= 0) {
                clearInterval(tick);
                if (btn) btn.disabled = false;
                if (redirect) window.location.assign(redirect);
            }
        }, 1000);
    });

})();

