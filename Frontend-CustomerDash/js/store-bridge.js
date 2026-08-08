/* ==========================================================================
   SWIFTFREIGHT CUSTOMER PORTAL - SHARED STORE BRIDGE
   Reads the shared 'swift_dashboard_data' store maintained by the
   Sales Agent portal and live-renders the Customer portal pages so
   agent changes (shipments, invoices, documents, tickets, SLA) appear
   instantly on the customer side.
   ========================================================================== */

const CUSTOMER_STORE_KEY = 'swift_dashboard_data';

function getSharedStore() {
    const raw = localStorage.getItem(CUSTOMER_STORE_KEY);
    if (!raw) return null;
    try { return JSON.parse(raw); } catch (e) { return null; }
}

function bridgeStatusBadge(status) {
    const map = {
        'In Transit': 'bg-blue-100 text-blue-700',
        'Customs': 'bg-amber-100 text-amber-700',
        'Delivered': 'bg-emerald-100 text-emerald-700',
        'Delayed': 'bg-red-100 text-red-700'
    };
    return `<span class="${map[status] || 'bg-slate-100 text-slate-700'} font-semibold px-2.5 py-1 rounded-full text-[10px]">● ${status}</span>`;
}

function bridgeCardByTitle(title) {
    const h3s = [...document.querySelectorAll('h3')];
    const el = h3s.find(h => h.innerText.trim() === title);
    return el ? el.closest('.bg-white') : null;
}

