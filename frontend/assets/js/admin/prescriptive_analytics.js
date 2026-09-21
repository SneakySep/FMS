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

// 1. Render Alerts
function renderAlerts(alerts) {
    const container = document.getElementById("alerts-container");
    if (!container) return;

    if (alerts.length === 0) {
        container.classList.add("hidden");
        return;
    }

    container.classList.remove("hidden");
    container.innerHTML = alerts.map(a => `
        <div class="flex items-center justify-between p-3.5 rounded-xl ${a.severity === 'CRITICAL' ? 'bg-rose-50 border border-rose-200 text-rose-800' : 'bg-amber-50 border border-amber-200 text-amber-800'}">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full ${a.severity === 'CRITICAL' ? 'bg-rose-600' : 'bg-amber-600'} animate-ping"></span>
                <p class="text-sm font-medium">${a.alert_message}</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-md bg-white border border-current font-semibold">${a.severity}</span>
        </div>
    `).join("");
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