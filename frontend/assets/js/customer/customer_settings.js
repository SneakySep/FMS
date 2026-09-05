/*
 * Customer portal - Settings page controller.
 * frontend/src/views/customer/settings.php
 *
 * Responsibilities
 *   1. Hydrate the page with the real signed-in customer (profile + saved prefs).
 *   2. Appearance controls (dark mode / accent / density) apply live to <html>
 *      and persist immediately, mirroring the no-flash bootstrap in
 *      src/includes/header.php.
 *   3. Notification controls stage until "Apply Changes" is pressed.
 *   4. Profile, password and 2FA actions talk to the FastAPI portal endpoints.
 *
 * Persistence is hybrid by design:
 *   - localStorage ('crm_customer_prefs') is ALWAYS written, so the theme is
 *     correct on the next paint even when the API is down.
 *   - The API (Supabase-backed) is attempted second. If it fails the user is
 *     told the change was kept locally instead of being shown a fake success.
 */
(function () {
  'use strict';

  var PREFS_KEY = 'crm_customer_prefs';
  var API_BASE = (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL
    ? window.APP_CONFIG.API_BASE_URL : 'http://127.0.0.1:8000') + '/api/v1';

  /* ------------------------------------------------------------------ *
   * Defaults + current state
   * ------------------------------------------------------------------ */
  var DEFAULTS = {
    dark_mode: false,
    accent_color: 'navy',
    density: 'comfortable',
    notif_sound: 'notification-1.mp3',
    sound_enabled: true,
    notify_shipment: true,
    notify_sla: true,
    notify_invoice: true,
    two_factor_enabled: false,
    billing_address: '',
    default_warehouse: 'Caloocan Hub'
  };

  // prefs = last known-good saved state. staged = not-yet-applied changes.
  var prefs = Object.assign({}, DEFAULTS);
  var staged = {};
  var apiReachable = true;   // flips false after the first failed call
  var profile = {};          // hydrated from PHP data attributes / API

  function byId(id) { return document.getElementById(id); }

  /* ------------------------------------------------------------------ *
   * Toast
   * The customer portal does not load SweetAlert (assets/js/components/
   * alert.js is admin-only), so a minimal styled toast lives here. If a
   * global showToast() ever gets added, it is preferred automatically.
   * ------------------------------------------------------------------ */
  var TOAST_STYLES = {
    success: 'bg-emerald-600',
    error: 'bg-rose-600',
    info: 'bg-slate-800'
  };
  var TOAST_ICONS = {
    success: 'fa-circle-check',
    error: 'fa-circle-exclamation',
    info: 'fa-circle-info'
  };

  function domToast(message, type) {
    var host = byId('settingsToastHost');
    if (!host) {
      host = document.createElement('div');
      host.id = 'settingsToastHost';
      host.className = 'fixed bottom-24 right-6 z-[999] flex flex-col items-end gap-2 pointer-events-none';
      document.body.appendChild(host);
    }

    var tone = TOAST_STYLES[type] ? type : 'success';
    var item = document.createElement('div');
    item.className = 'pointer-events-auto flex items-start gap-2.5 max-w-xs text-white text-xs font-semibold ' +
      'px-4 py-3 rounded-xl shadow-lg opacity-0 translate-y-2 transition-all duration-200 ' + TOAST_STYLES[tone];
    item.innerHTML = '<i class="fa-solid ' + TOAST_ICONS[tone] + ' mt-0.5"></i><span></span>';
    item.querySelector('span').textContent = message;
    host.appendChild(item);

    window.requestAnimationFrame(function () {
      item.classList.remove('opacity-0', 'translate-y-2');
    });
    window.setTimeout(function () {
      item.classList.add('opacity-0', 'translate-y-2');
      window.setTimeout(function () {
        if (item.parentNode) item.parentNode.removeChild(item);
      }, 250);
    }, 3200);
  }

  function notify(message, type) {
    type = type || 'success';
    if (typeof window.showToast === 'function') {
      window.showToast(message, type);
      return;
    }
    if (document.body) {
      domToast(message, type);
      return;
    }
    window.alert(message);
  }

  /* ------------------------------------------------------------------ *
   * localStorage mirror
   * ------------------------------------------------------------------ */
  function readLocalPrefs() {
    try {
      var raw = window.localStorage.getItem(PREFS_KEY);
      return raw ? (JSON.parse(raw) || {}) : {};
    } catch (e) { return {}; }
  }

  function writeLocalPrefs(values) {
    try {
      window.localStorage.setItem(PREFS_KEY, JSON.stringify(values));
      return true;
    } catch (e) {
      // Private mode / quota exceeded: appearance still works for this visit.
      return false;
    }
  }

  /* ------------------------------------------------------------------ *
   * API helpers
   * ------------------------------------------------------------------ */
  function userId() {
    var root = byId('customerSettingsRoot');
    var body = document.body;
    return (root && root.getAttribute('data-customer-user-id')) ||
           (body && body.getAttribute('data-agent-id')) ||
           '';
  }

  function api(method, path, body) {
    var headers = { 'Content-Type': 'application/json' };
    var uid = userId();
    if (uid) headers['x-user-id'] = uid;

    return fetch(API_BASE + path, {
      method: method,
      headers: headers,
      credentials: 'same-origin',
      body: body ? JSON.stringify(body) : undefined
    }).then(function (res) {
      return res.text().then(function (txt) {
        var data = null;
        try { data = txt ? JSON.parse(txt) : null; } catch (e) { data = null; }
        return { ok: res.status >= 200 && res.status < 300, status: res.status, data: data };
      });
    });
  }

  /**
   * Persist preferences: localStorage first (always), then the backend.
   * Returns a promise resolving to 'synced' | 'local'.
   */
  function savePrefs(patch, options) {
    options = options || {};

    var next = Object.assign({}, prefs, patch);
    prefs = next;
    writeLocalPrefs(next);

    if (!apiReachable && !options.force) {
      if (options.silent !== true) notify('Saved on this device only - portal service unreachable.', 'info');
      return Promise.resolve('local');
    }

    return api('PUT', '/portal/settings', patch).then(function (res) {
      if (res.ok) {
        apiReachable = true;
        if (res.data && res.data.data) prefs = Object.assign({}, prefs, res.data.data);
        if (options.toast) notify(options.toast, 'success');
        else if (options.silent !== true) notify('Settings saved.', 'success');
        return 'synced';
      }
      apiReachable = false;
      if (res.status === 404 || res.status === 401) {
        notify('Saved on this device only - please sign in again to sync.', 'warning');
      } else {
        notify('Saved on this device only - the server rejected the change.', 'warning');
      }
      return 'local';
    }).catch(function () {
      apiReachable = false;
      if (options.silent !== true) notify('Saved on this device only - portal service unreachable.', 'info');
      return 'local';
    });
  }

  /* ------------------------------------------------------------------ *
   * Appearance: live application
   * ------------------------------------------------------------------ */
  var ACCENT_SWATCHES = {
    navy: '#1d2e6a',
    blue: '#0066ff',
    violet: '#7c3aed',
    emerald: '#059669',
    amber: '#d97706',
    rose: '#dc2626'
  };

  function applyAppearance(values) {
    var root = document.documentElement;

    root.classList.toggle('dark', values.dark_mode === true);

    if (values.accent_color) {
      root.setAttribute('data-accent', values.accent_color);
    } else {
      root.removeAttribute('data-accent');
    }

    if (values.density === 'compact') {
      root.setAttribute('data-density', 'compact');
    } else {
      root.removeAttribute('data-density');
    }

    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
      meta.setAttribute('content', values.dark_mode ? '#080d1f' : '#f2f4f9');
    }
  }

  function syncAppearanceControls(values) {
    var darkToggle = byId('appearanceDarkToggle');
    if (darkToggle) darkToggle.checked = values.dark_mode === true;

    var density = byId('densityToggle');
    if (density) density.checked = values.density === 'compact';

    var sound = byId('notifSoundSelect');
    if (sound && values.notif_sound) sound.value = values.notif_sound;

    // Scope to the swatch buttons only: <html> also carries a data-accent
    // attribute, so a bare [data-accent] query would match the root element.
    document.querySelectorAll('#accentSwatches [data-accent]').forEach(function (swatch) {
      var active = swatch.getAttribute('data-accent') === values.accent_color;
      swatch.classList.toggle('ring-2', active);
      swatch.classList.toggle('ring-offset-2', active);
      swatch.classList.toggle('ring-slate-900', active);
      swatch.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }

  window.setAccent = function (color) {
    if (!Object.prototype.hasOwnProperty.call(ACCENT_SWATCHES, color)) return;
    var patch = { accent_color: color };
    applyAppearance(Object.assign({}, prefs, patch));
    syncAppearanceControls(Object.assign({}, prefs, patch));
    savePrefs(patch, { toast: 'Accent colour updated.' });
  };

  window.setDensity = function (density) {
    // Markup calls setDensity(this.checked) -> boolean; also accept the string.
    var value = (density === true || density === 'compact') ? 'compact' : 'comfortable';
    var patch = { density: value };
    applyAppearance(Object.assign({}, prefs, patch));
    syncAppearanceControls(Object.assign({}, prefs, patch));
    savePrefs(patch, { toast: value === 'compact' ? 'Compact view enabled.' : 'Comfortable view restored.' });
  };

  /**
   * Dark mode toggle. Kept as "stageAppearanceDark" because settings.php and
   * customer_dashboard.js already call that name; the change applies live and
   * persists immediately rather than waiting for Apply Changes.
   */
  window.stageAppearanceDark = function (checked) {
    var patch = { dark_mode: checked === true };
    applyAppearance(Object.assign({}, prefs, patch));
    syncAppearanceControls(Object.assign({}, prefs, patch));
    savePrefs(patch, {
      toast: checked ? 'Dark mode on.' : 'Light mode on.'
    });
  };

  /* ------------------------------------------------------------------ *
   * Notifications: staged until Apply Changes
   * ------------------------------------------------------------------ */
  var NOTIF_KEYS = ['notify_shipment', 'notify_sla', 'notify_invoice'];

  function notifKey(channel) {
    var map = { shipment: 'notify_shipment', sla: 'notify_sla', invoice: 'notify_invoice' };
    return map[channel] || null;
  }

  window.stageNotification = function (checked, channel) {
    var key = notifKey(channel);
    if (!key) return;
    staged[key] = checked === true;
    showApplyBar(true);
  };

  window.stageNotificationSound = function (value) {
    staged.notif_sound = value;
    showApplyBar(true);
  };

  window.stageSoundEnabled = function (checked) {
    staged.sound_enabled = checked === true;
    showApplyBar(true);
  };

  /**
   * Sound preview. No .mp3/.wav assets exist in the project, so the tones are
   * synthesised with the WebAudio API - the same approach the chat widget uses
   * for its "new message" chime. Each option maps to a distinct motif.
   */
  var SOUND_MOTIFS = {
    'notification-1.mp3': [[880, 0], [1320, 0.12]],
    'notification-2.mp3': [[660, 0], [660, 0.14], [990, 0.28]],
    'notification-3.mp3': [[523, 0], [659, 0.1], [784, 0.2], [1046, 0.3]],
    'notification-4.mp3': [[1046, 0], [784, 0.12]],
    'chime.mp3':          [[1046, 0], [1318, 0.1], [1568, 0.2]],
    'ping.mp3':           [[1568, 0]],
    'pulse.mp3':          [[440, 0], [440, 0.16]]
  };

  function playMotif(value) {
    var AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return false;

    var ctx = playMotif._ctx || (playMotif._ctx = new AudioCtx());
    if (ctx.state === 'suspended') ctx.resume();

    var motif = SOUND_MOTIFS[value] || SOUND_MOTIFS['notification-1.mp3'];
    var startAt = ctx.currentTime + 0.02;

    motif.forEach(function (note) {
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.type = value === 'pulse.mp3' ? 'sine' : 'triangle';
      osc.frequency.setValueAtTime(note[0], startAt + note[1]);
      gain.gain.setValueAtTime(0.0001, startAt + note[1]);
      gain.gain.exponentialRampToValueAtTime(0.22, startAt + note[1] + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, startAt + note[1] + 0.28);
      osc.connect(gain).connect(ctx.destination);
      osc.start(startAt + note[1]);
      osc.stop(startAt + note[1] + 0.3);
    });
    return true;
  }

  window.previewNotificationSound = function () {
    var select = byId('notifSoundSelect');
    var value = (select && select.value) || staged.notif_sound || prefs.notif_sound;

    if (prefs.sound_enabled === false && staged.sound_enabled !== true) {
      notify('Enable notification sounds first.', 'info');
      return;
    }
    if (!playMotif(value)) {
      notify('This browser cannot preview sounds.', 'info');
    }
  };

  /* ------------------------------------------------------------------ *
   * Apply bar (staged notification changes)
   * ------------------------------------------------------------------ */
  function showApplyBar(show) {
    var bar = byId('applyBar');
    if (!bar) return;
    var count = Object.keys(staged).length;
    var visible = show && count > 0;
    bar.classList.toggle('hidden', !visible);

    var hint = byId('applyHint');
    if (hint) {
      hint.textContent = visible
        ? 'You have ' + count + ' unsaved ' + (count === 1 ? 'change' : 'changes') + '.'
        : 'You have unsaved changes.';
    }
  }

  window.applySettings = function () {
    var patch = Object.assign({}, staged);
    if (!Object.keys(patch).length) {
      showApplyBar(false);
      return;
    }
    savePrefs(patch, { toast: 'Notification preferences saved.' }).then(function () {
      staged = {};
      showApplyBar(false);
      syncAppearanceControls(prefs);
      syncNotificationControls(prefs);
    });
  };

  window.discardSettings = function () {
    staged = {};
    showApplyBar(false);
    syncAppearanceControls(prefs);
    syncNotificationControls(prefs);
    notify('Changes discarded.', 'info');
  };

  function syncNotificationControls(values) {
    var map = { shipment: 'notify_shipment', sla: 'notify_sla', invoice: 'notify_invoice' };
    document.querySelectorAll('[data-notif-channel]').forEach(function (toggle) {
      var key = map[toggle.getAttribute('data-notif-channel')];
      if (key) toggle.checked = values[key] !== false;
    });

    var soundToggle = byId('soundEnabledToggle');
    if (soundToggle) soundToggle.checked = values.sound_enabled !== false;

    var sound = byId('notifSoundSelect');
    if (sound && values.notif_sound) sound.value = values.notif_sound;
  }

  /* ------------------------------------------------------------------ *
   * Profile & account
   * ------------------------------------------------------------------ */
  function initialsOf(name) {
    var parts = String(name || '').trim().split(/[\s.\-]+/).filter(Boolean);
    if (!parts.length) return 'SF';
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }

  function setTextOrValue(id, value) {
    var el = byId(id);
    if (!el || value === undefined || value === null || value === '') return;
    if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
      el.value = value;
    } else {
      el.textContent = value;
    }
  }

  function renderProfile(values) {
    profile = Object.assign({}, profile, values || {});

    var company = profile.company_name || 'Your company';
    setTextOrValue('profileCompanyName', company);
    setTextOrValue('miniAccountName', company);
    setTextOrValue('settingCompany', company);

    setTextOrValue('profileEmail', profile.email);
    setTextOrValue('miniAccountEmail', profile.email);
    setTextOrValue('settingEmail', profile.email);
    setTextOrValue('overviewAccountStatus', profile.status);

    var phone = profile.phone_number || profile.phone;
    setTextOrValue('profilePhone', phone);
    setTextOrValue('settingPhone', phone);

    setTextOrValue('settingAddress', prefs.billing_address);
    setTextOrValue('settingWarehouse', prefs.default_warehouse);

    setTextOrValue('accountIdLabel', profile.customer_id);
    setTextOrValue('overviewAccountId', profile.customer_id);
    setTextOrValue('profileAccountId', profile.customer_id);

    var initials = initialsOf(company);
    setTextOrValue('avatarInitials', initials);
    setTextOrValue('miniAccountInitials', initials);

    setTextOrValue('planLabel', profile.tier);
    setTextOrValue('miniAccountPlan', profile.tier);
    setTextOrValue('overviewPlan', profile.tier);

    if (profile.created_at) {
      var d = new Date(profile.created_at);
      if (!isNaN(d.getTime())) {
        setTextOrValue('memberSince', d.toLocaleDateString('en-PH', { month: 'short', year: 'numeric' }));
      }
    }
  }


  window.stageAccountDetails = function (event) {
    if (event && typeof event.preventDefault === 'function') event.preventDefault();
    var payload = {};
    var company = byId('settingCompany');
    var email = byId('settingEmail');
    var phone = byId('settingPhone');
    var address = byId('settingAddress');
    var warehouse = byId('settingWarehouse');

    if (company && company.value.trim()) payload.company_name = company.value.trim();
    if (email && email.value.trim()) payload.email = email.value.trim();
    if (phone && phone.value.trim()) payload.phone_number = phone.value.trim();
    if (address) payload.billing_address = address.value.trim();
    if (warehouse) payload.default_warehouse = warehouse.value.trim();

    if (!payload.company_name) { notify('Company name is required.', 'error'); return; }
    if (payload.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
      notify('Enter a valid email address.', 'error');
      return;
    }
    if (payload.phone_number && !/^[0-9+\-\s()]{7,}$/.test(payload.phone_number)) {
      notify('Enter a valid contact number.', 'error');
      return;
    }

    var btn = byId('saveAccountBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }

    api('PUT', '/portal/profile', payload).then(function (res) {
      if (btn) { btn.disabled = false; btn.textContent = 'Save changes'; }

      if (res.ok) {
        apiReachable = true;
        renderProfile((res.data && res.data.data) || {});

        // billing_address / default_warehouse live in customer_settings, so
        // mirror them into the local prefs copy without a second round-trip.
        var prefPatch = {};
        if (typeof payload.billing_address === 'string') prefPatch.billing_address = payload.billing_address;
        if (typeof payload.default_warehouse === 'string') prefPatch.default_warehouse = payload.default_warehouse;
        Object.assign(prefs, prefPatch);
        writeLocalPrefs(prefs);

        notify('Account details updated.', 'success');
        return;
      }

      apiReachable = false;
      var detail = (res.data && res.data.detail) || 'The server rejected the update.';
      notify(typeof detail === 'string' ? detail : JSON.stringify(detail), 'error');
    }).catch(function () {
      apiReachable = false;
      if (btn) { btn.disabled = false; btn.textContent = 'Save changes'; }
      notify('Could not reach the portal service - please try again.', 'error');
    });
  };

  /* ------------------------------------------------------------------ *
   * Security & privacy
   * ------------------------------------------------------------------ */
  window.stageSecurity = function (event) {
    if (event && typeof event.preventDefault === 'function') event.preventDefault();
    var current = byId('inputCurrentPassword');
    var next = byId('inputNewPassword');
    var confirm = byId('inputConfirmPassword');

    var errors = [];
    if (!next || next.value.length < 8) errors.push('New password must be at least 8 characters.');
    else {
      // Mirrors the rules enforced by PUT /portal/password.
      if (!/[0-9]/.test(next.value)) errors.push('New password must contain at least one number.');
      if (!/[A-Za-z]/.test(next.value)) errors.push('New password must contain at least one letter.');
    }
    if (next && confirm && next.value !== confirm.value) errors.push('New password and confirmation do not match.');
    if (next && current && next.value === current.value) errors.push('New password must differ from the current one.');

    if (errors.length) {
      notify(errors[0], 'error');
      return;
    }

    var btn = byId('savePasswordBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Updating...'; }

    api('PUT', '/portal/password', {
      current_password: current ? current.value : '',
      new_password: next.value,
      confirm_password: confirm ? confirm.value : ''
    }).then(function (res) {
      if (btn) { btn.disabled = false; btn.textContent = 'Update password'; }

      if (res.ok) {
        apiReachable = true;
        [current, next, confirm].forEach(function (el) { if (el) el.value = ''; });
        notify('Password updated.', 'success');
        return;
      }

      apiReachable = false;
      var detail = (res.data && res.data.detail) || 'Could not change the password.';
      notify(typeof detail === 'string' ? detail : JSON.stringify(detail), 'error');
    }).catch(function () {
      apiReachable = false;
      if (btn) { btn.disabled = false; btn.textContent = 'Update password'; }
      notify('Could not reach the portal service - please try again.', 'error');
    });
  };

  window.toggleTwoFactor = function (checked) {
    var patch = { two_factor_enabled: checked === true };
    savePrefs(patch, {
      toast: checked
        ? 'Two-factor authentication marked as enabled.'
        : 'Two-factor authentication disabled.'
    }).then(function () {
      refreshTwoFactorBadge(checked);
    });
  };



  /* ------------------------------------------------------------------ *
   * Sessions
   * ------------------------------------------------------------------ */
  /*
   * NOTE: the portal has no server-side session registry, so "revoke" can only
   * remove the row from this list. The label and toast say exactly that instead
   * of pretending a remote session was terminated.
   */
  window.endSession = function (btn) {
    var row = btn && btn.closest ? btn.closest('div.py-3') : null;
    if (!row) return;
    if (row.hasAttribute('data-current-session')) {
      notify('You cannot revoke the session you are currently using.', 'info');
      return;
    }

    row.style.transition = 'opacity .2s';
    row.style.opacity = '0';
    window.setTimeout(function () {
      if (row.parentNode) row.parentNode.removeChild(row);
      notify('Session removed from this list. Server-side revocation is not yet available.', 'info');
    }, 200);
  };

  window.endAllSessions = function () {
    var list = byId('sessionList');
    if (!list) return;

    var rows = Array.prototype.slice.call(list.querySelectorAll('div.py-3'));
    var removed = 0;
    rows.forEach(function (row) {
      // Keep the row that belongs to this device (the marker is on the row).
      if (row.hasAttribute('data-current-session')) return;
      row.style.opacity = '0';
      removed++;
      window.setTimeout(function () {
        if (row.parentNode) row.parentNode.removeChild(row);
      }, 200);
    });

    if (!removed) {
      notify('No other sessions to end.', 'info');
      return;
    }
    window.setTimeout(function () {
      notify(removed + ' session' + (removed > 1 ? 's' : '') + ' removed from this list. Server-side revocation is not yet available.', 'info');
    }, 220);
  };

  /* ------------------------------------------------------------------ *
   * Data export
   * ------------------------------------------------------------------ */
  window.exportAccountData = function () {
    var payload = {
      exported_at: new Date().toISOString(),
      account: {
        customer_id: profile.customer_id || null,
        company_name: profile.company_name || null,
        email: profile.email || null,
        phone_number: profile.phone_number || null,
        tier: profile.tier || null,
        status: profile.status || null
      },
      preferences: prefs,
      shipments: window.__PORTAL_SHIPMENTS__ || null,
      notifications: window.__PORTAL_NOTIFICATIONS__ || null
    };

    try {
      var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = 'swiftfreight-account-export-' + new Date().toISOString().slice(0, 10) + '.json';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
      notify('Account data exported.', 'success');
    } catch (e) {
      notify('Export failed in this browser.', 'error');
    }
  };

  /* ------------------------------------------------------------------ *
   * Billing
   * ------------------------------------------------------------------ */
  window.stageBillingAction = function (action) {
    var pages = {
      invoices: 'invoices.php',
      billing: 'invoices.php',
      shipments: 'shipments.php',
      documents: 'documents.php'
    };

    if (pages[action]) {
      window.location.href = pages[action];
      return;
    }

    var messages = {
      'payment-method': 'Payment methods are managed by your account manager - a billing request has been noted.',
      'download-invoice': 'Open the Invoices tab to download a specific invoice.',
      'upgrade-plan': 'Plan changes are handled by sales. Contact your account manager to upgrade.'
    };

    if (action === 'download-invoice') {
      window.location.href = 'invoices.php';
      return;
    }

    notify(messages[action] || 'This billing action is not available in the portal yet.', 'info');
  };


  /* ------------------------------------------------------------------ *
   * Hydration + boot
   * ------------------------------------------------------------------ */
  function hydrateFromMarkup() {
    var root = byId('customerSettingsRoot');
    if (!root) return;

    var read = function (name, fallback) {
      var v = root.getAttribute(name);
      return (v === null || v === '') ? fallback : v;
    };
    var readBool = function (name, fallback) {
      var v = root.getAttribute(name);
      if (v === null || v === '') return fallback;
      return v === '1' || v === 'true';
    };

    prefs.dark_mode = readBool('data-dark-mode', prefs.dark_mode);
    prefs.accent_color = read('data-accent-color', prefs.accent_color);
    prefs.density = read('data-density', prefs.density);
    prefs.notif_sound = read('data-notif-sound', prefs.notif_sound);
    prefs.sound_enabled = readBool('data-sound-enabled', prefs.sound_enabled);
    prefs.notify_shipment = readBool('data-notify-shipment', prefs.notify_shipment);
    prefs.notify_sla = readBool('data-notify-sla', prefs.notify_sla);
    prefs.notify_invoice = readBool('data-notify-invoice', prefs.notify_invoice);
    prefs.two_factor_enabled = readBool('data-two-factor', prefs.two_factor_enabled);
    prefs.billing_address = read('data-billing-address', prefs.billing_address);
    prefs.default_warehouse = read('data-default-warehouse', prefs.default_warehouse);

    profile = {
      customer_id: read('data-account-id', ''),
      company_name: read('data-company', ''),
      email: read('data-email', ''),
      phone_number: read('data-phone', ''),
      tier: read('data-tier', ''),
      status: read('data-status', ''),
      created_at: read('data-created-at', '')
    };
  }

  function applyLocalOverrides() {
    // localStorage is the freshest copy for appearance settings, so it wins
    // over the server values on first paint (the header bootstrap already used
    // it; this keeps the JS state in sync with what was rendered).
    var local = readLocalPrefs();
    ['dark_mode', 'accent_color', 'density'].forEach(function (key) {
      if (Object.prototype.hasOwnProperty.call(local, key)) prefs[key] = local[key];
    });
  }

  function pullFromApi() {
    if (!userId()) return Promise.resolve('local');

    return api('GET', '/portal/settings').then(function (res) {
      if (!res.ok || !res.data || !res.data.data) {
        apiReachable = false;
        return 'local';
      }
      apiReachable = true;
      var remote = res.data.data;
      var merged = Object.assign({}, DEFAULTS, remote);

      // Appearance stays local-first so the page never re-flashes after the
      // header bootstrap has already painted the saved theme.
      merged.dark_mode = prefs.dark_mode;
      merged.accent_color = prefs.accent_color;
      merged.density = prefs.density;

      prefs = merged;
      writeLocalPrefs(prefs);
      return 'synced';
    }).catch(function () {
      apiReachable = false;
      return 'local';
    });
  }

  function refreshTwoFactorBadge(enabled) {
    var on = !!enabled;
    var badge = byId('twoFactorStatus');
    var toggle = byId('twoFactorToggle');
    if (toggle) toggle.checked = on;
    if (badge) {
      badge.textContent = on ? 'Enabled' : 'Disabled';
      badge.classList.toggle('text-emerald-600', on);
      badge.classList.toggle('text-slate-400', !on);
    }

    // Overview summary card mirrors the same state.
    var overviewValue = byId('overviewTwoFactor');
    var overviewHint = byId('overviewTwoFactorHint');
    if (overviewValue) overviewValue.textContent = on ? 'On' : 'Off';
    if (overviewHint) {
      overviewHint.textContent = on ? 'One-time code required at sign-in' : 'Recommended on';
      overviewHint.classList.toggle('text-emerald-600', on);
      overviewHint.classList.toggle('text-rose-500', !on);
    }
  }

  function boot() {
    hydrateFromMarkup();
    applyLocalOverrides();

    // Paint immediately from what we know, then reconcile with the server.
    applyAppearance(prefs);
    syncAppearanceControls(prefs);
    syncNotificationControls(prefs);
    renderProfile(profile);
    refreshTwoFactorBadge(prefs.two_factor_enabled);
    showApplyBar(false);

    pullFromApi().then(function () {
      applyAppearance(prefs);
      syncAppearanceControls(prefs);
      syncNotificationControls(prefs);
      refreshTwoFactorBadge(prefs.two_factor_enabled);
      setTextOrValue('settingAddress', prefs.billing_address);
      setTextOrValue('settingWarehouse', prefs.default_warehouse);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

