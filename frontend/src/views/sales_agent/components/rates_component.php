<!-- ==================================================================================
     RATE SEARCH DASHBOARD  (Sales Agent · Deals & Offers)
     ----------------------------------------------------------------------------------
     Tailwind-only redesign (walang separate CSS file) — sinusunod ang aesthetic ng
     buong portal: white rounded-2xl cards, gray-100 borders, uppercase tracking-wider
     labels, font-black numbers, gradient BI hero, indigo/purple accents, Inter font.

     ⚠ ID CONTRACT: Bawat id="" dito ay binabasa ng:
        - assets/js/sales_agent/rates.js            (core: filters, results, compare, modal)
        - assets/js/sales_agent/rates_dashboard.js  (hero stats, quick quote, rail widgets)
       Huwag palitan ang mga pangalan ng ID.
     ================================================================================== -->
<div class="max-w-[1600px] mx-auto space-y-6">

    <!-- ============================================================================
         ROW 1 — HERO COMMAND BANNER (title + live status + quick actions + estimator)
         ============================================================================ -->
    <section class="relative overflow-hidden rounded-[2.2rem] bg-gradient-to-br from-[#689dff] via-[#4b82f6] to-[#3b70e6] p-6 lg:p-7 text-white shadow-sm">

        <!-- Decorative glow blobs -->
        <div class="pointer-events-none absolute -top-24 -right-16 w-64 h-64 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-28 left-1/3 w-56 h-56 rounded-full bg-white/10 blur-2xl"></div>

        <div class="relative flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">

            <!-- Title + live status -->
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-tags text-lg"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-2xl font-black tracking-tight">Rate Card Search &amp; Calculator</h1>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider bg-white/15 border border-white/25 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            Live Rates
                        </span>
                    </div>
                    <p class="text-sm text-white/80 mt-1">Mabilisang paghanap, pag-compare at pag-compute ng freight rates para sa customer.</p>
                    <p id="rates-last-updated" class="text-[11px] font-semibold text-white/70 mt-2">
                        <i class="fa-regular fa-clock mr-1"></i>&mdash;
                    </p>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" id="btn-refresh-rates" class="px-3.5 py-2 bg-white/15 hover:bg-white/25 border border-white/25 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate text-[10px]"></i> Refresh
                </button>
                <button type="button" id="btn-export-all" class="px-3.5 py-2 bg-white text-[#3b70e6] hover:bg-white/90 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-file-csv text-[10px]"></i> Export Results
                </button>
            </div>
        </div>

        <!-- Mini stats strip -->
        <div class="relative grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
            <div class="bg-white/10 border border-white/20 rounded-2xl px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/70">Total Rate Cards</p>
                <p id="hero-stat-total" class="text-2xl font-black leading-tight">0</p>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-2xl px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/70"><i class="fa-solid fa-plane mr-1"></i>Air</p>
                <p id="hero-stat-air" class="text-2xl font-black leading-tight">0</p>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-2xl px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/70"><i class="fa-solid fa-ship mr-1"></i>Sea</p>
                <p id="hero-stat-sea" class="text-2xl font-black leading-tight">0</p>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-2xl px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-white/70"><i class="fa-solid fa-truck-fast mr-1"></i>Land</p>
                <p id="hero-stat-land" class="text-2xl font-black leading-tight">0</p>
            </div>
        </div>

        <!-- ---------------------------------------------------------------
             QUICK QUOTE ESTIMATOR (bagong feature — instant all-in estimate)
             --------------------------------------------------------------- -->
        <div class="relative mt-4 bg-white rounded-2xl border border-white/50 shadow-lg shadow-blue-900/10 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-bolt text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Quick Quote Estimator</h2>
                        <p class="text-[11px] text-gray-400">Ilagay ang lane at bigat — agad na lalabas ang pinakamurang all-in estimate.</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Base + Trucking + Docs + Handling</span>
            </div>

            <form id="quick-quote-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-4">
                    <label for="quick-quote-lane" class="block text-xs font-semibold text-gray-600 mb-1">Lane / Route</label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot text-xs text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input list="quick-quote-lanes" type="text" id="quick-quote-lane" placeholder="Manila o Cebu o Singapore..."
                               class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 pl-8 pr-3 py-2 text-gray-800 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition" />
                    </div>
                    <datalist id="quick-quote-lanes"></datalist>
                </div>

                <div class="lg:col-span-2">
                    <label for="quick-quote-mode" class="block text-xs font-semibold text-gray-600 mb-1">Mode</label>
                    <select id="quick-quote-mode" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        <option value="">Any Mode</option>
                        <option value="AIR">Air</option>
                        <option value="SEA">Sea</option>
                        <option value="LAND">Land</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="quick-quote-weight" class="block text-xs font-semibold text-gray-600 mb-1">Weight (KG)</label>
                    <input type="number" step="0.1" min="0" value="10" id="quick-quote-weight"
                           class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition" />
                </div>

                <div class="lg:col-span-2">
                    <label for="quick-quote-cbm" class="block text-xs font-semibold text-gray-600 mb-1">Volume (CBM)</label>
                    <input type="number" step="0.1" min="0" value="0" id="quick-quote-cbm"
                           class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition" />
                </div>

                <div class="lg:col-span-2">
                    <button type="submit" id="btn-quick-quote" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm shadow-indigo-600/20 active:scale-95">
                        <i class="fa-solid fa-calculator text-xs"></i> Estimate
                    </button>
                </div>
            </form>

            <!-- Estimator result -->
            <div id="quick-quote-result" class="hidden mt-3 rounded-xl border border-emerald-100 bg-emerald-50/60 p-3 sm:flex sm:items-center sm:justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="quick-quote-rate-id" class="text-[10px] font-bold uppercase tracking-wider bg-white text-gray-500 border border-gray-200 px-2 py-0.5 rounded-full">&mdash;</span>
                        <p id="quick-quote-route" class="text-sm font-bold text-gray-900 truncate">&mdash;</p>
                        <span id="quick-quote-transit" class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">&mdash;</span>
                    </div>
                    <p id="quick-quote-carrier" class="text-[11px] text-gray-500 mt-1 truncate">&mdash;</p>
                    <p id="quick-quote-breakdown" class="text-[10px] text-gray-400 mt-0.5">&mdash;</p>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 mt-2 sm:mt-0">
                    <div class="sm:text-right">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Est. All-in Total</p>
                        <p id="quick-quote-total" class="text-xl font-black text-emerald-600">&#8369;0.00</p>
                    </div>
                    <button type="button" id="btn-quick-quote-open" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-up-right-from-square text-[10px]"></i> Open
                    </button>
                </div>
            </div>

            <p id="quick-quote-empty" class="hidden mt-3 text-xs text-amber-600 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i>Walang tumugmang rate sa lane na iyon. Subukan ang ibang destination, mode o bigat.
            </p>
        </div>
    </section>

    <!-- ============================================================================
         ROW 2 — KPI STAT CARDS (same IDs consumed by rates.js)
         ============================================================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">

        <!-- Total rates -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Rates</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-sm"></i>
                </div>
            </div>
            <div class="flex flex-col items-start gap-1">
                <h3 id="stat-total-rates" class="text-3xl font-black text-gray-900">0</h3>
                <span class="text-[11px] font-semibold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">Full rate catalog</span>
            </div>
        </div>

        <!-- Avg transit -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Avg Transit</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-sm"></i>
                </div>
            </div>
            <div class="flex flex-col items-start gap-1">
                <h3 id="stat-avg-transit" class="text-3xl font-black text-gray-900">&mdash;</h3>
                <span class="text-[11px] font-semibold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">Matching lanes</span>
            </div>
        </div>

        <!-- Lowest rate -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Lowest Rate</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-peso-sign text-sm"></i>
                </div>
            </div>
            <div class="flex flex-col items-start gap-1">
                <h3 id="stat-lowest-rate" class="text-3xl font-black text-emerald-600">&mdash;</h3>
                <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Best base price</span>
            </div>
        </div>

        <!-- Expiring soon -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Expiring Soon</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-hourglass-half text-sm"></i>
                </div>
            </div>
            <div class="flex flex-col items-start gap-1">
                <h3 id="stat-expiring" class="text-3xl font-black text-gray-900">0</h3>
                <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Within 45 days</span>
            </div>
        </div>

        <!-- Favorites -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Favorites</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fa-solid fa-heart text-sm"></i>
                </div>
            </div>
            <div class="flex flex-col items-start gap-1">
                <h3 id="stat-favorites" class="text-3xl font-black text-gray-900">0</h3>
                <span class="text-[11px] font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">Saved lanes</span>
            </div>
        </div>
    </div>

    <!-- ============================================================================
         ROW 3 — MAIN WORKSPACE (filters + results)  |  INSIGHT RAIL (right)
         ============================================================================ -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

        <!-- ============================ LEFT: MAIN COLUMN ===================== -->
        <div class="xl:col-span-2 space-y-6 min-w-0">

            <!-- SEARCH & FILTER PANEL -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex flex-wrap justify-between items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-filter text-xs"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Search &amp; Filter Rate Cards</h2>
                            <p class="text-[11px] text-gray-400">I-filter ang catalog ayon sa lane, mode, coverage at serbisyo</p>
                        </div>
                    </div>
                    <button type="button" id="btn-clear-filters" class="text-xs font-semibold text-gray-500 hover:text-gray-900 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark mr-1"></i> Clear All
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
                    <!-- Route -->
                    <div class="lg:col-span-2">
                        <label for="filter-route" class="block text-xs font-semibold text-gray-600 mb-1">Lane / Route</label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot text-xs text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" id="filter-route" placeholder="Search origin / destination..."
                                   class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 pl-8 pr-3 py-2 text-gray-800 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition" />
                        </div>
                    </div>

                    <!-- Mode -->
                    <div>
                        <label for="filter-mode" class="block text-xs font-semibold text-gray-600 mb-1">Freight Mode</label>
                        <select id="filter-mode" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            <option value="">All Modes</option>
                            <option value="AIR">&#9992;&#65039; Air Freight</option>
                            <option value="SEA">&#128674; Sea Freight</option>
                            <option value="LAND">&#128666; Land Freight</option>
                        </select>
                    </div>

                    <!-- Coverage -->
                    <div>
                        <label for="filter-type" class="block text-xs font-semibold text-gray-600 mb-1">Coverage</label>
                        <select id="filter-type" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            <option value="">All Coverage</option>
                            <option value="Local">Local (Philippines)</option>
                            <option value="International">International</option>
                        </select>
                    </div>

                    <!-- Service option -->
                    <div>
                        <label for="filter-delivery-option" class="block text-xs font-semibold text-gray-600 mb-1">Service Option</label>
                        <select id="filter-delivery-option" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            <option value="">All Options</option>
                            <option value="Door-to-Door">Door-to-Door</option>
                            <option value="Port-to-Port">Port-to-Port</option>
                        </select>
                    </div>

                    <!-- Transit -->
                    <div>
                        <label for="filter-transit-time" class="block text-xs font-semibold text-gray-600 mb-1">Transit Speed</label>
                        <select id="filter-transit-time" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            <option value="">All Speed</option>
                            <option value="Same Day">&#9889; Same Day</option>
                            <option value="1-2 Days">&#128640; 1-2 Days</option>
                            <option value="Days">&#128198; Multi-day</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="flex items-end">
                        <button type="button" id="btn-search-rates" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition flex items-center justify-center gap-2 shadow-sm shadow-indigo-600/20 active:scale-95">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i> Search
                        </button>
                    </div>
                </div>

                <!-- Active filter chips (JS-rendered) -->
                <div id="active-filters-container" class="hidden flex flex-wrap gap-2 pt-3 border-t border-gray-100"></div>

                <p class="text-[11px] text-gray-400">
                    <i class="fa-regular fa-lightbulb mr-1"></i>Tip: pindutin ang <span class="font-bold text-gray-500">/</span> para tumalon agad sa lane search.
                </p>
            </div>

            <!-- RESULTS TOOLBAR -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-list-check text-xs"></i>
                    </span>
                    <div>
                        <span id="result-count" class="block text-sm font-bold text-gray-800">0 rates found</span>
                        <span class="text-[11px] text-gray-400">Adjust filters to narrow the catalog</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Density toggle -->
                    <div class="flex items-center bg-gray-50 p-1 rounded-xl border border-gray-100 gap-1">
                        <button type="button" id="btn-view-comfortable" title="Comfortable view" class="px-2.5 py-1.5 rounded-lg text-xs bg-white text-gray-900 shadow-sm transition">
                            <i class="fa-solid fa-table-cells-large"></i>
                        </button>
                        <button type="button" id="btn-view-compact" title="Compact view" class="px-2.5 py-1.5 rounded-lg text-xs text-gray-500 hover:text-gray-900 transition">
                            <i class="fa-solid fa-grip"></i>
                        </button>
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        <label for="sort-by" class="text-xs font-semibold text-gray-500 hidden sm:inline">Sort by:</label>
                        <select id="sort-by" class="text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            <option value="price-asc">Price: Low &#8594; High</option>
                            <option value="price-desc">Price: High &#8594; Low</option>
                            <option value="transit-asc">Transit: Fastest</option>
                            <option value="best-value">&#11088; Best Value (price + speed)</option>
                        </select>
                    </div>

                    <!-- Compare toggle -->
                    <button type="button" id="btn-comparison-panel" style="display:none;" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-all shadow-sm items-center gap-1.5">
                        <i class="fa-solid fa-scale-balanced text-[10px]"></i>
                        <span id="comparison-count">0</span> Compare
                    </button>
                </div>
            </div>

            <!-- RESULTS GRID (JS-rendered cards) -->
            <div id="rates-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Skeleton loaders (pinapalitan ng JS) -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-pulse">
                    <div class="h-1.5 w-16 bg-indigo-100 rounded-full mb-4"></div>
                    <div class="h-4 w-2/3 bg-gray-100 rounded mb-2"></div>
                    <div class="h-3 w-1/2 bg-gray-100 rounded mb-5"></div>
                    <div class="space-y-2 mb-5">
                        <div class="h-3 w-full bg-gray-50 rounded"></div>
                        <div class="h-3 w-5/6 bg-gray-50 rounded"></div>
                        <div class="h-3 w-2/3 bg-gray-50 rounded"></div>
                    </div>
                    <div class="h-12 bg-gray-50 rounded-xl mb-4"></div>
                    <div class="flex gap-2"><div class="h-9 flex-1 bg-indigo-50 rounded-lg"></div><div class="h-9 flex-1 bg-gray-50 rounded-lg"></div></div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-pulse hidden md:block">
                    <div class="h-1.5 w-16 bg-sky-100 rounded-full mb-4"></div>
                    <div class="h-4 w-2/3 bg-gray-100 rounded mb-2"></div>
                    <div class="h-3 w-1/2 bg-gray-100 rounded mb-5"></div>
                    <div class="space-y-2 mb-5">
                        <div class="h-3 w-full bg-gray-50 rounded"></div>
                        <div class="h-3 w-5/6 bg-gray-50 rounded"></div>
                        <div class="h-3 w-2/3 bg-gray-50 rounded"></div>
                    </div>
                    <div class="h-12 bg-gray-50 rounded-xl mb-4"></div>
                    <div class="flex gap-2"><div class="h-9 flex-1 bg-indigo-50 rounded-lg"></div><div class="h-9 flex-1 bg-gray-50 rounded-lg"></div></div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 animate-pulse hidden lg:block">
                    <div class="h-1.5 w-16 bg-amber-100 rounded-full mb-4"></div>
                    <div class="h-4 w-2/3 bg-gray-100 rounded mb-2"></div>
                    <div class="h-3 w-1/2 bg-gray-100 rounded mb-5"></div>
                    <div class="space-y-2 mb-5">
                        <div class="h-3 w-full bg-gray-50 rounded"></div>
                        <div class="h-3 w-5/6 bg-gray-50 rounded"></div>
                        <div class="h-3 w-2/3 bg-gray-50 rounded"></div>
                    </div>
                    <div class="h-12 bg-gray-50 rounded-xl mb-4"></div>
                    <div class="flex gap-2"><div class="h-9 flex-1 bg-indigo-50 rounded-lg"></div><div class="h-9 flex-1 bg-gray-50 rounded-lg"></div></div>
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div id="empty-state" class="hidden text-center py-14 bg-white rounded-2xl border border-dashed border-gray-200">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-box-open text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-600">No rates found</h3>
                <p class="text-sm text-gray-400 mt-1">Subukang baguhin ang mga filter para makahanap ng bagay na rate.</p>
                <button type="button" onclick="document.getElementById('btn-clear-filters')?.click()" class="mt-5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Clear Filters
                </button>
            </div>
        </div>
        <!-- ============================ /LEFT MAIN COLUMN ==================== -->

        <!-- ============================ RIGHT: INSIGHT RAIL ================== -->
        <div class="space-y-6 min-w-0">
            <div class="space-y-6 xl:sticky xl:top-6">

                <!-- Rates at a Glance (donut + mode quick filters) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-bold text-gray-900">Rates at a Glance</h4>
                        <span class="text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2.5 py-0.5 rounded-full border border-indigo-100">By Mode</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-2">Distribution ng rate cards kada freight mode</p>

                    <div id="modeDonutChart" class="w-full min-h-[220px]"></div>

                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Quick Filter</p>
                        <div id="mode-chips" class="flex flex-wrap gap-2">
                            <span class="text-xs text-gray-400">Loading modes...</span>
                        </div>
                    </div>
                </div>

                <!-- Validity Health (bagong widget) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-bold text-gray-900">Validity Health</h4>
                        <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full border border-blue-100">Rate Freshness</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-3">Gaano pa katagal bago mag-expire ang mga rate cards</p>

                    <div id="validity-health-bar" class="flex h-2.5 w-full rounded-full overflow-hidden bg-gray-100">
                        <div id="health-seg-d7" class="h-full bg-rose-500 transition-all duration-500" style="width:0%"></div>
                        <div id="health-seg-d21" class="h-full bg-amber-400 transition-all duration-500" style="width:0%"></div>
                        <div id="health-seg-d45" class="h-full bg-sky-400 transition-all duration-500" style="width:0%"></div>
                        <div id="health-seg-d45plus" class="h-full bg-emerald-400 transition-all duration-500" style="width:0%"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-3">
                        <button type="button" data-health-scroll="1" class="flex items-center justify-between gap-2 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:border-gray-200 px-2.5 py-2 transition text-left">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                <span class="inline-block w-2 h-2 rounded-full bg-rose-500 mr-1"></span>0-7d
                            </span>
                            <span id="health-count-d7" class="text-sm font-black text-gray-900">0</span>
                        </button>
                        <button type="button" data-health-scroll="1" class="flex items-center justify-between gap-2 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:border-gray-200 px-2.5 py-2 transition text-left">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                <span class="inline-block w-2 h-2 rounded-full bg-amber-400 mr-1"></span>8-21d
                            </span>
                            <span id="health-count-d21" class="text-sm font-black text-gray-900">0</span>
                        </button>
                        <button type="button" data-health-scroll="1" class="flex items-center justify-between gap-2 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:border-gray-200 px-2.5 py-2 transition text-left">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                <span class="inline-block w-2 h-2 rounded-full bg-sky-400 mr-1"></span>22-45d
                            </span>
                            <span id="health-count-d45" class="text-sm font-black text-gray-900">0</span>
                        </button>
                        <button type="button" data-health-scroll="1" class="flex items-center justify-between gap-2 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:border-gray-200 px-2.5 py-2 transition text-left">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 mr-1"></span>45d+
                            </span>
                            <span id="health-count-d45plus" class="text-sm font-black text-gray-900">0</span>
                        </button>
                    </div>

                    <p class="text-[10px] text-gray-400 mt-3">
                        <i class="fa-solid fa-circle-info mr-1"></i>Klikin ang bucket para tingnan ang watchlist sa ibaba.
                    </p>
                </div>

                <!-- Expiring Soon watchlist -->
                <div id="expiring-soon-card" class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm transition-shadow">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-bold text-gray-900">Expiring Soon</h4>
                        <span class="text-[10px] font-bold bg-amber-50 text-amber-600 px-2.5 py-0.5 rounded-full border border-amber-100">Next 45 days</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-3">Rate cards na malapit nang mag-expire</p>

                    <div id="expiring-soon-list" class="space-y-2.5">
                        <div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400 animate-pulse">Loading rate validity...</div>
                    </div>
                </div>

                <!-- Cheapest lanes -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-bold text-gray-900">Cheapest Lanes</h4>
                        <span class="text-[10px] font-bold bg-emerald-50 text-emerald-600 px-2.5 py-0.5 rounded-full border border-emerald-100">Top 3</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-3">Pinakamababang base rate sa catalog</p>

                    <div id="cheapest-lanes-list" class="space-y-2.5">
                        <div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400 animate-pulse">Scanning cheapest rates...</div>
                    </div>
                </div>

                <!-- Carrier Coverage leaderboard (bagong widget) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-bold text-gray-900">Carrier Coverage</h4>
                        <span class="text-[10px] font-bold bg-sky-50 text-sky-600 px-2.5 py-0.5 rounded-full border border-sky-100">Top 5</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-3">Mga carrier na may pinakamaraming lane at pinakamurang base rate</p>

                    <div id="carrier-leaderboard-list" class="space-y-2.5">
                        <div class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400 animate-pulse">Analyzing carriers...</div>
                    </div>
                </div>

                <!-- Recent quote activity (bagong widget) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <h4 class="text-sm font-bold text-gray-900">Recent Quote Activity</h4>
                        <button type="button" id="btn-clear-activity" class="text-[10px] font-bold text-gray-400 hover:text-rose-500 transition uppercase tracking-wider">
                            <i class="fa-regular fa-trash-can mr-1"></i>Clear
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-3">Mga na-compute mong quotation sa session na ito</p>

                    <div id="quote-activity-list" class="space-y-2.5">
                        <p id="activity-empty" class="p-4 rounded-xl bg-gray-50 text-center text-xs text-gray-400">
                            Wala pang na-compute na quote. Pindutin ang <span class="font-bold text-gray-500">Calculate</span> sa isang rate card.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================ /RIGHT INSIGHT RAIL ================== -->
    </div>
    <!-- ============================ /MAIN WORKSPACE ====================== -->

    <!-- ============================================================================
         COMPARISON DRAWER (fixed bottom sheet — JS toggles translate-y-full)
         ============================================================================ -->
    <div id="comparison-panel" class="fixed bottom-0 right-0 w-full lg:w-96 bg-white border-l border-t border-gray-100 rounded-tl-3xl shadow-2xl transform translate-y-full transition-transform duration-300 z-40" style="max-height: 70vh; overflow-y: auto;">
        <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-scale-balanced text-xs"></i>
                </span>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Compare Rates</h3>
                    <p class="text-[11px] text-gray-400">Hanggang 5 rate cards</p>
                </div>
            </div>
            <button type="button" id="btn-close-comparison" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div id="comparison-items" class="p-4 space-y-3"></div>
        <div class="p-4 border-t border-gray-100 space-y-2 bg-gray-50">
            <button type="button" id="btn-export-rates" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-csv text-xs"></i> Export to CSV
            </button>
            <button type="button" id="btn-clear-comparison" class="w-full py-2 bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 font-semibold text-xs rounded-xl transition-colors">
                Clear Comparison
            </button>
        </div>
    </div>

