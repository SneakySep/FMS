document.addEventListener("DOMContentLoaded", () => {
    // Unang pag-load ng data
    fetchRates();

    // Search button event listener
    const searchBtn = document.getElementById("btn-search-rates");
    if (searchBtn) {
        searchBtn.addEventListener("click", (e) => {
            e.preventDefault();
            fetchRates();
        });
    }

    // Modal close event listener
    const closeBtn = document.getElementById("btn-close-modal");
    if (closeBtn) {
        closeBtn.addEventListener("click", closeModal);
    }

    // Modal calculate form event listener
    const calcForm = document.getElementById("form-calculate-quote");
    if (calcForm) {
        calcForm.addEventListener("submit", (e) => {
            e.preventDefault();
            calculateQuote();
        });
    }

    // Function para kumuha ng listahan ng Rates
    async function fetchRates() {
        // Kunin ang dropdown elements
        const modeEl = document.getElementById("filter-mode") || document.getElementById("filter-freight-mode");
        const coverageEl = document.getElementById("filter-coverage") || document.getElementById("filter-type");
        const serviceEl = document.getElementById("filter-service-option") || document.getElementById("filter-delivery-option");
        const transitEl = document.getElementById("filter-transit-speed") || document.getElementById("filter-transit-time");

        const mode = modeEl ? modeEl.value.trim() : "";
        const coverage = coverageEl ? coverageEl.value.trim() : "";
        const service = serviceEl ? serviceEl.value.trim() : "";
        const transit = transitEl ? transitEl.value.trim() : "";

        const params = new URLSearchParams();

        // I-append lang sa URL parameters kung may totoong napili (hindi empty, hindi "All...")
        if (mode && !mode.toLowerCase().startsWith("all")) {
            params.append("mode", mode);
        }
        if (coverage && !coverage.toLowerCase().startsWith("all")) {
            params.append("type", coverage);
        }
        if (service && !service.toLowerCase().startsWith("all")) {
            params.append("delivery_option", service);
        }
        if (transit && !transit.toLowerCase().startsWith("all")) {
            params.append("transit_time", transit);
        }

        const container = document.getElementById("rates-cards-grid");
        if (container) {
            container.innerHTML = `<div class="col-span-full text-center py-8 text-slate-400">Nag-hahanap ng available rates...</div>`;
        }

        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/sales-agent/rates?${params.toString()}`);
            if (!response.ok) throw new Error("Mali ang responde mula sa server.");
            
            const result = await response.json();
            renderRateCards(result.data || []);
        } catch (error) {
            console.error("Fetch Rates Error:", error);
            if (container) {
                container.innerHTML = `<div class="col-span-full text-center py-8 text-rose-500 font-medium">Nagkaroon ng problema sa pagkuha ng rates. Siguraduhing gumagana ang server.</div>`;
            }
        }
    }

    // Function para i-render ang rate cards sa UI
    function renderRateCards(rates) {
        const container = document.getElementById("rates-cards-grid");
        if (!container) return;

        if (rates.length === 0) {
            container.innerHTML = `<div class="col-span-full text-center py-12 text-slate-400 font-medium">Walang nahanap na rate batay sa napiling filter.</div>`;
            return;
        }

        container.innerHTML = rates.map(r => {
            const modeBadgeClass = r.mode === 'AIR' ? 'bg-sky-50 text-sky-700 border-sky-200' :
                                  r.mode === 'SEA' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' :
                                  'bg-amber-50 text-amber-700 border-amber-200';

            return `
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg border ${modeBadgeClass}">
                                ${r.mode} • ${r.delivery_option}
                            </span>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                                ${r.transit_time}
                            </span>
                        </div>

                        <div>
                            <h3 class="font-bold text-slate-900 text-base leading-snug">${r.origin} ➔ ${r.destination}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">${r.carrier} (${r.service_type})</p>
                        </div>

                        <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl space-y-1">
                            <div class="flex justify-between">
                                <span>Base Freight:</span>
                                <span class="font-semibold text-slate-900">
                                    ₱${r.base_rate_per_kg ? `${r.base_rate_per_kg}/kg` : r.base_rate_per_cbm ? `${r.base_rate_per_cbm}/cbm` : Number(r.base_rate_flat).toLocaleString()}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Trucking Pick & Drop:</span>
                                <span class="font-semibold text-slate-900">₱${((r.trucking_pickup_fee || 0) + (r.trucking_delivery_fee || 0)).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>

                    <button onclick="openCalcModal('${r.id}', '${r.origin} ➔ ${r.destination}', '${r.carrier}')" 
                            class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-medium text-xs rounded-xl transition-colors">
                        Calculate Customer Quote
                    </button>
                </div>
            `;
        }).join("");
    }

    // Modal Display Functions
    window.openCalcModal = function(id, route, carrier) {
        document.getElementById("modal-rate-id").value = id;
        document.getElementById("modal-route").textContent = route;
        document.getElementById("modal-carrier-info").textContent = carrier;
        document.getElementById("quote-breakdown-result").classList.add("hidden");
        
        const modal = document.getElementById("rate-modal");
        if (modal) {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }
    };

    function closeModal() {
        const modal = document.getElementById("rate-modal");
        if (modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    }

    // Function para sa POST computation request
    async function calculateQuote() {
        const rateId = document.getElementById("modal-rate-id").value;
        const weight = parseFloat(document.getElementById("modal-input-weight").value) || 0;
        const cbm = parseFloat(document.getElementById("modal-input-cbm").value) || 0;

        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/sales-agent/rates/calculate`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    rate_id: rateId,
                    weight_kg: weight,
                    cbm: cbm
                })
            });

            if (!response.ok) throw new Error("Nabigo sa pag-compute ng quotation.");

            const data = await response.json();

            document.getElementById("res-base").textContent = `₱${data.breakdown.base_freight.toLocaleString()}`;
            document.getElementById("res-pickup").textContent = `₱${data.breakdown.trucking_pickup.toLocaleString()}`;
            document.getElementById("res-delivery").textContent = `₱${data.breakdown.trucking_delivery.toLocaleString()}`;
            document.getElementById("res-docs").textContent = `₱${data.breakdown.documentation_fee.toLocaleString()}`;
            document.getElementById("res-handling").textContent = `₱${data.breakdown.handling_fee.toLocaleString()}`;
            document.getElementById("res-total").textContent = `₱${data.total_quotation.toLocaleString()} ${data.currency}`;

            document.getElementById("quote-breakdown-result").classList.remove("hidden");
        } catch (err) {
            console.error("Quote Calculation Error:", err);
            alert("Nagkaroon ng problema sa pag-compute ng Quote.");
        }
    }
});