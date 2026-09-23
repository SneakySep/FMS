/**
 * Rate Search Dashboard — Add-on Widgets
 * ------------------------------------------------------------------
 * Companion script para sa assets/js/sales_agent/rates_dashboard widgets:
 *   - Hero mini stats (Total / Air / Sea / Land)
 *   - Quick Quote Estimator (cheapest matching lane + all-in estimate)
 *   - Validity Health (0-7 / 8-21 / 22-45 / 45+ days)
 *   - Carrier Coverage leaderboard
 *   - Recent Quote Activity (localStorage, re-open sa calculator modal)
 *   - "/" keyboard shortcut papuntang lane search
 *
 * Data source: ang `rates:updated` / `rates:quote-computed` events mula sa
 * RatesDashboard (rates.js) — kaya wala nang doble pang fetch.
 * Walang element na kailangan = silent skip (fail-soft).
 */
const RatesDashboardWidgets = (() => {
  const HISTORY_KEY = 'rate_quote_history';
  const HISTORY_LIMIT = 6;
  const EXPIRY_WINDOW_DAYS = 45; // kapareho ng rates.js

  let allRates = [];
  let filteredRates = [];
  let quickQuoteRateId = null;

  // ---------- HELPERS ----------------------------------------------------
  const $ = (id) => document.getElementById(id);

  const peso = (n) => '₱' + Number(n || 0).toLocaleString('en-PH', { maximumFractionDigits: 2 });

  const esc = (str) =>
    String(str ?? '').replace(/[&<>"']/g, (c) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

  const daysUntil = (dateStr) => {
    if (!dateStr) return null;
    const target = new Date(dateStr + 'T00:00:00');
    if (isNaN(target)) return null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.ceil((target - today) / 86400000);
  };

  const relativeTime = (stamp) => {
    const diff = Date.now() - Number(stamp || 0);
    if (!diff || diff < 0) return 'ngayon lang';
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'ngayon lang';
    if (mins < 60) return `${mins}m ago`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return days === 1 ? 'kahapon' : `${days}d ago`;
  };

  const show = (el, visible) => {
    if (!el) return;
    el.classList.toggle('hidden', !visible);
  };

  // ---------- QUOTE HISTORY (localStorage) -------------------------------
  const getHistory = () => {
    try { return JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]'); }
    catch (_) { return []; }
  };

  const saveHistory = (rows) => {
    try { localStorage.setItem(HISTORY_KEY, JSON.stringify(rows.slice(0, HISTORY_LIMIT))); }
    catch (_) { /* storage full / blocked */ }
  };

  const recordQuote = (detail) => {
    if (!detail || !detail.rate) return;
    const entry = {
      rateId: String(detail.rate.id),
      route: `${detail.rate.origin} → ${detail.rate.destination}`,
      carrier: detail.rate.carrier || '-',
      total: Number(detail.total || 0),
      kg: Number(detail.weight_kg || 0),
      cbm: Number(detail.cbm || 0),
      at: Date.now()
    };
    const rows = getHistory()
      .filter((r) => !(r.rateId === entry.rateId && r.kg === entry.kg && r.cbm === entry.cbm));
    rows.unshift(entry);
    saveHistory(rows);
    renderQuoteActivity();
  };

  // ---------- HERO MINI STATS -------------------------------------------
  const renderHeroStats = () => {
    const byMode = { AIR: 0, SEA: 0, LAND: 0 };
    allRates.forEach((r) => { if (byMode[r.mode] !== undefined) byMode[r.mode] += 1; });

    const set = (id, val) => { const el = $(id); if (el) el.textContent = val; };
    set('hero-stat-air', byMode.AIR);
    set('hero-stat-sea', byMode.SEA);
    set('hero-stat-land', byMode.LAND);

    const totalEl = $('hero-stat-total');
    if (totalEl) {
      totalEl.textContent = allRates.length;
      totalEl.setAttribute(
        'title',
        filteredRates.length && filteredRates.length !== allRates.length
          ? `${filteredRates.length} of ${allRates.length} rate cards match the current filters`
          : `${allRates.length} rate cards in the catalog`
      );
    }
  };

  // ---------- QUICK QUOTE ESTIMATOR -------------------------------------
  /** Datalist options: buong lane + mga indibidwal na origin/destination. */
  const buildLaneOptions = () => {
    const list = $('quick-quote-lanes');
    if (!list) return;

    const lanes = new Set();
    const places = new Set();
    allRates.forEach((r) => {
      lanes.add(`${r.origin} → ${r.destination}`);
      places.add(r.origin);
      places.add(r.destination);
    });

    const options = [...lanes, ...places].slice(0, 60)
      .map((v) => `<option value="${esc(v)}"></option>`).join('');
    list.innerHTML = options;
  };

  const currentQuickInputs = () => ({
    lane: ($('quick-quote-lane')?.value || '').trim(),
    mode: $('quick-quote-mode')?.value || '',
    weight: parseFloat($('quick-quote-weight')?.value) || 0,
    cbm: parseFloat($('quick-quote-cbm')?.value) || 0
  });

  /** Pinakamurang tumutugmang lane (all-in) para sa hinahanap na ruta/mode. */
  const findBestLane = ({ lane, mode, weight, cbm }) => {
    const q = lane.toLowerCase();
    const engine = window.RatesDashboard;
    if (!engine || !allRates.length) return null;

    const candidates = allRates.filter((r) =>
      (mode === '' || r.mode === mode) &&
      (q === '' || `${r.origin} ${r.destination}`.toLowerCase().includes(q))
    );
    if (!candidates.length) return null;

    let best = null;
    candidates.forEach((r) => {
      const est = engine.estimate(r.id, weight, cbm);
      if (!est) return;
      const score = Number(est.total || 0);
      if (
        !best ||
        score < best.total ||
        (score === best.total && r.transit_days < best.rate.transit_days)
      ) {
        best = { rate: r, total: score, breakdown: est.breakdown, matches: candidates.length };
      }
    });
    return best;
  };

  const renderQuickResult = (result, weight, cbm) => {
    const box = $('quick-quote-result');
    const empty = $('quick-quote-empty');

    if (!result) {
      quickQuoteRateId = null;
      show(box, false);
      show(empty, true);
      return;
    }

    quickQuoteRateId = result.rate.id;
    show(empty, false);

    const b = result.breakdown || {};
    const set = (id, val) => { const el = $(id); if (el) el.textContent = val; };
    set('quick-quote-rate-id', `#${result.rate.id}`);
    set('quick-quote-route', `${result.rate.origin} → ${result.rate.destination}`);
    set('quick-quote-transit', result.rate.transit_time);
    set('quick-quote-carrier', `${result.rate.carrier} • ${result.rate.mode} • ${result.rate.delivery_option}`);
    set('quick-quote-total', peso(result.total));
    set('quick-quote-breakdown',
      `${weight}kg${cbm ? ` / ${cbm}cbm` : ''} • Base ${peso(b.base_freight)} + Pickup ${peso(b.trucking_pickup)} + Delivery ${peso(b.trucking_delivery)} + Docs ${peso(b.documentation_fee)} + Handling ${peso(b.handling_fee)}`);

    show(box, true);
  };

  const runQuickQuote = (e) => {
    if (e) e.preventDefault();
    const inputs = currentQuickInputs();
    const result = findBestLane(inputs);
    renderQuickResult(result, inputs.weight, inputs.cbm);
  };

  const openQuickQuoteInCalculator = () => {
    if (!quickQuoteRateId || !window.RatesDashboard) return;
    window.RatesDashboard.openModal(quickQuoteRateId);
  };

  // ---------- VALIDITY HEALTH -------------------------------------------
  const renderValidityHealth = () => {
    if (!$('validity-health-bar')) return;

    const buckets = { d7: 0, d21: 0, d45: 0, d45plus: 0 };
    let rated = 0;

    allRates.forEach((r) => {
      const d = daysUntil(r.valid_until);
      if (d === null) return;
      rated += 1;
      if (d <= 7) buckets.d7 += 1;            // kasama ang expired (urgent)
      else if (d <= 21) buckets.d21 += 1;
      else if (d <= EXPIRY_WINDOW_DAYS) buckets.d45 += 1;
      else buckets.d45plus += 1;
    });

    const pct = (n) => (rated ? Math.round((n / rated) * 100) : 0);
    const setBar = (id, n, label) => {
      const el = $(id);
      if (!el) return;
      el.style.width = `${pct(n)}%`;
      el.setAttribute('title', `${label}: ${n} rate card${n === 1 ? '' : 's'}`);
    };
    setBar('health-seg-d7', buckets.d7, '0-7 days (kasama ang expired)');
    setBar('health-seg-d21', buckets.d21, '8-21 days');
    setBar('health-seg-d45', buckets.d45, '22-45 days');
    setBar('health-seg-d45plus', buckets.d45plus, '45+ days');

    const setCount = (id, n) => { const el = $(id); if (el) el.textContent = n; };
    setCount('health-count-d7', buckets.d7);
    setCount('health-count-d21', buckets.d21);
    setCount('health-count-d45', buckets.d45);
    setCount('health-count-d45plus', buckets.d45plus);
  };

  const scrollToWatchlist = () => {
    const card = $('expiring-soon-card');
    if (!card) return;
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    card.classList.add('ring-2', 'ring-amber-300');
    window.setTimeout(() => card.classList.remove('ring-2', 'ring-amber-300'), 1400);
  };

  // ---------- CARRIER COVERAGE LEADERBOARD -------------------------------
  const renderCarrierLeaderboard = () => {
    const box = $('carrier-leaderboard-list');
    if (!box) return;

    if (!allRates.length) {
      box.innerHTML = '<div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400">No carriers to rank yet.</div>';
      return;
    }

    const carriers = new Map();
    allRates.forEach((r) => {
      const key = r.carrier || 'Unassigned';
      const row = carriers.get(key) || { lanes: 0, cheapest: Infinity };
      row.lanes += 1;
      row.cheapest = Math.min(row.cheapest, Number(r.base_price || 0));
      carriers.set(key, row);
    });

    const ranked = [...carriers.entries()]
      .sort((a, b) => b[1].lanes - a[1].lanes || a[1].cheapest - b[1].cheapest)
      .slice(0, 5);
    const maxLanes = Math.max(...ranked.map(([, v]) => v.lanes), 1);

    box.innerHTML = ranked.map(([name, row], i) => {
      const rankCls = i === 0 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600';
      return `
      <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition">
        <div class="flex items-center justify-between gap-2">
          <div class="flex items-center gap-2.5 min-w-0">
            <span class="w-6 h-6 rounded-lg text-[10px] font-black flex items-center justify-center shrink-0 ${rankCls}">${i + 1}</span>
            <p class="text-xs font-bold text-gray-800 truncate" title="${esc(name)}">${esc(name)}</p>
          </div>
          <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full shrink-0">${row.lanes} lane${row.lanes === 1 ? '' : 's'}</span>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <div class="flex-1 h-1.5 bg-white rounded-full overflow-hidden border border-gray-100">
            <div class="h-full bg-gradient-to-r from-[#689dff] to-[#3b70e6] rounded-full" style="width:${Math.round((row.lanes / maxLanes) * 100)}%"></div>
          </div>
          <span class="text-[10px] font-bold text-gray-400 shrink-0">from ${peso(row.cheapest)}</span>
        </div>
      </div>`;
    }).join('');
  };

  // ---------- RECENT QUOTE ACTIVITY --------------------------------------
  const renderQuoteActivity = () => {
    const box = $('quote-activity-list');
    if (!box) return;

    const rows = getHistory();
    if (!rows.length) {
      box.innerHTML = `<p id="activity-empty" class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400">
        Wala pang na-compute na quote. Pindutin ang <span class="font-bold text-gray-500">Calculate</span> sa isang rate card.
      </p>`;
      return;
    }

    box.innerHTML = rows.map((row) => `
      <button type="button" data-quote-rate="${esc(row.rateId)}" class="w-full text-left p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-indigo-200 hover:bg-white transition">
        <div class="flex items-start justify-between gap-2">
          <div class="min-w-0">
            <p class="text-xs font-bold text-gray-800 truncate">${esc(row.route)}</p>
            <p class="text-[10px] text-gray-400 mt-0.5 truncate">#${esc(row.rateId)} • ${esc(row.carrier)}</p>
          </div>
          <span class="text-[10px] font-semibold text-gray-400 shrink-0">${relativeTime(row.at)}</span>
        </div>
        <div class="flex items-center justify-between mt-2">
          <span class="text-[10px] font-bold text-gray-500 bg-white border border-gray-100 px-2 py-0.5 rounded-full">${esc(row.kg)}kg${Number(row.cbm) ? ` / ${esc(row.cbm)}cbm` : ''}</span>
          <span class="text-sm font-black text-emerald-600">${peso(row.total)}</span>
        </div>
      </button>`).join('');

    box.querySelectorAll('[data-quote-rate]').forEach((btn) => {
      btn.addEventListener('click', () => {
        if (window.RatesDashboard) window.RatesDashboard.openModal(btn.dataset.quoteRate);
      });
    });
  };

  const clearActivity = () => {
    saveHistory([]);
    renderQuoteActivity();
  };

  // ---------- KEYBOARD SHORTCUT ("/" -> lane search) ---------------------
  const isTypingTarget = (el) => {
    if (!el) return false;
    const tag = String(el.tagName || '').toLowerCase();
    return tag === 'input' || tag === 'select' || tag === 'textarea' || el.isContentEditable === true;
  };

  const setupKeyboard = () => {
    document.addEventListener('keydown', (e) => {
      if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
      if (isTypingTarget(e.target)) return;
      const input = $('filter-route');
      if (!input) return;
      e.preventDefault();
      input.scrollIntoView({ behavior: 'smooth', block: 'center' });
      input.focus();
    });
  };

  // ---------- WIRING -----------------------------------------------------
  const applyCatalog = (detail) => {
    const engine = window.RatesDashboard;
    allRates = Array.isArray(detail && detail.all) ? detail.all : (engine && engine.getRates ? engine.getRates() : []);
    filteredRates = Array.isArray(detail && detail.filtered) ? detail.filtered : allRates;

    renderHeroStats();
    buildLaneOptions();
    renderValidityHealth();
    renderCarrierLeaderboard();
  };

  const setupEventListeners = () => {
    document.addEventListener('rates:updated', (e) => applyCatalog(e.detail || {}));
    document.addEventListener('rates:quote-computed', (e) => recordQuote(e.detail || {}));

    $('quick-quote-form')?.addEventListener('submit', runQuickQuote);
    $('btn-quick-quote-open')?.addEventListener('click', openQuickQuoteInCalculator);
    $('quick-quote-mode')?.addEventListener('change', () => { if (quickQuoteRateId) runQuickQuote(); });
    $('btn-clear-activity')?.addEventListener('click', clearActivity);
    document.querySelectorAll('[data-health-scroll]').forEach((btn) => {
      btn.addEventListener('click', scrollToWatchlist);
    });

    setupKeyboard();
  };

  const init = () => {
    setupEventListeners();
    renderQuoteActivity();

    // Fallback kung nauna nang nag-load ang catalog bago ang script na ito.
    const existing = window.RatesDashboard && window.RatesDashboard.getRates ? window.RatesDashboard.getRates() : [];
    if (existing.length) {
      applyCatalog({ all: existing, filtered: window.RatesDashboard.getFiltered() });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // ---------- PUBLIC API -------------------------------------------------
  return {
    refresh: () => applyCatalog({}),
    getFiltered: () => filteredRates,
    renderQuoteActivity,
    clearActivity
  };
})();



