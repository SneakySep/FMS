document.addEventListener("DOMContentLoaded", () => {
    fetchPrescriptiveAnalytics();

    const refreshBtn = document.getElementById("btn-refresh-prescriptive");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", () => {
            const icon = document.getElementById("refresh-icon");
            if (icon) icon.classList.add("animate-spin");
            fetchPrescriptiveAnalytics().finally(() => {
                if (icon) icon.classList.remove("animate-spin");
            });
        });
    }
});

async function fetchPrescriptiveAnalytics() {
    try {
        const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/api/v1/admin/analytics/bi/prescriptive`);
        
        if (!response.ok) {
            throw new Error(`HTTP Error! Status: ${response.status}`);
        }
        
        const payload = await response.json();

        // I-check kung naka-wrap sa payload.data o direktang object ang ibinalik ng API
        const data = payload.data || payload;

        renderAlerts(data.automated_followup_alerts || []);
        renderLeadsTable(data.lead_prioritization_engine || []);
        renderNextBestAction(data.next_best_actions || []);
        renderPricingSuggestions(data.dynamic_pricing_suggestions || []);
        renderGeminiInsights(data.suggestions_based_on_lost_won || {});
    } catch (error) {
        console.error("Prescriptive Analytics Error:", error);

        // UI Fallbacks kapag nag-error ang API call
        renderAlerts([{
            severity: 'CRITICAL',
            alert_message: `Hindi maikonekta sa Analytics Server: ${error.message}`
        }]);
        renderLeadsTable([]);
        renderNextBestAction([]);
        renderPricingSuggestions([]);
        renderGeminiInsights({
            summary: "Hindi makuha ang AI insights sa ngayon. Paki-check ang iyong backend server connection.",
            actionable_recommendations: []
        });
    }
}

// 1. Render Alerts — card rows matching site aesthetic
let prescriptiveAlertsCache = [];
let prescriptiveAlertFilter = 'ALL';

function escapeHtmlAlert(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function parseAlertMeta(alert) {
    const msg = alert.alert_message || '';
    const mCompany = msg.match(/'([^']+)'/);
    const mHours = msg.match(/([0-9,.]+)[ ]*oras/);
    return {
        company: alert.company_name || (mCompany ? mCompany[1] : 'Lead'),
        hours: alert.hours_unattended ?? (mHours ? mHours[1] : null)
    };
}

function styleAlertFilterButtons() {
    document.querySelectorAll('.alert-filter-btn').forEach((btn) => {
        const active = btn.dataset.alertFilter === prescriptiveAlertFilter;
        btn.className = 'alert-filter-btn px-3 py-1 rounded-lg transition-all ' + (active
            ? 'bg-white text-[#1A1A1A] font-semibold shadow-sm'
            : 'text-gray-500 hover:text-slate-800');
    });
}

function buildAlertRow(a) {
    const isCritical = a.severity === 'CRITICAL';
    const meta = parseAlertMeta(a);
    const safeMsg = escapeHtmlAlert(a.alert_message);
    const safeCompany = escapeHtmlAlert(meta.company);
    const safeSev = escapeHtmlAlert(a.severity);
    const accent = isCritical ? 'border-l-rose-500 hover:border-rose-200' : 'border-l-amber-400 hover:border-amber-200';
    const iconWrap = isCritical ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-amber-50 text-amber-600 border-amber-100';
    const pill = isCritical ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200';
    const iconSvg = isCritical
        ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>'
        : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    const ping = isCritical ? '<span class="absolute -top-1 -right-1 flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span></span>' : '';
    const subHours = meta.hours !== null ? '<span aria-hidden="true">•</span><span class="inline-flex items-center gap-1"><i class="fa-regular fa-clock text-[10px]"></i>' + escapeHtmlAlert(meta.hours) + ' hrs pending</span>' : '';
    return '' +
    '<article class="alert-row flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-xl bg-white border border-slate-200 border-l-4 ' + accent + ' hover:shadow-sm transition-all duration-200">' +
        '<div class="flex items-start gap-3 flex-1 min-w-0">' +
            '<span class="relative flex w-9 h-9 shrink-0 items-center justify-center rounded-xl border ' + iconWrap + '">' + iconSvg + ping + '</span>' +
            '<div class="min-w-0 flex-1">' +
                '<p class="text-sm font-medium text-slate-800 leading-snug">' + safeMsg + '</p>' +
                '<p class="text-xs text-gray-400 mt-1 flex items-center gap-1.5 flex-wrap">' +
                    '<span class="inline-flex items-center gap-1"><i class="fa-regular fa-building text-[10px]"></i>' + safeCompany + '</span>' +
                    subHours +
                    '<span aria-hidden="true">•</span><span>High-probability</span>' +
                '</p>' +
            '</div>' +
        '</div>' +
        '<div class="flex items-center gap-2 shrink-0 sm:pl-2">' +
            '<span class="text-[11px] font-bold px-2.5 py-1 rounded-full border ' + pill + '">' + safeSev + '</span>' +
            '<button type="button" data-alert-action="view" data-company="' + safeCompany + '" title="Highlight lead in table" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white transition active:scale-95"><i class="fa-solid fa-arrow-right text-[10px]"></i><span class="hidden md:inline">View Lead</span></button>' +
            '<button type="button" data-alert-action="dismiss" title="Dismiss alert" aria-label="Dismiss alert for ' + safeCompany + '" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition flex items-center justify-center"><i class="fa-solid fa-xmark text-xs"></i></button>' +
        '</div>' +
    '</article>';
}
function renderAlerts(alerts) {
    prescriptiveAlertsCache = Array.isArray(alerts) ? alerts : [];
    const section = document.getElementById("alerts-section");
    const container = document.getElementById("alerts-container");
    const badge = document.getElementById("alerts-count-badge");
    if (!section || !container) return;
    bindAlertControlsOnce();
    styleAlertFilterButtons();
    if (prescriptiveAlertsCache.length === 0) {
        section.classList.add("hidden");
        container.innerHTML = "";
        return;
    }
    section.classList.remove("hidden");
    const critN = prescriptiveAlertsCache.filter((x) => x.severity === "CRITICAL").length;
    if (badge) {
        badge.textContent = critN > 0 ? critN + " critical" : (prescriptiveAlertsCache.length - critN) + " warning";
        badge.className = "text-[11px] font-bold px-2 py-0.5 rounded-full border " + (critN > 0 ? "bg-rose-50 text-rose-700 border-rose-200" : "bg-amber-50 text-amber-700 border-amber-200");
    }
    const visible = prescriptiveAlertsCache.filter((x) => prescriptiveAlertFilter === "ALL" || x.severity === prescriptiveAlertFilter);
    if (visible.length === 0) {
        container.innerHTML = '<div class="text-center py-6 text-sm text-slate-400">No ' + escapeHtmlAlert(prescriptiveAlertFilter.toLowerCase()) + ' alerts right now.</div>';
        return;
    }
    container.innerHTML = visible.map(buildAlertRow).join("");
}

function bindAlertControlsOnce() {
    const container = document.getElementById("alerts-container");
    if (container && !container.dataset.bound) {
        container.dataset.bound = "1";
        container.addEventListener("click", (e) => {
            const btn = e.target.closest("[data-alert-action]");
            if (!btn) return;
            const row = btn.closest(".alert-row");
            if (btn.dataset.alertAction === "dismiss" && row) {
                row.style.transition = "opacity .25s ease, transform .25s ease";
                row.style.opacity = "0";
                row.style.transform = "translateX(12px)";
                setTimeout(() => {
                    row.remove();
                    if (container.querySelectorAll(".alert-row").length === 0) {
                        container.innerHTML = '<div class="flex items-center justify-center gap-2 py-6 text-sm text-slate-400"><i class="fa-solid fa-circle-check text-emerald-500"></i>All caught up — no follow-ups pending.</div>';
                        const b = document.getElementById("alerts-count-badge");
                        if (b) { b.textContent = "all clear"; b.className = "text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200"; }
                    }
                }, 240);
            }
            if (btn.dataset.alertAction === "view") {
                const tbody = document.getElementById("leads-table-body");
                const company = (btn.dataset.company || "").toLowerCase();
                if (tbody) {
                    tbody.scrollIntoView({ behavior: "smooth", block: "center" });
                    const match = Array.from(tbody.querySelectorAll("tr")).find((r) => r.textContent.toLowerCase().includes(company));
                    if (match) {
                        match.style.backgroundColor = "#fef2f2";
                        match.style.outline = "1px solid #fecaca";
                        setTimeout(() => { match.style.backgroundColor = ""; match.style.outline = ""; }, 2200);
                    }
                }
            }
        });
    }
    document.querySelectorAll(".alert-filter-btn").forEach((btn) => {
        if (btn.dataset.bound) return;
        btn.dataset.bound = "1";
        btn.addEventListener("click", () => {
            prescriptiveAlertFilter = btn.dataset.alertFilter || "ALL";
            renderAlerts(prescriptiveAlertsCache);
        });
    });
    const toggleBtn = document.getElementById("btn-toggle-alerts");
    if (toggleBtn && !toggleBtn.dataset.bound) {
        toggleBtn.dataset.bound = "1";
        toggleBtn.addEventListener("click", () => {
            const list = document.getElementById("alerts-container");
            const chev = document.getElementById("alerts-chevron");
            if (!list) return;
            const collapsed = list.classList.toggle("hidden");
            toggleBtn.setAttribute("aria-expanded", String(!collapsed));
            if (chev) chev.style.transform = collapsed ? "rotate(180deg)" : "rotate(0deg)";
        });
    }
}

// 2. Render Lead Prioritization
function renderLeadsTable(leads) {
    const tbody = document.getElementById("leads-table-body");
    if (!tbody) return;

    if (leads.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-4 text-center text-slate-400">No active leads found</td></tr>`;
        return;
    }

    tbody.innerHTML = leads.map(l => `
        <tr class="hover:bg-slate-50 transition-colors">
            <td class="py-3 px-3 font-medium text-slate-900">${l.company_name} <span class="block text-xs text-slate-400">${l.service_type} • ${l.platform}</span></td>
            <td class="py-3 px-3 font-mono text-slate-700">₱${Number(l.estimated_amount).toLocaleString()}</td>
            <td class="py-3 px-3">
                <div class="w-full bg-slate-200 rounded-full h-1.5 w-16 inline-block mr-2">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: ${l.conversion_prob}%"></div>
                </div>
                <span class="text-xs font-mono text-slate-600">${l.conversion_prob}%</span>
            </td>
            <td class="py-3 px-3 text-center">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold ${l.priority_score >= 75 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200'}">
                    ${l.priority_score}
                </span>
            </td>
            <td class="py-3 px-3 text-right">
                <span class="text-xs font-semibold ${l.urgency_level === 'High' ? 'text-rose-600' : l.urgency_level === 'Medium' ? 'text-amber-600' : 'text-slate-500'}">
                    ${l.urgency_level}
                </span>
            </td>
        </tr>
    `).join("");
}

