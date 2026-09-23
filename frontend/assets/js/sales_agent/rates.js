/**
 * Rate Search Dashboard - JavaScript Module
 * ------------------------------------------------------------------
 * Fetches live rates from FastAPI (/api/v1/sales-agent/rates),
 * normalizes the rate.json shape, and powers:
 *   - KPI stats (total, avg transit, lowest, expiring, favorites)
 *   - ApexCharts donut (distribution by mode) + mode quick-filter chips
 *   - Expiring-soon watchlist & cheapest-lanes widgets
 *   - Filtering / sorting / favorites / comparison / quote calculator
 * Falls back to an embedded local catalog when the API is unreachable.
 */
const RatesDashboard = (() => {
  const STORAGE_KEY = 'rate_favorites';
  const EXPIRY_WINDOW_DAYS = 45;
  const COMPARISON_LIMIT = 5;

  let allRates = [];
  let filteredRates = [];
  let comparisonList = [];
  let modeDonutChart = null;
  let currentView = 'comfortable';

  // ---------- HELPERS --------------------------------------------------
  const apiBase = () => (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL) || '';

  const peso = (n) =>
    '₱' + Number(n || 0).toLocaleString('en-PH', { maximumFractionDigits: 2 });

  const esc = (str) =>
    String(str ?? '').replace(/[&<>"']/g, (c) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

  const $ = (id) => document.getElementById(id);

  /** "Same Day" -> 1, "1-2 Days" -> 2, "5-7 Days" -> 7 ... */
  /** Ipapaalam sa dashboard widgets (rates_dashboard.js) ang bagong catalog/filter state. */
  const notify = () => {
    document.dispatchEvent(new CustomEvent('rates:updated', {
      detail: { all: allRates, filtered: filteredRates }
    }));
  };

  const parseTransitDays = (txt) => {
    if (!txt) return 1;
    if (String(txt).toLowerCase().includes('same day')) return 1;
    const nums = (String(txt).match(/\d+/g) || []).map(Number);
    return nums.length ? Math.max(...nums) : 1;
  };

  const daysUntil = (dateStr) => {
    if (!dateStr) return null;
    const target = new Date(dateStr + 'T00:00:00');
    if (isNaN(target)) return null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return Math.ceil((target - today) / 86400000);
  };

  /**
   * Best Value score = price rank + transit rank (mas mababa = mas magandang value).
   * Niraranggo sa loob ng kasalukuyang filtered list — walang rating field sa
   * rate.json, kaya price + speed ang gamit na value metric ng Best Value sort.
   */
  const valueScore = (rate) => {
    const pool = filteredRates.length ? filteredRates : allRates;
    if (!pool.length) return 0;
    const cheaper = pool.filter((r) => r.base_price < rate.base_price).length;
    const cheaperTie = pool.filter((r) => r.base_price === rate.base_price && r.id < rate.id).length;
    const faster = pool.filter((r) => r.transit_days < rate.transit_days).length;
    const fasterTie = pool.filter((r) => r.transit_days === rate.transit_days && r.id < rate.id).length;
    return (cheaper + cheaperTie) + (faster + fasterTie);
  };

  const getFavorites = () => {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch (_) { return []; }
  };
  const saveFavorites = (fav) => localStorage.setItem(STORAGE_KEY, JSON.stringify(fav));

  /** Base price from rate.json fields (or legacy base_price). */
  const extractBasePrice = (raw) => {
    if (raw.base_price != null) return Number(raw.base_price);
    if (raw.base_rate_per_kg != null) return Number(raw.base_rate_per_kg);
    if (raw.base_rate_per_cbm != null) return Number(raw.base_rate_per_cbm);
    if (raw.base_rate_flat != null) return Number(raw.base_rate_flat);
    return 0;
  };

  const priceUnit = (raw) => {
    if (raw.base_rate_per_cbm != null) return '/cbm';
    if (raw.base_rate_per_kg != null) return '/kg';
    return '';
  };

  /** Normalize API / mock rows into one view model. */
  const normalize = (raw) => ({
    id: String(raw.id),
    mode: String(raw.mode || 'LAND').toUpperCase(),
    type: raw.type || 'Local',
    origin: raw.origin || '-',
    destination: raw.destination || '-',
    service_type: raw.service_type || '-',
    delivery_option: raw.delivery_option || '-',
    carrier: raw.carrier || '-',
    transit_time: raw.transit_time || (raw.transit_days ? `${raw.transit_days} Days` : '-'),
    transit_days: raw.transit_days ?? parseTransitDays(raw.transit_time),
    valid_until: raw.valid_until || '',
    currency: raw.currency || 'PHP',
    min_weight_kg: raw.min_weight_kg ?? 1,
    min_cbm: raw.min_cbm ?? 0,
    base_price: extractBasePrice(raw),
    price_unit: priceUnit(raw),
    rating: raw.rating ?? null,
    raw
  });

  /** Embedded catalog (same shape as backend app/data/rate.json) — offline fallback. */
  const MOCK_RATES = [
    { id: 'RATE-001', mode: 'AIR', type: 'Local', origin: 'Manila (MNL)', destination: 'Cebu (CEB)', service_type: 'Airport-to-Airport', delivery_option: 'Port-to-Port', carrier: 'Philippine Airlines Cargo Express', min_weight_kg: 1, base_rate_per_kg: 110, trucking_pickup_fee: 0, trucking_delivery_fee: 0, documentation_fee: 300, handling_fee: 250, currency: 'PHP', transit_time: 'Same Day', valid_until: '2026-10-31' },
    { id: 'RATE-002', mode: 'AIR', type: 'Local', origin: 'Manila (MNL)', destination: 'Cebu (CEB)', service_type: 'Door-to-Door Express', delivery_option: 'Door-to-Door', carrier: 'Cebu Pacific Air Cargo', min_weight_kg: 1, base_rate_per_kg: 165, trucking_pickup_fee: 1500, trucking_delivery_fee: 1800, documentation_fee: 400, handling_fee: 400, currency: 'PHP', transit_time: 'Same Day', valid_until: '2026-10-12' },
    { id: 'RATE-003', mode: 'AIR', type: 'International', origin: 'Manila (MNL), Philippines', destination: 'Singapore (SIN), Singapore', service_type: 'Airport-to-Airport', delivery_option: 'Port-to-Port', carrier: 'Singapore Airlines Cargo', min_weight_kg: 45, base_rate_per_kg: 180, trucking_pickup_fee: 0, trucking_delivery_fee: 0, documentation_fee: 1500, handling_fee: 800, currency: 'PHP', transit_time: '1-2 Days', valid_until: '2026-11-15' },
    { id: 'RATE-010', mode: 'SEA', type: 'International', origin: 'Manila (MNL), Philippines', destination: 'Singapore (SIN), Singapore', service_type: 'Port-to-Port Ocean', delivery_option: 'Port-to-Port', carrier: 'Maersk Line', min_weight_kg: 1, base_rate_per_cbm: 4500, trucking_pickup_fee: 0, trucking_delivery_fee: 0, documentation_fee: 2500, handling_fee: 1200, currency: 'PHP', transit_time: '5-7 Days', valid_until: '2026-10-05' },
    { id: 'RATE-011', mode: 'SEA', type: 'Local', origin: 'Manila (MNL)', destination: 'Cebu (CEB)', service_type: 'Domestic Ocean Freight', delivery_option: 'Port-to-Port', carrier: '2GO Shipping', min_weight_kg: 1, base_rate_per_cbm: 1800, trucking_pickup_fee: 0, trucking_delivery_fee: 0, documentation_fee: 500, handling_fee: 350, currency: 'PHP', transit_time: '2-3 Days', valid_until: '2026-09-30' },
    { id: 'RATE-012', mode: 'SEA', type: 'International', origin: 'Manila (MNL), Philippines', destination: 'Bangkok (BKK), Thailand', service_type: 'Door-to-Door Sea', delivery_option: 'Door-to-Door', carrier: 'Evergreen Marine', min_weight_kg: 1, base_rate_per_cbm: 5200, trucking_pickup_fee: 2200, trucking_delivery_fee: 2600, documentation_fee: 2800, handling_fee: 1400, currency: 'PHP', transit_time: '5-7 Days', valid_until: '2026-11-30' },
    { id: 'RATE-020', mode: 'LAND', type: 'Local', origin: 'Manila (MNL)', destination: 'Baguio (BAG)', service_type: 'Door-to-Door Trucking', delivery_option: 'Door-to-Door', carrier: 'LBC Trucking', min_weight_kg: 1, base_rate_per_kg: 18, trucking_pickup_fee: 800, trucking_delivery_fee: 900, documentation_fee: 250, handling_fee: 200, currency: 'PHP', transit_time: '1-2 Days', valid_until: '2026-10-20' },
    { id: 'RATE-021', mode: 'LAND', type: 'Local', origin: 'Manila (MNL)', destination: 'Davao (DVO)', service_type: 'Provincial Trucking', delivery_option: 'Door-to-Door', carrier: 'Victory Liner Cargo', min_weight_kg: 1, base_rate_per_kg: 22, trucking_pickup_fee: 1000, trucking_delivery_fee: 1200, documentation_fee: 300, handling_fee: 250, currency: 'PHP', transit_time: '2-3 Days', valid_until: '2026-10-12' },
    { id: 'RATE-022', mode: 'LAND', type: 'Local', origin: 'Manila (MNL)', destination: 'Cebu (CEB)', service_type: 'Port Trucking', delivery_option: 'Port-to-Port', carrier: 'Fuji Express', min_weight_kg: 1, base_rate_per_kg: 15, trucking_pickup_fee: 0, trucking_delivery_fee: 0, documentation_fee: 200, handling_fee: 180, currency: 'PHP', transit_time: '3-5 Days', valid_until: '2026-11-08' }
  ];

  // ---------- DATA LOADING --------------------------------------------
  const loadRates = async () => {
    try {
      const res = await fetch(`${apiBase()}/api/v1/sales-agent/rates`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();
      const rows = Array.isArray(json) ? json : (json.data || []);
      if (!rows.length) throw new Error('empty catalog');
      allRates = rows.map(normalize);
    } catch (err) {
      console.warn('[RatesDashboard] API unavailable, using local catalog:', err.message);
      allRates = MOCK_RATES.map(normalize);
    }

    // Preserve favorites/compare selections that still exist in the catalog
    comparisonList = comparisonList.filter((id) => allRates.some((r) => r.id === id));

    filteredRates = [...allRates];
    const stamp = $('rates-last-updated');
    if (stamp) {
      stamp.innerHTML = `<i class="fa-regular fa-clock mr-1"></i>Updated ${new Date().toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' })}`;
    }
    renderAll();
  };

  // ---------- FILTERING & SORTING -------------------------------------
  const currentFilters = () => ({
    route: ($('filter-route')?.value || '').toLowerCase().trim(),
    mode: $('filter-mode')?.value || '',
    type: $('filter-type')?.value || '',
    delivery: $('filter-delivery-option')?.value || '',
    transit: $('filter-transit-time')?.value || ''
  });

  const applyFilters = () => {
    const f = currentFilters();
    filteredRates = allRates.filter((r) =>
      (f.route === '' || r.origin.toLowerCase().includes(f.route) || r.destination.toLowerCase().includes(f.route)) &&
      (f.mode === '' || r.mode === f.mode) &&
      (f.type === '' || r.type === f.type) &&
      (f.delivery === '' || r.delivery_option === f.delivery) &&
      (f.transit === '' || r.transit_time.toLowerCase().includes(f.transit.toLowerCase()))
    );
    sortRates();
  };

  const clearFilters = () => {
    ['filter-route', 'filter-mode', 'filter-type', 'filter-delivery-option', 'filter-transit-time']
      .forEach((id) => { const el = $(id); if (el) el.value = ''; });
    filteredRates = [...allRates];
    sortRates();
  };

  const sortRates = () => {
    const sortBy = $('sort-by')?.value || 'price-asc';
    filteredRates.sort((a, b) => {
      switch (sortBy) {
        case 'price-asc': return a.base_price - b.base_price;
        case 'price-desc': return b.base_price - a.base_price;
        case 'transit-asc': return a.transit_days - b.transit_days;
        case 'rating': return (b.rating || 0) - (a.rating || 0);
        case 'best-value': return valueScore(a) - valueScore(b);
        default: return 0;
      }
    });
    renderRates();
    renderCheapestLanes();
    updateStats();
    renderActiveFilters();
    renderModeChips();
    notify();
  };

  /** Removable chips showing which filters are active. */
  const renderActiveFilters = () => {
    const box = $('active-filters-container');
    if (!box) return;
    const labels = {
      'filter-route': (v) => `Route: "${v}"`,
      'filter-mode': (v) => `Mode: ${v}`,
      'filter-type': (v) => `Coverage: ${v}`,
      'filter-delivery-option': (v) => `Service: ${v}`,
      'filter-transit-time': (v) => `Transit: ${v}`
    };
    const chips = Object.entries(labels)
      .map(([id, label]) => {
        const el = $(id);
        const val = el ? el.value : '';
        if (!val) return '';
        return `<button type="button" data-clear-filter="${id}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100 px-2.5 py-1 rounded-full hover:bg-indigo-100 transition">
          ${esc(label(val))} <i class="fa-solid fa-xmark text-[9px]"></i>
        </button>`;
      })
      .filter(Boolean);

    box.innerHTML = chips.join('');
    box.classList.toggle('hidden', chips.length === 0);

    box.querySelectorAll('[data-clear-filter]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const el = $(btn.dataset.clearFilter);
        if (el) { el.value = ''; applyFilters(); }
      });
    });
  };

  // ---------- KPI STATS -------------------------------------------------
  const updateStats = () => {
    const pool = filteredRates.length ? filteredRates : [];
    const fav = getFavorites();

    const totalEl = $('stat-total-rates');
    if (totalEl) totalEl.textContent = allRates.length;

    const avgEl = $('stat-avg-transit');
    if (avgEl) {
      avgEl.textContent = pool.length
        ? `${Math.round(pool.reduce((s, r) => s + r.transit_days, 0) / pool.length)} days`
        : '—';
    }

    const lowEl = $('stat-lowest-rate');
    if (lowEl) {
      lowEl.textContent = pool.length
        ? peso(Math.min(...pool.map((r) => r.base_price)))
        : '—';
    }

    const expEl = $('stat-expiring');
    if (expEl) {
      expEl.textContent = allRates.filter((r) => {
        const d = daysUntil(r.valid_until);
        return d !== null && d >= 0 && d <= EXPIRY_WINDOW_DAYS;
      }).length;
    }

    const favEl = $('stat-favorites');
    if (favEl) {
      favEl.textContent = fav.filter((id) => allRates.some((r) => r.id === id)).length;
    }
  };

  // ---------- MODE QUICK-FILTER CHIPS ----------------------------------
  const MODE_META = {
    AIR:  { icon: 'fa-plane',          label: 'Air' },
    SEA:  { icon: 'fa-ship',           label: 'Sea' },
    LAND: { icon: 'fa-truck-fast',     label: 'Land' }
  };

  const renderModeChips = () => {
    const box = $('mode-chips');
    if (!box) return;
    const activeMode = $('filter-mode')?.value || '';

    const chips = ['AIR', 'SEA', 'LAND'].map((mode) => {
      const count = allRates.filter((r) => r.mode === mode).length;
      const meta = MODE_META[mode];
      const active = activeMode === mode;
      const activeCls = active
        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-600/20'
        : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300 hover:text-indigo-600';
      return `<button type="button" data-mode-chip="${mode}" class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-full border transition ${activeCls}">
        <i class="fa-solid ${meta.icon} text-[10px]"></i> ${meta.label}
        <span class="${active ? 'bg-white/20' : 'bg-gray-100'} px-1.5 py-0.5 rounded-full text-[10px]">${count}</span>
      </button>`;
    });

    const allActive = activeMode === '';
    chips.unshift(`<button type="button" data-mode-chip="" class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-full border transition ${allActive ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-600/20' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300 hover:text-indigo-600'}">
      <i class="fa-solid fa-border-all text-[10px]"></i> All
    </button>`);

    box.innerHTML = chips.join('');
    box.querySelectorAll('[data-mode-chip]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const sel = $('filter-mode');
        if (sel) sel.value = btn.dataset.modeChip;
        applyFilters();
      });
    });
  };

  // ---------- DONUT CHART (ApexCharts — already loaded in header) ------
  const renderDonut = () => {
    const el = $('modeDonutChart');
    if (!el || typeof ApexCharts === 'undefined') return;

    const labels = ['AIR', 'SEA', 'LAND'];
    const series = labels.map((m) => allRates.filter((r) => r.mode === m).length);
    const colors = ['#4f46e5', '#0ea5e9', '#f59e0b'];

    if (modeDonutChart) { modeDonutChart.destroy(); modeDonutChart = null; }

    modeDonutChart = new ApexCharts(el, {
      chart: { type: 'donut', height: 220, fontFamily: 'Inter, sans-serif' },
      labels: labels.map((m) => MODE_META[m].label + ' Freight'),
      series,
      colors,
      stroke: { width: 0 },
      dataLabels: { enabled: false },
      legend: { position: 'bottom', fontSize: '11px', fontWeight: 600, labels: { colors: '#64748b' }, markers: { width: 8, height: 8, radius: 8 } },
      plotOptions: { pie: { donut: { size: '68%', labels: { show: true,
        name: { show: true, fontSize: '11px', fontWeight: 600, color: '#94a3b8' },
        value: { show: true, fontSize: '22px', fontWeight: 900, color: '#111827', formatter: (v) => v },
        total: { show: true, label: 'Total Rates', color: '#94a3b8', formatter: () => allRates.length }
      } } } },
      tooltip: { theme: 'light' }
    });
    modeDonutChart.render();
  };

  // ---------- EXPIRING SOON WIDGET -------------------------------------
  const renderExpiringSoon = () => {
    const box = $('expiring-soon-list');
    if (!box) return;

    const soon = allRates
      .map((r) => ({ ...r, days_left: daysUntil(r.valid_until) }))
      .filter((r) => r.days_left !== null)
      .sort((a, b) => a.days_left - b.days_left)
      .slice(0, 4);

    if (!soon.length) {
      box.innerHTML = `<div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400">No validity dates available.</div>`;
      return;
    }

    box.innerHTML = soon.map((r) => {
      const expired = r.days_left < 0;
      const urgency = expired
        ? { bg: 'bg-gray-100', text: 'text-gray-500', label: 'Expired' }
        : r.days_left <= 14
          ? { bg: 'bg-rose-50', text: 'text-rose-600', label: `${r.days_left}d left` }
          : { bg: 'bg-amber-50', text: 'text-amber-600', label: `${r.days_left}d left` };

      return `<div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-gray-200 transition">
        <div class="min-w-0">
          <p class="text-xs font-bold text-gray-800 truncate">${esc(r.origin)} → ${esc(r.destination)}</p>
          <p class="text-[10px] text-gray-400 mt-0.5"><i class="fa-regular fa-calendar mr-1"></i>Valid until ${esc(r.valid_until)}</p>
        </div>
        <span class="shrink-0 text-[10px] font-bold px-2 py-1 rounded-full ${urgency.bg} ${urgency.text}">${urgency.label}</span>
      </div>`;
    }).join('');
  };

  // ---------- CHEAPEST LANES WIDGET ------------------------------------
  const renderCheapestLanes = () => {
    const box = $('cheapest-lanes-list');
    if (!box) return;

    const source = (filteredRates.length ? filteredRates : allRates)
      .slice()
      .sort((a, b) => a.base_price - b.base_price)
      .slice(0, 3);

    if (!source.length) {
      box.innerHTML = `<div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400">No rates to rank yet.</div>`;
      return;
    }

    const rankColors = ['bg-emerald-100 text-emerald-700', 'bg-teal-100 text-teal-700', 'bg-sky-100 text-sky-700'];

    box.innerHTML = source.map((r, i) => `
      <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-emerald-50 bg-emerald-50/40">
        <div class="flex items-center gap-3 min-w-0">
          <span class="w-7 h-7 rounded-lg text-[11px] font-black flex items-center justify-center shrink-0 ${rankColors[i]}">#${i + 1}</span>
          <div class="min-w-0">
            <p class="text-xs font-bold text-gray-800 truncate">${esc(r.origin)} → ${esc(r.destination)}</p>
            <p class="text-[10px] text-gray-400 mt-0.5"><i class="fa-solid ${MODE_META[r.mode]?.icon || 'fa-truck'} mr-1"></i>${esc(r.mode)} • ${esc(r.transit_time)}</p>
          </div>
        </div>
        <div class="text-right shrink-0">
          <p class="text-sm font-black text-emerald-600">${peso(r.base_price)}<span class="text-[10px] font-bold text-gray-400">${esc(r.price_unit)}</span></p>
          <button type="button" onclick="RatesDashboard.openModal('${r.id}')" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">Compute →</button>
        </div>
      </div>`).join('');
  };

  // ---------- ORCHESTRATOR ---------------------------------------------
  const renderAll = () => {
    renderModeChips();
    renderDonut();
    renderExpiringSoon();
    renderCheapestLanes();
    updateStats();
    renderActiveFilters();
    renderRates();
    updateComparisonUI();
    notify();
  };

  // ---------- RATE CARDS GRID ------------------------------------------
  const RIBBON = {
    AIR:  'from-indigo-600 via-indigo-500 to-sky-400',
    SEA:  'from-blue-600 via-cyan-500 to-teal-400',
    LAND: 'from-amber-500 via-orange-500 to-rose-400'
  };

  const modeBadge = (mode) => {
    const map = { AIR: 'bg-indigo-50 text-indigo-600', SEA: 'bg-sky-50 text-sky-600', LAND: 'bg-amber-50 text-amber-600' };
    return `<span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full ${map[mode] || 'bg-gray-100 text-gray-600'}">
      <i class="fa-solid ${MODE_META[mode]?.icon || 'fa-truck'} text-[9px]"></i>${esc(mode)}</span>`;
  };

  const freshnessBadge = (validUntil) => {
    const d = daysUntil(validUntil);
    if (d === null) return '';
    if (d < 0) return `<span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Expired</span>`;
    const cls = d <= 7 ? 'text-rose-600 bg-rose-50'
      : d <= 21 ? 'text-amber-600 bg-amber-50'
      : 'text-emerald-600 bg-emerald-50';
    return `<span class="text-[10px] font-bold ${cls} px-2 py-0.5 rounded-full">${d}d left</span>`;
  };

  const renderRates = () => {
    const grid = $('rates-cards-grid');
    const countEl = $('result-count');
    const empty = $('empty-state');
    if (!grid) return;

    if (countEl) countEl.textContent = `${filteredRates.length} rate${filteredRates.length === 1 ? '' : 's'} found`;
    if (empty) empty.classList.toggle('hidden', filteredRates.length > 0);
    grid.classList.toggle('hidden', filteredRates.length === 0);
    if (!filteredRates.length) { grid.innerHTML = ''; return; }

    const favorites = getFavorites();

    grid.innerHTML = filteredRates.map((r) => {
      const isFav = favorites.includes(r.id);
      const inCompare = comparisonList.includes(r.id);
      const typeCls = r.type === 'International'
        ? 'bg-purple-50 text-purple-600 border-purple-100'
        : 'bg-gray-50 text-gray-500 border-gray-100';

      return `
      <div class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all overflow-hidden flex flex-col">
        <div class="h-1.5 bg-gradient-to-r ${RIBBON[r.mode] || 'from-gray-500 to-gray-400'}"></div>

        <div class="p-5 flex flex-col flex-1">
          <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">#${esc(r.id)}</span>
                ${modeBadge(r.mode)}
              </div>
              <h3 class="text-sm font-bold text-gray-900 truncate" title="${esc(r.origin)} → ${esc(r.destination)}">
                ${esc(r.origin)} <span class="text-indigo-500 font-black mx-0.5">→</span> ${esc(r.destination)}
              </h3>
              <p class="text-xs text-gray-500 truncate mt-0.5"><i class="fa-solid fa-truck-field mr-1 text-gray-400"></i>${esc(r.carrier)}</p>
            </div>
            <button type="button" onclick="RatesDashboard.toggleFavorite('${r.id}')" title="Favorite"
              class="shrink-0 w-8 h-8 rounded-xl flex items-center justify-center transition ${isFav ? 'bg-rose-50 text-rose-500' : 'text-gray-300 hover:text-rose-500 hover:bg-rose-50'}">
              <i class="fa-${isFav ? 'solid' : 'regular'} fa-heart text-sm"></i>
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-1.5 mb-4">
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border ${typeCls}">${esc(r.type)}</span>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-500">${esc(r.delivery_option)}</span>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600"><i class="fa-regular fa-clock mr-1"></i>${esc(r.transit_time)}</span>
            ${freshnessBadge(r.valid_until)}
          </div>

          <dl class="space-y-1.5 text-[11px] text-gray-500 mb-4 border-t border-dashed border-gray-100 pt-3">
            <div class="flex justify-between gap-2"><dt class="text-gray-400">Service</dt><dd class="font-semibold text-gray-700 text-right truncate">${esc(r.service_type)}</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-400">Coverage</dt><dd class="font-semibold text-gray-700">${esc(r.type)} • ${esc(r.mode)}</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-400">Valid until</dt><dd class="font-semibold text-gray-700">${esc(r.valid_until || '—')}</dd></div>
          </dl>

          <div class="mt-auto space-y-3">
            <div class="bg-gray-50 rounded-xl p-3 flex items-end justify-between border border-gray-100">
              <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Base Rate</span>
                <p class="text-2xl font-black text-emerald-600 leading-tight">${peso(r.base_price)}<span class="text-xs font-bold text-gray-400">${esc(r.price_unit)}</span></p>
              </div>
              <span class="text-[10px] text-gray-400 text-right">min ${esc(r.min_weight_kg)}kg${Number(r.min_cbm) ? `<br>${esc(r.min_cbm)} cbm` : ''}</span>
            </div>

            <div class="flex gap-2">
              <button type="button" onclick="RatesDashboard.openModal('${r.id}')" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-calculator text-[10px]"></i> Calculate
              </button>
              <button type="button" onclick="RatesDashboard.addComparison('${r.id}')"
                class="flex-1 py-2 text-xs font-bold rounded-xl transition border flex items-center justify-center gap-1.5 ${inCompare ? 'bg-slate-900 border-slate-900 text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-slate-900 hover:text-slate-900'}">
                <i class="fa-solid fa-scale-balanced text-[10px]"></i> ${inCompare ? 'Added' : 'Compare'}
              </button>
            </div>
          </div>
        </div>
      </div>`;
    }).join('');
  };

  // ---------- FAVORITES (localStorage) ---------------------------------
  const toggleFavorite = (rateId) => {
    const id = String(rateId);
    let favorites = getFavorites();
    if (favorites.includes(id)) favorites = favorites.filter((f) => f !== id);
    else favorites.push(id);
    saveFavorites(favorites);
    updateStats();
    renderRates();
  };

  // ---------- COMPARISON ------------------------------------------------
  const addComparison = (rateId) => {
    const id = String(rateId);
    if (comparisonList.includes(id)) { toggleComparison(); renderRates(); return; }
    if (comparisonList.length >= COMPARISON_LIMIT) {
      alert(`You can only compare up to ${COMPARISON_LIMIT} rates.`);
      return;
    }
    comparisonList.push(id);
    updateComparisonUI();
    renderRates();
  };

  const removeComparison = (rateId) => {
    comparisonList = comparisonList.filter((id) => id !== String(rateId));
    updateComparisonUI();
    renderRates();
  };

  const updateComparisonUI = () => {
    const countEl = $('comparison-count');
    const btn = $('btn-comparison-panel');
    const items = $('comparison-items');
    if (countEl) countEl.textContent = comparisonList.length;
    if (btn) btn.style.display = comparisonList.length ? 'inline-flex' : 'none';
    if (!items) return;

    if (!comparisonList.length) {
      items.innerHTML = `<p class="text-xs text-gray-400 text-center py-4">Walang idinagdag para i-compare.</p>`;
      return;
    }

    items.innerHTML = comparisonList.map((id) => {
      const r = allRates.find((x) => x.id === id);
      if (!r) return '';
      return `<div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
        <div class="flex justify-between items-start gap-2">
          <div class="min-w-0">
            <p class="text-xs font-bold text-gray-800 truncate">${esc(r.origin)} → ${esc(r.destination)}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">${esc(r.carrier)} • ${esc(r.transit_time)}</p>
          </div>
          <button type="button" onclick="RatesDashboard.removeComparison('${r.id}')" class="text-gray-400 hover:text-rose-500 text-sm leading-none shrink-0"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="flex justify-between items-end mt-2">
          <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">${esc(r.mode)} • ${esc(r.type)}</span>
          <span class="text-sm font-black text-emerald-600">${peso(r.base_price)}<span class="text-[10px] text-gray-400">${esc(r.price_unit)}</span></span>
        </div>
      </div>`;
    }).join('');
  };

  const toggleComparison = () => {
    const panel = $('comparison-panel');
    if (panel) panel.classList.toggle('translate-y-full');
  };

  // ---------- QUOTE CALCULATION MODAL ----------------------------------
  const openModal = (rateId) => {
    const id = String(rateId);
    const rate = allRates.find((r) => r.id === id);
    if (!rate) return;
    const modal = $('rate-modal');
    if (!modal) return;

    const set = (key, val) => { const el = $(key); if (el) el.textContent = val; };
    set('modal-route', `${rate.origin} → ${rate.destination}`);
    set('modal-carrier-info', `Carrier: ${rate.carrier}`);
    set('modal-mode', `${MODE_META[rate.mode]?.label || rate.mode} Freight`);
    set('modal-service', rate.delivery_option);
    set('modal-transit', rate.transit_time);
    set('modal-validity', rate.valid_until ? `Valid until ${rate.valid_until}` : 'No validity set');

    const idField = $('modal-rate-id');
    if (idField) idField.value = rate.id;

    const weight = $('modal-input-weight');
    const cbm = $('modal-input-cbm');
    if (weight) weight.value = rate.min_weight_kg || 1;
    if (cbm) cbm.value = 0;

    const result = $('quote-breakdown-result');
    if (result) result.classList.add('hidden');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  };

  const closeModal = () => {
    const modal = $('rate-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
  };

  const renderBreakdown = (breakdown, total) => {
    const set = (key, val) => { const el = $(key); if (el) el.textContent = peso(val); };
    set('res-base', breakdown.base_freight);
    set('res-pickup', breakdown.trucking_pickup);
    set('res-delivery', breakdown.trucking_delivery);
    set('res-docs', breakdown.documentation_fee);
    set('res-handling', breakdown.handling_fee);
    set('res-total', total);
    const result = $('quote-breakdown-result');
    if (result) result.classList.remove('hidden');
  };

  /** Local fallback mirroring backend-api rates.py calculate logic. */
  const computeLocally = (rate, weightKg, cbm) => {
    const raw = rate.raw;
    const w = Math.max(weightKg || 0, Number(raw.min_weight_kg || 0));
    const v = Math.max(cbm || 0, Number(raw.min_cbm || 0));

    let base = 0;
    if (raw.base_rate_per_kg != null) base = w * Number(raw.base_rate_per_kg);
    else if (raw.base_rate_per_cbm != null) base = v * Number(raw.base_rate_per_cbm);
    else base = Number(raw.base_rate_flat ?? raw.base_price ?? 0);

    const isD2D = String(rate.delivery_option).toLowerCase() === 'door-to-door';
    const breakdown = {
      base_freight: base,
      trucking_pickup: isD2D ? Number(raw.trucking_pickup_fee || 0) : 0,
      trucking_delivery: isD2D ? Number(raw.trucking_delivery_fee || 0) : 0,
      documentation_fee: Number(raw.documentation_fee || 0),
      handling_fee: Number(raw.handling_fee || 0)
    };
    const total = Object.values(breakdown).reduce((s, x) => s + x, 0);
    return { breakdown, total };
  };

  const calculateQuote = async (e) => {
    if (e) e.preventDefault();
    const idField = $('modal-rate-id');
    const rate = allRates.find((r) => r.id === String(idField?.value));
    if (!rate) return;

    const weightKg = parseFloat($('modal-input-weight')?.value) || 1;
    const cbm = parseFloat($('modal-input-cbm')?.value) || 0;

    let result = null;
    try {
      const res = await fetch(`${apiBase()}/api/v1/sales-agent/rates/calculate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rate_id: rate.id, weight_kg: weightKg, cbm })
      });
      if (res.ok) {
        const json = await res.json();
        if (json.status === 'success' && json.breakdown) {
          result = { breakdown: json.breakdown, total: Number(json.total_quotation) };
        }
      }
    } catch (err) {
      console.warn('[RatesDashboard] calculate API unavailable, computing locally:', err.message);
    }

    if (!result) result = computeLocally(rate, weightKg, cbm);
    renderBreakdown(result.breakdown, result.total);
    document.dispatchEvent(new CustomEvent('rates:quote-computed', {
      detail: {
        rate: {
          id: rate.id,
          mode: rate.mode,
          origin: rate.origin,
          destination: rate.destination,
          carrier: rate.carrier,
          transit_time: rate.transit_time,
          price_unit: rate.price_unit
        },
        total: result.total,
        breakdown: result.breakdown,
        weight_kg: weightKg,
        cbm: cbm
      }
    }));
  };

  const copyQuote = async () => {
    const result = $('quote-breakdown-result');
    if (!result || result.classList.contains('hidden')) { alert('Compute a quotation first.'); return; }
    const text = [
      `Quote for ${$('modal-route')?.textContent || ''}`,
      `Carrier: ${$('modal-carrier-info')?.textContent || ''}`,
      `Weight: ${$('modal-input-weight')?.value || '-'} kg | CBM: ${$('modal-input-cbm')?.value || '-'} cbm`,
      `Base: ${$('res-base')?.textContent} | Pickup: ${$('res-pickup')?.textContent} | Delivery: ${$('res-delivery')?.textContent}`,
      `Docs: ${$('res-docs')?.textContent} | Handling: ${$('res-handling')?.textContent}`,
      `TOTAL: ${$('res-total')?.textContent}`
    ].join('\n');
    try {
      await navigator.clipboard.writeText(text);
      alert('Quote summary copied to clipboard.');
    } catch (_) { alert('Copy not supported on this browser.'); }
  };

  const sendQuote = () => {
    const result = $('quote-breakdown-result');
    if (!result || result.classList.contains('hidden')) { alert('Compute a quotation first.'); return; }
    alert('Quote sending is not yet wired to a messaging backend — copy the summary and send it manually to the customer.');
  };

  // ---------- CSV EXPORT ------------------------------------------------
  const exportCsv = (rows, filename) => {
    if (!rows.length) { alert('No rates to export.'); return; }
    const headers = ['ID', 'Mode', 'Type', 'Origin', 'Destination', 'Service', 'Delivery Option', 'Carrier', 'Transit', 'Base Price (PHP)', 'Unit', 'Valid Until'];
    const csvRows = rows.map((r) => [
      r.id, r.mode, r.type, r.origin, r.destination, r.service_type, r.delivery_option,
      r.carrier, r.transit_time, r.base_price, r.price_unit, r.valid_until
    ].map((v) => `"${String(v ?? '').replace(/"/g, '""')}"`).join(','));

    const csv = [headers.join(','), ...csvRows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
  };

  // ---------- VIEW DENSITY TOGGLE --------------------------------------
  const setView = (view) => {
    currentView = view;
    const grid = $('rates-cards-grid');
    const comfort = $('btn-view-comfortable');
    const compact = $('btn-view-compact');
    if (grid) {
      grid.classList.remove('lg:grid-cols-3', 'lg:grid-cols-4');
      grid.classList.add(view === 'compact' ? 'lg:grid-cols-4' : 'lg:grid-cols-3');
    }
    const activeCls = 'px-2.5 py-1.5 rounded-lg text-xs bg-white text-gray-900 shadow-sm transition';
    const idleCls = 'px-2.5 py-1.5 rounded-lg text-xs text-gray-500 hover:text-gray-900 transition';
    if (comfort) comfort.className = view === 'comfortable' ? activeCls : idleCls;
    if (compact) compact.className = view === 'compact' ? activeCls : idleCls;
  };

  // ---------- EVENT LISTENERS ------------------------------------------
  const setupEventListeners = () => {
    $('btn-search-rates')?.addEventListener('click', applyFilters);
    $('btn-clear-filters')?.addEventListener('click', clearFilters);
    $('filter-route')?.addEventListener('input', applyFilters);
    ['filter-mode', 'filter-type', 'filter-delivery-option', 'filter-transit-time', 'sort-by']
      .forEach((id) => $(id)?.addEventListener('change', applyFilters));

    $('btn-refresh-rates')?.addEventListener('click', loadRates);
    $('btn-export-all')?.addEventListener('click', () => exportCsv(filteredRates, 'rate_search_results.csv'));
    $('btn-export-rates')?.addEventListener('click', () =>
      exportCsv(comparisonList.map((id) => allRates.find((r) => r.id === id)).filter(Boolean), 'rate_comparison.csv'));

    $('btn-comparison-panel')?.addEventListener('click', toggleComparison);
    $('btn-close-comparison')?.addEventListener('click', toggleComparison);
    $('btn-clear-comparison')?.addEventListener('click', () => {
      comparisonList = [];
      updateComparisonUI();
      renderRates();
    });

    $('btn-view-comfortable')?.addEventListener('click', () => setView('comfortable'));
    $('btn-view-compact')?.addEventListener('click', () => setView('compact'));

    $('form-calculate-quote')?.addEventListener('submit', calculateQuote);
    $('btn-close-modal')?.addEventListener('click', closeModal);
    $('rate-modal')?.addEventListener('click', (e) => { if (e.target.id === 'rate-modal') closeModal(); });
    $('btn-copy-quote')?.addEventListener('click', copyQuote);
    $('btn-send-quote')?.addEventListener('click', sendQuote);

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
  };

  // ---------- INIT ------------------------------------------------------
  const init = () => {
    setupEventListeners();
    setView(currentView);
    loadRates();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // ---------- PUBLIC API (inline onclick handlers) ----------------------
  return {
    toggleFavorite,
    addComparison,
    removeComparison,
    openModal,
    closeModal,
    calculateQuote,
    toggleComparison,
    refresh: loadRates,
    getRates: () => allRates,
    getFiltered: () => filteredRates,
    estimate: (rateId, weightKg, cbm) => {
      const rate = allRates.find((r) => r.id === String(rateId));
      return rate ? computeLocally(rate, weightKg, cbm) : null;
    }
  };
})();