function bridgeRenderDashboard(store) {
    let active = 0, inTransit = 0, delayed = 0, delivered = 0;
    store.shipments.forEach(s => {
        if (s.status === 'In Transit') { active++; inTransit++; }
        else if (s.status === 'Customs') active++;
        else if (s.status === 'Delayed') { active++; delayed++; }
        else if (s.status === 'Delivered') delivered++;
    });

    document.querySelectorAll('.grid > div.bg-white').forEach(card => {
        const label = card.querySelector('span.text-xs');
        const val = card.querySelector('strong');
        if (!label || !val) return;
        const t = label.innerText.trim();
        if (t === 'Active Shipments') val.innerText = active;
        else if (t === 'In Transit') val.innerText = inTransit;
        else if (t === 'Delayed') val.innerText = delayed;
        else if (t === 'Delivered (30d)') val.innerText = delivered;
    });

    // Shipment Manifest table
    const manifest = bridgeCardByTitle('Shipment Manifest');
    const mTbody = manifest && manifest.querySelector('tbody');
    if (mTbody) {
        mTbody.innerHTML = store.shipments.map(s => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4">
                    <strong class="font-mono text-slate-900 text-xs block">${s.id}</strong>
                    <span class="text-[10px] text-slate-400">${s.type}</span>
                </td>
                <td class="py-4 w-40">
                    <div class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full ${s.status === 'Delivered' ? 'bg-emerald-500' : s.status === 'Delayed' ? 'bg-red-500' : 'bg-brand-blue'}"></span>
                        <div class="flex-1 h-0.5 ${s.status === 'Delivered' ? 'bg-emerald-500' : s.status === 'Delayed' ? 'bg-red-400' : 'bg-brand-blue'} rounded"></div>
                        <span class="w-1.5 h-1.5 rounded-full ${s.status === 'Delivered' ? 'bg-emerald-500' : 'bg-slate-300'}"></span>
                    </div>
                </td>
                <td class="py-4">${bridgeStatusBadge(s.status)}</td>
                <td class="py-4 text-right font-mono font-medium ${s.status === 'Delayed' ? 'text-red-500' : 'text-slate-700'}">${s.eta}</td>
            </tr>`).join('');
    }

    // SLA Health bars (dashboard right card)
    const slaCard = document.getElementById('sla');
    if (slaCard) {
        const rows = slaCard.querySelectorAll('.space-y-3\.5 > div');
        const slaRows = [
            { key: 'On-time Pickup', val: store.sla.pickup },
            { key: 'Transit Time', val: store.sla.transit },
            { key: 'Customs Clearance', val: store.sla.customs },
            { key: 'Damage-free Delivery', val: store.sla.damageFree }
        ];
        const rowEls = [...slaCard.querySelectorAll('div')].filter(d =>
            d.querySelector('span.text-slate-700') && d.querySelector('.w-full.bg-slate-100')
        );
        rowEls.forEach(row => {
            const name = row.querySelector('span.text-slate-700').innerText.trim();
            const match = slaRows.find(r => r.key === name);
            if (!match) return;
            const pct = match.val;
            const color = pct >= 90 ? 'bg-emerald-500' : pct >= 80 ? 'bg-amber-500' : 'bg-red-500';
            row.querySelector('span.font-bold').innerText = pct + '%';
            const bar = row.querySelector('.w-full.bg-slate-100 > div');
            bar.className = `h-full ${color} rounded-full`;
            bar.style.width = pct + '%';
        });
    }

    // Recent Documents (dashboard right card)
    const docCard = document.getElementById('documents');
    const docList = docCard && docCard.querySelector('.space-y-2\.5');
    if (docList) {
        docList.innerHTML = store.documents.slice(0, 3).map(d => `
            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/50 transition-colors group cursor-pointer" onclick="alert('Downloading ${d.name} (${d.type})...')">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs"><i class="fa-solid fa-file-pdf"></i></div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue transition-colors">${d.name}</h4>
                        <span class="text-[10px] text-slate-400">${d.type} · Uploaded ${d.uploaded}</span>
                    </div>
                </div>
                <i class="fa-solid fa-arrow-down-long text-slate-400 text-xs"></i>
            </div>`).join('');
    }
}

function bridgeRenderShipments(store) {
    const tbody = document.querySelector('#shipmentsTable tbody');
    if (!tbody) return;
    tbody.innerHTML = store.shipments.map(s => `
        <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="${s.status.toLowerCase().replace(/\s+/g, '-')}">
            <td class="py-4">
                <strong class="font-mono text-slate-900 text-xs block">${s.id}</strong>
                <span class="text-[10px] text-slate-400">${s.type}</span>
            </td>
            <td class="py-4 font-semibold text-slate-800">${s.route}</td>
            <td class="py-4 text-slate-700 font-medium">${s.carrier}</td>
            <td class="py-4">${bridgeStatusBadge(s.status)}</td>
            <td class="py-4 text-right font-mono font-medium text-slate-700">${s.eta}</td>
        </tr>`).join('');
}

function bridgeRenderInvoices(store) {
    const tbody = document.querySelector('#invoicesTable tbody');
    if (!tbody) return;
    tbody.innerHTML = store.invoices.map(inv => {
        const cls = inv.status === 'Paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700';
        return `
            <tr class="invoice-row hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">${inv.id}</td>
                <td class="py-4 font-mono text-slate-600">${inv.waybill}</td>
                <td class="py-4 font-extrabold text-slate-900">${inv.amount}</td>
                <td class="py-4"><span class="${cls} font-semibold px-3 py-1 rounded-full text-[10px]">● ${inv.status}</span></td>
                <td class="py-4 text-right font-mono font-medium text-slate-600">${inv.due}</td>
            </tr>`;
    }).join('');
}

function bridgeRenderDocuments(store) {
    const container = document.getElementById('docListContainer');
    if (!container) return;
    const labels = { 'bill-lading': 'Bill of Lading', 'customs': 'Customs Declaration', 'proof-delivery': 'Proof of Delivery', 'invoice': 'Invoice', 'other': 'Other' };
    container.innerHTML = store.documents.map(d => `
        <div class="doc-item flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/50 transition-colors group cursor-pointer" data-category="${d.category}" onclick="alert('Downloading ${d.name} (${d.type})...')">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs"><i class="fa-solid fa-file-pdf"></i></div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue transition-colors">${d.name}</h4>
                    <span class="text-[10px] text-slate-400">${d.type} · Uploaded ${d.uploaded} · ${labels[d.category] || 'Document'}</span>
                </div>
            </div>
            <i class="fa-solid fa-arrow-down-long text-slate-400 text-xs"></i>
        </div>`).join('');
}

function bridgeRenderTickets(store) {
    const container = document.getElementById('ticketsContainer');
    if (!container) return;
    const clsMap = {
        'In Progress': 'bg-blue-100 text-blue-700',
        'Awaiting Reply': 'bg-amber-100 text-amber-700',
        'Resolved': 'bg-emerald-100 text-emerald-700'
    };
    container.innerHTML = store.tickets.map(t => `
        <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ${t.id} details...')">
            <div class="space-y-1">
                <span class="text-[10px] font-mono text-slate-400 font-bold block">${t.id}</span>
                <h4 class="text-xs font-extrabold text-slate-900 hover:text-brand-blue transition-colors">${t.title}</h4>
            </div>
            <span class="${clsMap[t.status] || 'bg-slate-100 text-slate-700'} font-semibold px-3 py-1 rounded-full text-[10px]">● ${t.status}</span>
        </div>`).join('');
}

function bridgeRenderSla(store) {
    document.querySelectorAll('.grid > div.bg-white').forEach(card => {
        const label = card.querySelector('span.text-xs');
        const val = card.querySelector('strong');
        if (!label || !val) return;
        const t = label.innerText.trim();
        if (t === 'Overall Compliance') val.innerText = store.sla.overall + '%';
        else if (t === 'Open Breaches') {
            val.innerText = store.sla.breaches.filter(b => b.status === 'Open').length;
        }
    });

    const tbody = document.querySelector('.lg\\:col-span-8 tbody');
    if (tbody) {
        tbody.innerHTML = store.sla.breaches.map(b => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">${b.waybill}</td>
                <td class="py-4 font-semibold text-slate-800">${b.commitment}</td>
                <td class="py-4 font-mono text-slate-600">${b.flagged}</td>
                <td class="py-4 text-right"><span class="${b.status === 'Open' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'} font-semibold px-3 py-1 rounded-full text-[10px]">● ${b.status}</span></td>
            </tr>`).join('');
    }
}