// 3. Render Next Best Action
function renderNextBestAction(actions) {
    const container = document.getElementById("next-action-container");
    if (!container) return;

    const topLead = actions[0];
    if (!topLead) {
        container.innerHTML = `<p class="text-sm text-slate-400">No immediate actions required.</p>`;
        return;
    }

    container.innerHTML = `
        <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100">
            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Top Priority Lead</span>
            <h4 class="text-lg font-bold text-slate-900 mt-0.5">${topLead.company_name}</h4>
            <p class="text-xs text-slate-500 mt-1">${topLead.service_type} • Pending ${topLead.hours_pending} hrs</p>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Recommended Strategy</span>
            <p class="text-sm font-semibold text-emerald-700 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                ${topLead.recommended_action}
            </p>
            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200">
                ${topLead.action_details}
            </p>
        </div>
    `;
}

// 4. Render Dynamic Pricing Suggestions
function renderPricingSuggestions(pricing) {
    const container = document.getElementById("pricing-suggestions-list");
    if (!container) return;

    if (pricing.length === 0) {
        container.innerHTML = `<p class="text-sm text-slate-400">No pricing adjustments suggested.</p>`;
        return;
    }

    container.innerHTML = pricing.map(p => `
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start justify-between gap-3">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-900">${p.route}</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200 font-medium">${p.service_type}</span>
                </div>
                <p class="text-xs text-slate-500">${p.reasoning}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg ${p.suggested_adjustment.includes('+') ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                    ${p.suggested_adjustment}
                </span>
                <span class="block text-[10px] text-slate-400 mt-1">${p.strategy_type}</span>
            </div>
        </div>
    `).join("");
}

// 5. Render Gemini Insights
function renderGeminiInsights(insights) {
    const summaryBox = document.getElementById("won-lost-summary-box");
    const recsList = document.getElementById("won-lost-recommendations-list");

    if (summaryBox) {
        summaryBox.textContent = insights.summary || "No AI synthesis available at the moment.";
    }

    if (recsList) {
        const recs = insights.actionable_recommendations || [];
        if (recs.length === 0) {
            recsList.innerHTML = `<li class="text-xs text-slate-400">No recommendations available.</li>`;
            return;
        }

        recsList.innerHTML = recs.map(r => `
            <li class="flex items-start gap-2">
                <span class="text-indigo-600 font-bold">•</span>
                <span>${r}</span>
            </li>
        `).join("");
    }
}