</div><!-- /dashboard container -->

<!-- ============================================================================
     COMPUTE / QUOTATION MODAL (rates.js toggles `hidden` ↔ `flex`)
     ============================================================================ -->
<div id="rate-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full my-auto shadow-xl border border-gray-100">

        <!-- Modal header -->
        <div class="p-5 border-b border-gray-100 flex justify-between items-start gap-3">
            <div class="flex items-start gap-3 min-w-0">
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calculator text-sm"></i>
                </span>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-gray-900 truncate" id="modal-route">Compute Final Quote</h3>
                    <p class="text-xs text-gray-500 truncate" id="modal-carrier-info">-</p>
                </div>
            </div>
            <button type="button" id="btn-close-modal" class="text-gray-400 hover:text-gray-600 font-bold text-xl leading-none shrink-0">&times;</button>
        </div>

        <div class="p-5 space-y-5">
            <!-- Rate summary pills -->
            <div class="flex flex-wrap gap-2">
                <span id="modal-mode" class="text-[11px] font-bold bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full">-</span>
                <span id="modal-service" class="text-[11px] font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">-</span>
                <span id="modal-transit" class="text-[11px] font-bold bg-sky-50 text-sky-600 px-2.5 py-1 rounded-full">-</span>
                <span id="modal-validity" class="text-[11px] font-bold bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full">-</span>
            </div>

            <form id="form-calculate-quote" class="space-y-4">
                <input type="hidden" id="modal-rate-id" />

                <div class="grid grid-cols-2 gap-3" id="cargo-input-group">
                    <div>
                        <label for="modal-input-weight" class="block text-xs font-semibold text-gray-600 mb-1">Weight (KG)</label>
                        <input type="number" step="0.1" id="modal-input-weight" value="1" min="0"
                               class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition" />
                    </div>
                    <div>
                        <label for="modal-input-cbm" class="block text-xs font-semibold text-gray-600 mb-1">Volume (CBM)</label>
                        <input type="number" step="0.1" id="modal-input-cbm" value="0" min="0"
                               class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-gray-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition" />
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm shadow-emerald-600/20 active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-equals text-xs"></i> Calculate Breakdown
                </button>
            </form>

            <!-- Calculation breakdown output -->
            <div id="quote-breakdown-result" class="hidden space-y-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Quotation Breakdown</h4>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Computed</span>
                </div>

                <div class="space-y-1.5 text-xs text-gray-600">
                    <div class="flex justify-between"><span>Base Freight:</span><span id="res-base" class="font-semibold text-gray-900">&#8369;0.00</span></div>
                    <div class="flex justify-between"><span>Pickup Trucking:</span><span id="res-pickup" class="font-semibold text-gray-900">&#8369;0.00</span></div>
                    <div class="flex justify-between"><span>Delivery Trucking:</span><span id="res-delivery" class="font-semibold text-gray-900">&#8369;0.00</span></div>
                    <div class="flex justify-between"><span>Documentation Fee:</span><span id="res-docs" class="font-semibold text-gray-900">&#8369;0.00</span></div>
                    <div class="flex justify-between"><span>Handling Fee:</span><span id="res-handling" class="font-semibold text-gray-900">&#8369;0.00</span></div>
                </div>

                <div class="border-t border-gray-200 pt-2.5 flex justify-between items-center text-sm font-bold text-gray-900">
                    <span>Total Quotation Rate:</span>
                    <span id="res-total" class="text-emerald-600 text-base font-black">&#8369;0.00</span>
                </div>

                <p id="quote-history-hint" class="text-[10px] text-gray-400">
                    <i class="fa-regular fa-floppy-disk mr-1"></i>Naka-save sa Recent Quote Activity.
                </p>
            </div>

            <div class="flex gap-2">
                <button type="button" id="btn-copy-quote" class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-regular fa-copy text-[11px]"></i> Copy Summary
                </button>
                <button type="button" id="btn-send-quote" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-paper-plane text-[11px]"></i> Send Quote
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================================
     SCRIPTS — rates.js (core engine) + rates_dashboard.js (dashboard widgets)
     ============================================================================ -->
<script src="../../../../assets/js/sales_agent/rates.js"></script>
<script src="../../../../assets/js/sales_agent/rates_dashboard.js"></script>