function bridgeRenderTracking(store) {
    const select = document.getElementById('waybillSelect');
    if (!select) return;
    select.innerHTML = store.shipments.map(s => `
        <option value="${s.id}">${s.id} (${s.route})</option>`).join('');
    const first = store.shipments.find(s => ['SF-WB-208841', 'SF-WB-208835', 'SF-WB-208790'].includes(s.id)) || store.shipments[0];
    if (first && window.switchTrackWaybill) {
        select.value = first.id;
        switchTrackWaybill(first.id);
    }
}

function bridgeRenderSettings(store) {
    const cust = store.customers[0];
    if (!cust) return;
    const map = [
        ['settingCompany', cust.company],
        ['settingEmail', cust.email],
        ['settingPhone', cust.phone],
        ['settingWarehouse', cust.warehouse],
        ['settingAddress', cust.address]
    ];
    map.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.value = val;
    });
}

function bridgeRenderAnalytics(store) {
    if (!store || !Array.isArray(store.shipments)) return;

    // "Top Routes" card (right column) — recompute from shared shipment data
    const card = bridgeCardByTitle('Top Routes');
    const list = card && card.querySelector('.divide-y');
    if (list) {
        const counts = {};
        store.shipments.forEach(s => {
            const route = s.route || 'Unassigned';
            counts[route] = (counts[route] || 0) + 1;
        });
        const topRoutes = Object.entries(counts).sort((a, b) => b[1] - a[1]).slice(0, 5);
        list.innerHTML = topRoutes.map(([route, count]) => `
            <div class="py-3.5 flex justify-between items-center">
                <strong class="text-slate-900 font-extrabold">${route}</strong>
                <span class="font-mono text-slate-500 font-bold">${count}</span>
            </div>`).join('');
    }
}

function bridgeInit() {
    const store = getSharedStore();
    if (!store) return;

    const page = window.location.pathname.split('/').pop();

    if (page === 'dashboard.php') bridgeRenderDashboard(store);
    else if (page === 'shipments.php') bridgeRenderShipments(store);
    else if (page === 'invoices.php') bridgeRenderInvoices(store);
    else if (page === 'documents.php') bridgeRenderDocuments(store);
    else if (page === 'tickets.php') bridgeRenderTickets(store);
    else if (page === 'sla-monitoring.php') bridgeRenderSla(store);
    else if (page === 'tracking.php') bridgeRenderTracking(store);
    else if (page === 'settings.php') bridgeRenderSettings(store);
    else if (page === 'analytics.php') bridgeRenderAnalytics(store);

    // Listen for live updates from the Sales Agent portal (same browser)
    window.addEventListener('storage', (e) => {
        if (e.key === CUSTOMER_STORE_KEY) {
            const fresh = getSharedStore();
            if (!fresh) return;
            if (page === 'dashboard.php') bridgeRenderDashboard(fresh);
            else if (page === 'shipments.php') bridgeRenderShipments(fresh);
            else if (page === 'invoices.php') bridgeRenderInvoices(fresh);
            else if (page === 'documents.php') bridgeRenderDocuments(fresh);
            else if (page === 'tickets.php') bridgeRenderTickets(fresh);
            else if (page === 'sla-monitoring.php') bridgeRenderSla(fresh);
            else if (page === 'analytics.php') bridgeRenderAnalytics(fresh);
        }
    });
}

document.addEventListener('DOMContentLoaded', bridgeInit);