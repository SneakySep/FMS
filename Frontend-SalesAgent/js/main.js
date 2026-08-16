/* ==========================================================================
   PRIORITY HANDLING LOGISTICS SALES AGENT PORTAL - JAVASCRIPT LOGIC
   Manages the shared data store that drives the Customer Dashboard portal.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. SHARED DATA STORE (localStorage)
   This store is read by both the Sales Agent portal (write) and the
   Customer dashboard (read). Key: priority_dashboard_data
   -------------------------------------------------------------------------- */

const STORE_KEY = 'priority_dashboard_data';

function getDefaultStore() {
    return {
        customers: [
            { id: 'cust-001', company: 'Charlie Hub.Inc', contact: 'J. Sison', account: '8841', email: 'client@company.ph', port: 'CLIENT', portalAccess: true, warehouse: 'Caloocan Hub', address: '12 Rizal Ave, Caloocan City, Metro Manila', phone: '+63 917 000 1234' }
        ],
        shipments: [
            { id: 'PH-WB-208841', type: '40ft container · Reefer', route: 'Manila → Cebu', carrier: 'Trans-Pacific Lines', status: 'In Transit', eta: 'Jul 29, 14:00' },
            { id: 'PH-WB-208835', type: '20ft container · Dry van', route: 'Cebu → Manila', carrier: '2GO Freight', status: 'Customs', eta: 'Jul 30, 09:00' },
            { id: 'PH-WB-208790', type: 'LCL · Palletized', route: 'Davao → Manila', carrier: 'Trans-Pacific Lines', status: 'Delivered', eta: 'Jul 25, 11:20' },
            { id: 'PH-WB-208712', type: '40ft container · Dry van', route: 'Manila → Iloilo', carrier: 'Sulpicio Lines', status: 'Delayed', eta: 'Jul 27, 18:00' },
            { id: 'PH-WB-208699', type: '20ft container · Reefer', route: 'Manila → Cagayan de Oro', carrier: '2GO Freight', status: 'In Transit', eta: 'Aug 2, 07:30' },
            { id: 'PH-WB-208650', type: 'FCL · Dry van', route: 'Manila → Bacolod', carrier: 'Trans-Pacific Lines', status: 'In Transit', eta: 'Jul 31, 10:00' }
        ],
        invoices: [
            { id: 'INV-2026-0841', waybill: 'PH-WB-208841', amount: '₱24,000', status: 'Pending', due: 'Aug 5, 2026' },
            { id: 'INV-2026-0835', waybill: 'PH-WB-208835', amount: '₱24,200', status: 'Pending', due: 'Aug 6, 2026' },
            { id: 'INV-2026-0790', waybill: 'PH-WB-208790', amount: '₱18,750', status: 'Paid', due: 'Jul 20, 2026' },
            { id: 'INV-2026-0712', waybill: 'PH-WB-208712', amount: '₱31,000', status: 'Paid', due: 'Jul 15, 2026' }
        ],
        documents: [
            { id: 'doc-001', name: 'Bill of Lading — WB-208841', category: 'bill-lading', type: 'PDF', uploaded: 'Jul 26' },
            { id: 'doc-002', name: 'Customs Declaration — WB-208835', category: 'customs', type: 'PDF', uploaded: 'Jul 25' },
            { id: 'doc-003', name: 'Proof of Delivery — WB-208790', category: 'proof-delivery', type: 'PDF', uploaded: 'Jul 25' }
        ],
        tickets: [
            { id: 'TCK-1042', title: 'Delay on PH-WB-208712 — need updated ETA', status: 'In Progress' },
            { id: 'TCK-1039', title: 'Request for duplicate Bill of Lading', status: 'Awaiting Reply' },
            { id: 'TCK-1021', title: 'Billing discrepancy on INV-2026-0790', status: 'Resolved' }
        ],
        sla: {
            overall: 94,
            pickup: 97,
            transit: 92,
            customs: 78,
            damageFree: 99,
            breaches: [
                { waybill: 'PH-WB-208712', commitment: 'Transit time', flagged: 'Jul 27, 06:00', status: 'Open' },
                { waybill: 'PH-WB-208601', commitment: 'Customs clearance', flagged: 'Jul 20, 15:30', status: 'Resolved' },
                { waybill: 'PH-WB-208588', commitment: 'On-time pickup', flagged: 'Jul 18, 08:00', status: 'Resolved' }
            ]
        },
        updatedAt: new Date().toISOString()
    };
}

function getStore() {
    const raw = localStorage.getItem(STORE_KEY);
    if (!raw) {
        const def = getDefaultStore();
        localStorage.setItem(STORE_KEY, JSON.stringify(def));
        return def;
    }
    try {
        return JSON.parse(raw);
    } catch (e) {
        const def = getDefaultStore();
        localStorage.setItem(STORE_KEY, JSON.stringify(def));
        return def;
    }
}

function saveStore(store) {
    store.updatedAt = new Date().toISOString();
    localStorage.setItem(STORE_KEY, JSON.stringify(store));
}

function notifyCustomer(message) {
    // Simulates pushing a notification to the customer portal hub
    console.log(`[Notification Hub] Customer notified: ${message}`);
    playNotificationSound();
}

/* --------------------------------------------------------------------------
   2. AGENT AUTHENTICATION & SETTINGS
   -------------------------------------------------------------------------- */

const DEMO_OTP = '123456';
let generatedOtp = DEMO_OTP;

function handleAgentLogin(e) {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
    const loginBtn = document.getElementById('loginBtn');
    const status = document.getElementById('loginStatus');

    loginBtn.disabled = true;
    loginBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...`;

    setTimeout(() => {
        if (email === 'agent@priority-ph.com' && password === 'demo1234') {
            loginBtn.disabled = false;
            loginBtn.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i> Sign In to Agent Portal`;
            status.classList.add('hidden');

            generatedOtp = String(Math.floor(100000 + Math.random() * 900000));
            console.log(`[Priority Handling Demo] Agent OTP sent to ${email}: ${generatedOtp}`);

            document.getElementById('credentialsForm').classList.add('hidden');
            document.getElementById('otpForm').classList.remove('hidden');
            document.getElementById('otpEmailDisplay').innerText = email;
            document.getElementById('otpInput').value = '';
            document.getElementById('otpStatus').classList.add('hidden');

            setTimeout(() => document.getElementById('otpInput').focus(), 100);
        } else {
            loginBtn.disabled = false;
            loginBtn.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i> Sign In to Agent Portal`;
            status.classList.remove('hidden');
            status.innerText = 'Invalid credentials. Use agent@priority-ph.com / demo1234';
        }
    }, 1000);
}

function handleAgentOtpVerify(e) {
    e.preventDefault();

    const otpInput = document.getElementById('otpInput').value.trim();
    const otpBtn = document.getElementById('otpBtn');
    const otpStatus = document.getElementById('otpStatus');
    const email = document.getElementById('loginEmail').value.trim();

    otpBtn.disabled = true;
    otpBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Verifying...`;

    setTimeout(() => {
        if (otpInput === generatedOtp) {
            localStorage.setItem('priority_agent_session', JSON.stringify({
                user: 'Maria Santos',
                role: 'Senior Sales Agent',
                email: email,
                token: 'agent_session_token_ph_77321',
                otpVerified: true,
                verifiedAt: new Date().toISOString()
            }));
            window.location.href = 'dashboard.php';
        } else {
            otpBtn.disabled = false;
            otpBtn.innerHTML = `<i class="fa-solid fa-check"></i> Verify & Sign In`;
            otpStatus.classList.remove('hidden');
            otpStatus.innerText = 'Invalid OTP code. Please try again.';
            document.getElementById('otpInput').value = '';
            document.getElementById('otpInput').focus();
        }
    }, 800);
}

function backToCredentials() {
    document.getElementById('otpForm').classList.add('hidden');
    document.getElementById('credentialsForm').classList.remove('hidden');
    document.getElementById('otpStatus').classList.add('hidden');
    document.getElementById('loginStatus').classList.add('hidden');
}

function resendOtp() {
    generatedOtp = String(Math.floor(100000 + Math.random() * 900000));
    const email = document.getElementById('loginEmail').value.trim();
    console.log(`[Priority Handling Demo] New OTP sent to ${email}: ${generatedOtp}`);

    const otpStatus = document.getElementById('otpStatus');
    otpStatus.classList.remove('hidden');
    otpStatus.className = 'p-3 bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 rounded-xl text-center text-xs';
    otpStatus.innerText = `A new verification code has been sent to ${email}`;

    document.getElementById('otpInput').value = '';
    document.getElementById('otpInput').focus();

    setTimeout(() => {
        otpStatus.classList.add('hidden');
        otpStatus.className = 'hidden p-3 bg-red-500/20 border border-red-500/30 text-red-300 rounded-xl text-center text-xs';
    }, 4000);
}

function handleLogout() {
    localStorage.removeItem('priority_agent_session');
    window.location.href = 'index.php';
}

function checkAgentSession() {
    const session = localStorage.getItem('priority_agent_session');
    const protectedPages = ['dashboard.php', 'customers.php', 'settings.php', 'leads.php', 'pipelines.php', 'ai-escalations.php', 'quotes.php', 'contracts.php', 'meetings.php', 'chat.php'];
    const current = window.location.pathname.split('/').pop();

    if (protectedPages.includes(current) && !session) {
        window.location.href = 'index.php';
    }
}

/* --------------------------------------------------------------------------
   3. SHIPMENT CONTROL (CRUD)
   -------------------------------------------------------------------------- */

function renderShipmentsTable() {
    const tbody = document.getElementById('shipmentsTableBody');
    if (!tbody) return;

    const store = getStore();
    tbody.innerHTML = '';

    store.shipments.forEach(s => {
        const statusClass = {
            'In Transit': 'bg-blue-100 text-blue-700',
            'Customs': 'bg-amber-100 text-amber-700',
            'Delivered': 'bg-emerald-100 text-emerald-700',
            'Delayed': 'bg-red-100 text-red-700'
        }[s.status] || 'bg-slate-100 text-slate-700';

        const dataStatus = s.status.toLowerCase().replace(/\s+/g, '-');

        tbody.innerHTML += `
            <tr class="shipment-row hover:bg-slate-50 transition-colors" data-status="${dataStatus}">
                <td class="py-4">
                    <strong class="font-mono text-slate-900 text-xs block">${s.id}</strong>
                    <span class="text-[10px] text-slate-400">${s.type}</span>
                </td>
                <td class="py-4 font-semibold text-slate-800">${s.route}</td>
                <td class="py-4 text-slate-700 font-medium">${s.carrier}</td>
                <td class="py-4">
                    <span class="${statusClass} font-semibold px-2.5 py-1 rounded-full text-[10px]">● ${s.status}</span>
                </td>
                <td class="py-4 text-right font-mono font-medium text-slate-700">${s.eta}</td>
                <td class="py-4 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-2">
                        <select onchange="updateShipmentStatus('${s.id}', this.value)" class="bg-slate-50 border border-slate-200 text-slate-700 text-[10px] font-semibold px-2 py-1.5 rounded-lg focus:outline-none focus:border-brand-blue cursor-pointer">
                            <option value="In Transit" ${s.status === 'In Transit' ? 'selected' : ''}>In Transit</option>
                            <option value="Customs" ${s.status === 'Customs' ? 'selected' : ''}>Customs</option>
                            <option value="Delivered" ${s.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
                            <option value="Delayed" ${s.status === 'Delayed' ? 'selected' : ''}>Delayed</option>
                        </select>
                        <button onclick="openEditShipmentModal('${s.id}')" title="Edit" class="text-slate-400 hover:text-brand-blue p-1 transition-colors">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                        </button>
                        <button onclick="deleteShipment('${s.id}')" title="Delete" class="text-slate-400 hover:text-red-500 p-1 transition-colors">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

function updateShipmentStatus(wbId, newStatus) {
    const store = getStore();
    const shipment = store.shipments.find(s => s.id === wbId);
    if (!shipment) return;

    shipment.status = newStatus;
    saveStore(store);
    renderShipmentsTable();
    updateDashboardKPIs();
    notifyCustomer(`${wbId} status changed to ${newStatus}`);
    alert(`Shipment ${wbId} updated to: ${newStatus}`);
}

function createNewShipment(e) {
    e.preventDefault();
    const store = getStore();

    const waybill = document.getElementById('newWaybill').value.trim();
    const type = document.getElementById('newShipmentType').value.trim();
    const route = document.getElementById('newRoute').value.trim();
    const carrier = document.getElementById('newCarrier').value.trim();
    const status = document.getElementById('newStatus').value;
    const eta = document.getElementById('newEta').value.trim();

    if (store.shipments.some(s => s.id === waybill)) {
        alert('That waybill number already exists.');
        return false;
    }

    store.shipments.unshift({ id: waybill, type, route, carrier, status, eta });
    saveStore(store);
    renderShipmentsTable();
    updateDashboardKPIs();
    closeModal('shipmentModal');
    notifyCustomer(`New shipment ${waybill} created`);
    alert(`Shipment ${waybill} created and pushed to customer portal.`);
    return false;
}

function openEditShipmentModal(wbId) {
    const store = getStore();
    const s = store.shipments.find(x => x.id === wbId);
    if (!s) return;

    document.getElementById('editWaybill').value = s.id;
    document.getElementById('editShipmentType').value = s.type;
    document.getElementById('editRoute').value = s.route;
    document.getElementById('editCarrier').value = s.carrier;
    document.getElementById('editStatus').value = s.status;
    document.getElementById('editEta').value = s.eta;
    openModal('editShipmentModal');
}

function saveEditedShipment(e) {
    e.preventDefault();
    const store = getStore();
    const waybill = document.getElementById('editWaybill').value.trim();
    const s = store.shipments.find(x => x.id === waybill);
    if (!s) return false;

    s.type = document.getElementById('editShipmentType').value.trim();
    s.route = document.getElementById('editRoute').value.trim();
    s.carrier = document.getElementById('editCarrier').value.trim();
    s.status = document.getElementById('editStatus').value;
    s.eta = document.getElementById('editEta').value.trim();

    saveStore(store);
    renderShipmentsTable();
    updateDashboardKPIs();
    closeModal('editShipmentModal');
    notifyCustomer(`${waybill} details updated`);
    alert(`Shipment ${waybill} updated.`);
    return false;
}

function deleteShipment(wbId) {
    if (!confirm(`Delete shipment ${wbId}? This will also remove it from the customer portal.`)) return;

    const store = getStore();
    store.shipments = store.shipments.filter(s => s.id !== wbId);
    saveStore(store);
    renderShipmentsTable();
    updateDashboardKPIs();
    notifyCustomer(`Shipment ${wbId} removed`);
    alert(`Shipment ${wbId} deleted.`);
}

function filterShipments(status, btn) {
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(t => {
        t.classList.remove('bg-brand-blue', 'text-white', 'shadow-sm');
        t.classList.add('bg-white', 'text-slate-600');
    });

    btn.classList.remove('bg-white', 'text-slate-600');
    btn.classList.add('bg-brand-blue', 'text-white', 'shadow-sm');

    const rows = document.querySelectorAll('.shipment-row');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

function searchShipmentsTable() {
    const input = document.getElementById('shipmentSearchInput');
    if (!input) return;
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.shipment-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (text.includes(filter)) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

function exportShipmentsCSV() {
    const store = getStore();
    const rows = store.shipments.map(s => [s.id, s.route, s.carrier, s.status, s.eta].join(',')).join('\n');
    console.log('CSV export preview:\n' + rows);
    alert('Exporting shipments data to PriorityHandling_Waybills.csv...');
}

/* --------------------------------------------------------------------------
   4. INVOICE CONTROL (CRUD)
   -------------------------------------------------------------------------- */

function renderInvoicesTable() {
    const tbody = document.getElementById('invoicesTableBody');
    if (!tbody) return;

    const store = getStore();
    tbody.innerHTML = '';

    store.invoices.forEach(inv => {
        const statusClass = inv.status === 'Paid'
            ? 'bg-emerald-100 text-emerald-700'
            : 'bg-amber-100 text-amber-700';

        tbody.innerHTML += `
            <tr class="invoice-row hover:bg-slate-50 transition-colors" onclick="alert('Opening ${inv.id} details...')">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">${inv.id}</td>
                <td class="py-4 font-mono text-slate-600">${inv.waybill}</td>
                <td class="py-4 font-extrabold text-slate-900">${inv.amount}</td>
                <td class="py-4">
                    <span class="${statusClass} font-semibold px-3 py-1 rounded-full text-[10px]">● ${inv.status}</span>
                </td>
                <td class="py-4 text-right font-mono font-medium text-slate-600">${inv.due}</td>
                <td class="py-4 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-2">
                        ${inv.status !== 'Paid' ? `<button onclick="markInvoicePaid('${inv.id}')" title="Mark Paid" class="text-emerald-500 hover:text-emerald-600 p-1 transition-colors"><i class="fa-solid fa-check text-[10px]"></i></button>` : ''}
                        <button onclick="deleteInvoice('${inv.id}')" title="Delete" class="text-slate-400 hover:text-red-500 p-1 transition-colors">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

function markInvoicePaid(invId) {
    const store = getStore();
    const inv = store.invoices.find(i => i.id === invId);
    if (!inv) return;

    inv.status = 'Paid';
    saveStore(store);
    renderInvoicesTable();
    updateDashboardKPIs();
    notifyCustomer(`${invId} marked as paid`);
    alert(`Invoice ${invId} marked as Paid.`);
}

function deleteInvoice(invId) {
    if (!confirm(`Delete invoice ${invId}?`)) return;

    const store = getStore();
    store.invoices = store.invoices.filter(i => i.id !== invId);
    saveStore(store);
    renderInvoicesTable();
    updateDashboardKPIs();
    alert(`Invoice ${invId} deleted.`);
}

function openNewInvoiceModal() {
    const store = getStore();
    const select = document.getElementById('newInvoiceWaybill');
    select.innerHTML = '';
    store.shipments.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = `${s.id} (${s.route})`;
        select.appendChild(opt);
    });
    openModal('newInvoiceModal');
}

function createNewInvoice(e) {
    e.preventDefault();
    const store = getStore();

    const invoice = document.getElementById('newInvoiceId').value.trim();
    const waybill = document.getElementById('newInvoiceWaybill').value;
    const amount = document.getElementById('newInvoiceAmount').value.trim();
    const due = document.getElementById('newInvoiceDue').value.trim();

    if (store.invoices.some(i => i.id === invoice)) {
        alert('That invoice number already exists.');
        return false;
    }

    store.invoices.unshift({ id: invoice, waybill, amount: `₱${amount}`, status: 'Pending', due });
    saveStore(store);
    renderInvoicesTable();
    updateDashboardKPIs();
    closeModal('newInvoiceModal');
    notifyCustomer(`New invoice ${invoice} issued`);
    alert(`Invoice ${invoice} created and pushed to customer portal.`);
    return false;
}

function searchTrackingWaybills(filter) {
    const select = document.getElementById('waybillSelect');
    if (!select) return;

    const store = getStore();
    const q = filter.toLowerCase();
    select.innerHTML = '';

    store.shipments
        .filter(s => q === '' || s.id.toLowerCase().includes(q) || s.route.toLowerCase().includes(q))
        .forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = `${s.id} (${s.route})`;
            select.appendChild(opt);
        });

    if (select.options.length > 0) {
        switchTrackWaybill(select.value);
    }
}

function searchInvoicesTable() {
    const input = document.getElementById('invoiceSearchInput');
    if (!input) return;
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.invoice-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (text.includes(filter)) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

function downloadAllInvoices() {
    alert("Downloading invoice package (PriorityHandling_Invoices_Q3.zip)...");
}

/* --------------------------------------------------------------------------
   5. DOCUMENT VAULT CONTROL
   -------------------------------------------------------------------------- */

function renderDocumentsGrid() {
    const container = document.getElementById('documentsContainer');
    if (!container) return;

    const store = getStore();
    container.innerHTML = '';

    store.documents.forEach(doc => {
        const categoryLabel = {
            'bill-lading': 'Bill of Lading',
            'customs': 'Customs Declaration',
            'proof-delivery': 'Proof of Delivery',
            'invoice': 'Invoice',
            'other': 'Other'
        }[doc.category] || 'Document';

        container.innerHTML += `
            <div class="doc-item flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/50 transition-colors group cursor-pointer" data-category="${doc.category}" onclick="alert('Downloading ${doc.name} (${doc.type})...')">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 group-hover:text-brand-blue transition-colors">${doc.name}</h4>
                        <span class="text-[10px] text-slate-400">${doc.type} · Uploaded ${doc.uploaded} · ${categoryLabel}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-arrow-down-long text-slate-400 text-xs"></i>
                    <button onclick="event.stopPropagation(); deleteDocument('${doc.id}')" title="Delete" class="text-slate-400 hover:text-red-500 p-1 transition-colors">
                        <i class="fa-solid fa-trash text-[10px]"></i>
                    </button>
                </div>
            </div>
        `;
    });
}

function createNewDocument(e) {
    e.preventDefault();
    const store = getStore();

    const name = document.getElementById('newDocName').value.trim();
    const category = document.getElementById('newDocCategory').value;
    const type = document.getElementById('newDocType').value || 'PDF';

    const docId = 'doc-' + Date.now().toString().slice(-6);
    const uploaded = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

    store.documents.unshift({ id: docId, name, category, type, uploaded });
    saveStore(store);
    renderDocumentsGrid();
    closeModal('newDocumentModal');
    notifyCustomer(`New document "${name}" added to vault`);
    alert(`Document "${name}" uploaded and published to customer vault.`);
    return false;
}

function deleteDocument(docId) {
    if (!confirm('Delete this document from the customer vault?')) return;

    const store = getStore();
    store.documents = store.documents.filter(d => d.id !== docId);
    saveStore(store);
    renderDocumentsGrid();
    notifyCustomer('A document was removed from the vault');
    alert('Document deleted.');
}

function filterDocuments(category, btn) {
    const tabs = document.querySelectorAll('.doc-filter-tab');
    tabs.forEach(t => {
        t.classList.remove('bg-brand-blue', 'text-white', 'shadow-sm');
        t.classList.add('bg-white', 'text-slate-600');
    });

    btn.classList.remove('bg-white', 'text-slate-600');
    btn.classList.add('bg-brand-blue', 'text-white', 'shadow-sm');

    const items = document.querySelectorAll('.doc-item');
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

function searchDocVault() {
    const input = document.getElementById('docSearchInput');
    if (!input) return;
    const filter = input.value.toLowerCase();
    const items = document.querySelectorAll('.doc-item');

    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.includes(filter)) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

/* --------------------------------------------------------------------------
   6. TICKETS CONTROL
   -------------------------------------------------------------------------- */

function renderTicketsList() {
    const container = document.getElementById('ticketsContainer');
    if (!container) return;

    const store = getStore();
    container.innerHTML = '';

    store.tickets.forEach(t => {
        const statusClass = {
            'In Progress': 'bg-blue-100 text-blue-700',
            'Awaiting Reply': 'bg-amber-100 text-amber-700',
            'Resolved': 'bg-emerald-100 text-emerald-700'
        }[t.status] || 'bg-slate-100 text-slate-700';

        container.innerHTML += `
            <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ${t.id} details...')">
                <div class="space-y-1">
                    <span class="text-[10px] font-mono text-slate-400 font-bold block">${t.id}</span>
                    <h4 class="text-xs font-extrabold text-slate-900 hover:text-brand-blue transition-colors">${t.title}</h4>
                </div>
                <div class="flex items-center gap-3">
                    <select onchange="updateTicketStatus('${t.id}', this.value)" class="bg-slate-50 border border-slate-200 text-slate-700 text-[10px] font-semibold px-2 py-1 rounded-lg focus:outline-none focus:border-brand-blue cursor-pointer" onclick="event.stopPropagation()">
                        <option value="In Progress" ${t.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                        <option value="Awaiting Reply" ${t.status === 'Awaiting Reply' ? 'selected' : ''}>Awaiting Reply</option>
                        <option value="Resolved" ${t.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                    </select>
                    <button onclick="event.stopPropagation(); replyToTicket('${t.id}')" title="Reply" class="text-slate-400 hover:text-brand-blue p-1 transition-colors">
                        <i class="fa-solid fa-reply text-[10px]"></i>
                    </button>
                </div>
            </div>
        `;
    });
}

function updateTicketStatus(ticketId, newStatus) {
    const store = getStore();
    const ticket = store.tickets.find(t => t.id === ticketId);
    if (!ticket) return;

    ticket.status = newStatus;
    saveStore(store);
    renderTicketsList();
    updateDashboardKPIs();
    notifyCustomer(`Ticket ${ticketId} status: ${newStatus}`);
    alert(`Ticket ${ticketId} updated to ${newStatus}.`);
}

function replyToTicket(ticketId) {
    const reply = prompt(`Reply to ${ticketId}:`);
    if (reply && reply.trim()) {
        notifyCustomer(`Reply sent to ${ticketId}: "${reply.trim()}"`);
        alert(`Reply sent to customer for ${ticketId}.`);
    }
}

function createNewTicket() {
    const title = prompt("Enter a brief description for the new support ticket:");
    if (!title || !title.trim()) return;

    const store = getStore();
    const randomNum = Math.floor(1000 + Math.random() * 9000);
    store.tickets.unshift({ id: `TCK-${randomNum}`, title: title.trim(), status: 'In Progress' });
    saveStore(store);
    renderTicketsList();
    updateDashboardKPIs();
    alert(`Ticket TCK-${randomNum} created and visible to the customer portal.`);
}

function searchTicketsList() {
    const input = document.getElementById('ticketSearchInput');
    if (!input) return;
    const filter = input.value.toLowerCase();
    const items = document.querySelectorAll('.ticket-item');

    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.includes(filter)) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

/* --------------------------------------------------------------------------
   7. SLA MONITORING CONTROL
   -------------------------------------------------------------------------- */

function renderSlaMetrics() {
    const store = getStore();
    const sla = store.sla;

    const overallEl = document.getElementById('overallCompliance');
    const openBreachesEl = document.getElementById('openBreaches');
    const openBreachesDetailEl = document.getElementById('openBreachesDetail');

    if (overallEl) overallEl.innerText = `${sla.overall}%`;
    if (openBreachesEl) {
        const open = sla.breaches.filter(b => b.status === 'Open').length;
        openBreachesEl.innerText = open;
    }
    if (openBreachesDetailEl) {
        const firstOpen = sla.breaches.find(b => b.status === 'Open');
        openBreachesDetailEl.innerText = firstOpen ? `${firstOpen.commitment}, ${firstOpen.waybill}` : 'No open breaches';
    }
}

function renderBreachLog() {
    const tbody = document.getElementById('breachLogBody');
    if (!tbody) return;

    const store = getStore();
    tbody.innerHTML = '';

    store.sla.breaches.forEach(breach => {
        const statusClass = breach.status === 'Open'
            ? 'bg-red-100 text-red-700'
            : 'bg-emerald-100 text-emerald-700';

        tbody.innerHTML += `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-4 font-mono font-extrabold text-slate-900 text-xs">${breach.waybill}</td>
                <td class="py-4 font-semibold text-slate-800">${breach.commitment}</td>
                <td class="py-4 font-mono text-slate-600">${breach.flagged}</td>
                <td class="py-4 text-right">
                    <span class="${statusClass} font-semibold px-3 py-1 rounded-full text-[10px]">● ${breach.status}</span>
                </td>
                <td class="py-4 text-right">
                    ${breach.status === 'Open' ? `<button onclick="resolveBreach('${breach.waybill}', '${breach.commitment}')" class="text-xs font-semibold text-emerald-600 hover:underline">Resolve</button>` : ''}
                </td>
            </tr>
        `;
    });
}

function resolveBreach(waybill, commitment) {
    const store = getStore();
    const breach = store.sla.breaches.find(b => b.waybill === waybill && b.commitment === commitment);
    if (!breach) return;

    breach.status = 'Resolved';
    // Slight compliance improvement on resolve
    store.sla.overall = Math.min(99, store.sla.overall + 1);
    saveStore(store);
    renderBreachLog();
    renderSlaMetrics();
    updateDashboardKPIs();
    notifyCustomer(`SLA breach ${waybill} (${commitment}) resolved`);
    alert(`Breach ${waybill} (${commitment}) marked as Resolved.`);
}

function adjustSlaCompliance(metric, value) {
    const store = getStore();
    store.sla[metric] = parseInt(value, 10) || 0;

    // Overall = simple average of the four metrics
    store.sla.overall = Math.round((store.sla.pickup + store.sla.transit + store.sla.customs + store.sla.damageFree) / 4);

    saveStore(store);
    renderSlaMetrics();
    renderSlaBars();
    updateDashboardKPIs();
    notifyCustomer(`SLA compliance updated: ${metric} = ${value}%`);
}

function renderSlaBars() {
    const store = getStore();
    const sla = store.sla;

    const bars = [
        { id: 'pickupBar', valueId: 'pickupValue', value: sla.pickup },
        { id: 'transitBar', valueId: 'transitValue', value: sla.transit },
        { id: 'customsBar', valueId: 'customsValue', value: sla.customs },
        { id: 'damageBar', valueId: 'damageValue', value: sla.damageFree }
    ];

    bars.forEach(bar => {
        const barEl = document.getElementById(bar.id);
        const valueEl = document.getElementById(bar.valueId);
        if (barEl) barEl.style.width = `${bar.value}%`;
        if (valueEl) valueEl.innerText = `${bar.value}%`;
    });
}

function renderSettingsSlaSliders() {
    const store = getStore();
    const sla = store.sla;

    const elMap = {
        pickupSlider: { val: 'pickupSliderVal', key: 'pickup' },
        transitSlider: { val: 'transitSliderVal', key: 'transit' },
        customsSlider: { val: 'customsSliderVal', key: 'customs' },
        damageSlider: { val: 'damageSliderVal', key: 'damageFree' }
    };

    Object.entries(elMap).forEach(([sliderId, cfg]) => {
        const slider = document.getElementById(sliderId);
        const valEl = document.getElementById(cfg.val);
        if (slider) {
            slider.value = sla[cfg.key];
            if (valEl) valEl.innerText = `${sla[cfg.key]}%`;
        }
    });
}

/* --------------------------------------------------------------------------
   8. CUSTOMER ACCOUNT CONTROL
   -------------------------------------------------------------------------- */

function renderCustomersTable() {
    const tbody = document.getElementById('customersTableBody');
    if (!tbody) return;

    const store = getStore();
    const shipmentCount = store.shipments.length;
    tbody.innerHTML = '';

    store.customers.forEach(c => {
        const initials = c.company
            .split(' ')
            .map(w => w[0])
            .join('')
            .slice(0, 2)
            .toUpperCase();

        const statusPill = c.portalAccess
            ? '<span class="bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>'
            : '<span class="bg-rose-100 text-rose-700 text-xs font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Disabled</span>';

        tbody.innerHTML += `
            <tr class="hover:bg-slate-50 transition">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-brand-blue/15 text-brand-darkblue rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">${initials}</div>
                        <div class="min-w-0">
                            <span class="text-sm font-bold text-slate-900 block">${c.company}</span>
                            <span class="text-[11px] text-slate-400">Acct #${c.account} · ${c.email}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 font-medium text-slate-700">${c.contact}</td>
                <td class="py-4 px-6">${statusPill}</td>
                <td class="py-4 px-6 font-medium text-slate-700">${shipmentCount}</td>
                <td class="py-4 px-6 text-right font-bold text-slate-900">₱—</td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="toggleCustomerAccess('${c.id}')" class="text-xs font-semibold ${c.portalAccess ? 'text-amber-600 hover:text-amber-700' : 'text-emerald-600 hover:text-emerald-700'} transition">
                            ${c.portalAccess ? 'Disable' : 'Enable'}
                        </button>
                        <button onclick="deleteCustomer('${c.id}')" title="Delete" class="text-slate-400 hover:text-red-500 p-1 transition-colors">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

function toggleCustomerAccess(custId) {
    const store = getStore();
    const customer = store.customers.find(c => c.id === custId);
    if (!customer) return;

    customer.portalAccess = !customer.portalAccess;
    saveStore(store);
    renderCustomersTable();
    updateDashboardKPIs();
    alert(`${customer.company} portal access ${customer.portalAccess ? 'enabled' : 'disabled'}.`);
}

function deleteCustomer(custId) {
    const store = getStore();
    const customer = store.customers.find(c => c.id === custId);
    if (!customer) return;
    if (!confirm(`Delete customer account ${customer.company}?`)) return;

    store.customers = store.customers.filter(c => c.id !== custId);
    saveStore(store);
    renderCustomersTable();
    updateDashboardKPIs();
    alert(`${customer.company} account deleted.`);
}

function createNewCustomer(e) {
    e.preventDefault();
    const store = getStore();

    const company = document.getElementById('newCustCompany').value.trim();
    const contact = document.getElementById('newCustContact').value.trim();
    const email = document.getElementById('newCustEmail').value.trim();
    const warehouse = document.getElementById('newCustWarehouse').value;
    const account = document.getElementById('newCustAccount').value.trim();

    const custId = 'cust-' + Date.now().toString().slice(-6);
    store.customers.push({
        id: custId,
        company,
        contact,
        email,
        account: account || Math.floor(1000 + Math.random() * 9000).toString(),
        portalAccess: true,
        warehouse,
        address: '',
        phone: ''
    });
    saveStore(store);
    renderCustomersTable();
    updateDashboardKPIs();
    closeModal('newCustomerModal');
    notifyCustomer(`Customer account ${company} created with portal access`);
    alert(`Customer ${company} created with portal access enabled.`);
    return false;
}

/* --------------------------------------------------------------------------
   9. DASHBOARD CONTROL WIDGETS & KPI UPDATES
   -------------------------------------------------------------------------- */

function updateDashboardKPIs() {
    const store = getStore();

    const activeShipments = store.shipments.filter(s => s.status !== 'Delivered').length;
    const inTransit = store.shipments.filter(s => s.status === 'In Transit').length;
    const delayed = store.shipments.filter(s => s.status === 'Delayed').length;
    const delivered = store.shipments.filter(s => s.status === 'Delivered').length;
    const openTickets = store.tickets.filter(t => t.status !== 'Resolved').length;
    const openBreaches = store.sla.breaches.filter(b => b.status === 'Open').length;

    const map = {
        'dashboardActiveShipments': activeShipments,
        'dashboardInTransit': inTransit,
        'dashboardDelayed': delayed,
        'dashboardDelivered': delivered,
        'dashboardOpenTickets': openTickets,
        'dashboardOpenBreaches': openBreaches,
        'dashboardActiveCustomers': store.customers.filter(c => c.portalAccess).length
    };

    Object.entries(map).forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.innerText = val;
    });
}

function renderDashboardManifest() {
    const tbody = document.getElementById('dashboardManifestBody');
    if (!tbody) return;

    const store = getStore();
    tbody.innerHTML = '';

    store.shipments.slice(0, 4).forEach(s => {
        const statusClass = {
            'In Transit': 'bg-blue-100 text-blue-700',
            'Customs': 'bg-amber-100 text-amber-700',
            'Delivered': 'bg-emerald-100 text-emerald-700',
            'Delayed': 'bg-red-100 text-red-700'
        }[s.status] || 'bg-slate-100 text-slate-700';

        tbody.innerHTML += `
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
                <td class="py-4">
                    <span class="${statusClass} font-semibold px-2.5 py-1 rounded-full text-[10px]">● ${s.status}</span>
                </td>
                <td class="py-4 text-right font-mono font-medium ${s.status === 'Delayed' ? 'text-red-500' : 'text-slate-700'}">${s.eta}</td>
            </tr>
        `;
    });
}

function renderRecentTickets() {
    const container = document.getElementById('recentTicketsContainer');
    if (!container) return;

    const store = getStore();
    container.innerHTML = '';

    store.tickets.slice(0, 3).forEach(t => {
        const statusClass = {
            'In Progress': 'bg-blue-100 text-blue-700',
            'Awaiting Reply': 'bg-amber-100 text-amber-700',
            'Resolved': 'bg-emerald-100 text-emerald-700'
        }[t.status] || 'bg-slate-100 text-slate-700';

        container.innerHTML += `
            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ${t.id} details...')">
                <div class="space-y-0.5">
                    <span class="text-[10px] font-mono text-slate-400 font-bold block">${t.id}</span>
                    <h4 class="text-xs font-extrabold text-slate-900 truncate max-w-[220px]">${t.title}</h4>
                </div>
                <span class="${statusClass} font-semibold px-2.5 py-1 rounded-full text-[10px]">● ${t.status}</span>
            </div>
        `;
    });
}

/* --------------------------------------------------------------------------
   10. TRACKING PAGE (Leaflet Map + Timeline)
   -------------------------------------------------------------------------- */

let trackingMap;
let trackingPolyline;

function getTrackingData(wbId) {
    const store = getStore();
    const shipment = store.shipments.find(s => s.id === wbId);
    if (!shipment) return null;

    const [origin, dest] = shipment.route.split(' → ');

    return {
        route: shipment.route,
        statusBadge: `● ${shipment.status}`,
        nextCheckpoint: `${dest || 'Destination'} (${shipment.eta})`,
        mapWaypoints: [
            { pos: [14.5995, 120.9842], title: origin || 'Origin', status: "Departed", date: "Jul 26", color: "#38bdf8" },
            { pos: [10.3157, 123.8854], title: dest || 'Destination', status: shipment.status === 'Delivered' ? 'Delivered' : 'Scheduled', date: "Jul 29", color: shipment.status === 'Delivered' ? "#10b981" : "#f59e0b" }
        ],
        milestones: [
            { title: "Booking confirmed", date: "Jul 24, 2026 • 09:12", desc: "Waybill generated, container reserved.", type: "completed" },
            { title: "Picked up from origin", date: "Jul 25, 2026 • 07:40", desc: "Cargo collected from origin warehouse.", type: "completed" },
            { title: `In transit — ${shipment.route}`, date: "Jul 26, 2026 • 22:05", desc: shipment.status === 'Delivered' ? "Transit complete." : "Vessel departed on schedule.", type: shipment.status === 'Delivered' ? "completed" : "active" },
            { title: "Customs clearance", date: shipment.status === 'Delivered' ? "Jul 27, 2026 • 13:00" : "Estimated " + shipment.eta, desc: shipment.status === 'Delivered' ? "Internal clearance passed." : "Pending arrival at destination customs.", type: shipment.status === 'Delivered' ? "completed" : "upcoming", stepNum: 4 },
            { title: "Out for delivery", date: shipment.status === 'Delivered' ? "Jul 28, 2026 • 08:00" : "Estimated " + shipment.eta, desc: "", type: shipment.status === 'Delivered' ? "completed" : "upcoming", stepNum: 5 },
            { title: "Delivered", date: shipment.eta, desc: shipment.status === 'Delivered' ? "Signed by recipient." : "", type: shipment.status === 'Delivered' ? "completed" : "upcoming", stepNum: 6 }
        ]
    };
}

function renderWaybillTimeline(wbId) {
    const container = document.getElementById('timelineContainer');
    if (!container) return;

    const data = getTrackingData(wbId);
    if (!data) return;

    container.innerHTML = '';

    data.milestones.forEach(m => {
        let iconHtml = '';
        if (m.type === 'completed') {
            iconHtml = `<div class="absolute -left-[31px] bg-emerald-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs shadow-md"><i class="fa-solid fa-check"></i></div>`;
        } else if (m.type === 'active') {
            iconHtml = `<div class="absolute -left-[31px] bg-brand-blue text-white w-6 h-6 rounded-full flex items-center justify-center text-xs shadow-md shadow-blue-500/50"><div class="w-2.5 h-2.5 bg-white rounded-full"></div></div>`;
        } else {
            iconHtml = `<div class="absolute -left-[31px] bg-slate-100 text-slate-400 border border-slate-300 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold">${m.stepNum}</div>`;
        }

        container.innerHTML += `
            <div class="relative pl-4 space-y-1">
                ${iconHtml}
                <div class="flex items-baseline gap-2">
                    <h4 class="text-sm font-extrabold ${m.type === 'upcoming' ? 'text-slate-400' : 'text-slate-900'}">${m.title}</h4>
                    <span class="text-xs text-slate-400 font-mono">${m.date}</span>
                </div>
                ${m.desc ? `<p class="text-xs text-slate-500 leading-relaxed">${m.desc}</p>` : ''}
            </div>
        `;
    });

    const nextCheckpoint = document.getElementById('nextCheckpointText');
    const routeBadge = document.getElementById('routeMapBadge');

    if (nextCheckpoint) nextCheckpoint.innerText = data.nextCheckpoint;
    if (routeBadge) routeBadge.innerText = data.statusBadge;
}

function initTrackingLeafletMap(wbId) {
    const mapElement = document.getElementById('trackingMap');
    if (!mapElement || typeof L === 'undefined') return;

    if (trackingMap) trackingMap.remove();

    const data = getTrackingData(wbId);
    if (!data) return;

    trackingMap = L.map('trackingMap', {
        center: [12, 122],
        zoom: 5,
        zoomControl: true,
        attributionControl: false
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd'
    }).addTo(trackingMap);

    const latLngs = data.mapWaypoints.map(w => w.pos);
    trackingPolyline = L.polyline(latLngs, {
        color: '#1D2E6A',
        weight: 3.5,
        opacity: 0.9,
        dashArray: '8, 8'
    }).addTo(trackingMap);

    data.mapWaypoints.forEach(wp => {
        const customIcon = L.divIcon({
            className: 'custom-pin',
            html: `<div style="background-color: ${wp.color}; width: 14px; height: 14px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 0 10px ${wp.color};"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        L.marker(wp.pos, { icon: customIcon })
            .addTo(trackingMap)
            .bindPopup(`<b style="color:#1D2E6A;">${wp.title}</b><br>${wp.status}`);
    });

    trackingMap.fitBounds(trackingPolyline.getBounds(), { padding: [40, 40] });
}

function switchTrackWaybill(wbId) {
    renderWaybillTimeline(wbId);
    initTrackingLeafletMap(wbId);
}

function populateWaybillSelect() {
    const select = document.getElementById('waybillSelect');
    if (!select) return;

    const store = getStore();
    select.innerHTML = '';

    store.shipments.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = `${s.id} (${s.route})`;
        select.appendChild(opt);
    });

    if (store.shipments.length > 0) {
        select.value = store.shipments[0].id;
        switchTrackWaybill(store.shipments[0].id);
    }
}

/* --------------------------------------------------------------------------
   11. MODAL HELPERS
   -------------------------------------------------------------------------- */

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('opacity-0', 'pointer-events-none');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('opacity-0', 'pointer-events-none');
        const form = modal.querySelector('form');
        if (form) form.reset();
    }
}

function openNewShipmentModal() {
    document.getElementById('newWaybill').value = 'PH-WB-' + Math.floor(100000 + Math.random() * 900000);
    openModal('shipmentModal');
}

/* --------------------------------------------------------------------------
   12. NOTIFICATION SOUND SUPPORT
   -------------------------------------------------------------------------- */
const NOTIFICATION_SOUND_KEY = 'priority_notif_sound';
const DEFAULT_NOTIFICATION_SOUND = 'notification-1.mp3';

function getNotificationSound() {
    return localStorage.getItem(NOTIFICATION_SOUND_KEY) || DEFAULT_NOTIFICATION_SOUND;
}

function saveNotificationSound(sound) {
    localStorage.setItem(NOTIFICATION_SOUND_KEY, sound || DEFAULT_NOTIFICATION_SOUND);
}

function playNotificationSound(sound) {
    try {
        const audio = new Audio('audio/' + (sound || getNotificationSound()));
        audio.volume = 0.6;
        audio.play().catch(() => {});
    } catch (e) {
        console.warn('[Notification Sound] Unable to play audio:', e);
    }
}

function previewNotificationSound() {
    const select = document.getElementById('notifSoundSelect');
    playNotificationSound(select ? select.value : null);
}

function initNotificationSoundSetting() {
    const select = document.getElementById('notifSoundSelect');
    if (select) select.value = getNotificationSound();
}

/* --------------------------------------------------------------------------
   13. SETTINGS
   -------------------------------------------------------------------------- */

/* --------------------------------------------------------------------------
   SETTINGS — deferred "Apply Changes" model
   All edits (appearance/dark mode, SLA targets, account fields, notification
   sound) are staged locally and only persisted when applySettings() runs.
   -------------------------------------------------------------------------- */
const DARK_MODE_KEY = 'priority_dark_mode';
let pendingSettings = {
    darkMode: null,   // null => unchanged
    sound: null,     // null => unchanged
    sla: null,       // null => unchanged (object of slider values)
    account: null    // null => unchanged (object of field values)
};

function getStagedDarkMode() {
    const isDark = document.documentElement.classList.contains('dark');
    return (pendingSettings.darkMode === null) ? isDark : pendingSettings.darkMode;
}

function showApplyBar() {
    const bar = document.getElementById('applyBar');
    if (bar) bar.classList.remove('hidden');
}

function stageAppearanceDark(isOn) {
    pendingSettings.darkMode = !!isOn;
    showApplyBar();
}

function stageNotificationSound(value) {
    pendingSettings.sound = value;
    showApplyBar();
}

function stageSlaTargets() {
    pendingSettings.sla = {
        pickup: parseInt(document.getElementById('pickupSlider').value, 10) || 0,
        transit: parseInt(document.getElementById('transitSlider').value, 10) || 0,
        customs: parseInt(document.getElementById('customsSlider').value, 10) || 0,
        damageFree: parseInt(document.getElementById('damageSlider').value, 10) || 0
    };
    showApplyBar();
}

function stageAgentDetails(e) {
    e.preventDefault();
    pendingSettings.account = {
        name: document.getElementById('agentName') ? document.getElementById('agentName').value : null,
        email: document.getElementById('agentEmail') ? document.getElementById('agentEmail').value : null
    };
    showApplyBar();
}

function applySettings() {
    // 1. Dark mode
    if (pendingSettings.darkMode !== null) {
        localStorage.setItem(DARK_MODE_KEY, pendingSettings.darkMode ? 'true' : 'false');
        document.documentElement.classList.toggle('dark', pendingSettings.darkMode);
    }
    // 2. Notification sound
    if (pendingSettings.sound !== null) {
        saveNotificationSound(pendingSettings.sound);
    }
    // 3. SLA targets (persist to shared store)
    if (pendingSettings.sla !== null) {
        const store = getStore();
        store.sla = Object.assign(store.sla || {}, pendingSettings.sla);
        store.sla.overall = Math.round((store.sla.pickup + store.sla.transit + store.sla.customs + store.sla.damageFree) / 4);
        saveStore(store);
        if (typeof renderSlaMetrics === 'function') renderSlaMetrics();
        if (typeof renderSlaBars === 'function') renderSlaBars();
        if (typeof updateDashboardKPIs === 'function') updateDashboardKPIs();
    }
    // 4. Agent account
    if (pendingSettings.account !== null) {
        const session = JSON.parse(localStorage.getItem('priority_agent_session') || '{}');
        if (pendingSettings.account.name !== null) session.user = pendingSettings.account.name;
        if (pendingSettings.account.email !== null) session.email = pendingSettings.account.email;
        localStorage.setItem('priority_agent_session', JSON.stringify(session));
    }

    pendingSettings = { darkMode: null, sound: null, sla: null, account: null };

    const bar = document.getElementById('applyBar');
    if (bar) bar.classList.add('hidden');

    const btn = document.getElementById('saveAccountBtn');
    if (btn) {
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-check"></i> Applied`;
        setTimeout(() => { btn.disabled = false; btn.innerHTML = original; }, 1200);
    }
}

function discardSettings() {
    pendingSettings = { darkMode: null, sound: null, sla: null, account: null };
    const bar = document.getElementById('applyBar');
    if (bar) bar.classList.add('hidden');
    location.reload();
}

function initAppearanceSetting() {
    const toggle = document.getElementById('appearanceDarkToggle');
    if (toggle) toggle.checked = document.documentElement.classList.contains('dark');
}

function exportLeadsCsv() {
    const tbody = document.getElementById('leadsTableBody');
    if (!tbody) return false;

    const headers = [];
    const headerRow = tbody.closest('table') && tbody.closest('table').querySelector('thead tr');
    if (headerRow) {
        headerRow.querySelectorAll('th').forEach(th => headers.push(th.textContent.trim()));
    }

    const rows = [];
    tbody.querySelectorAll('tr').forEach(tr => {
        rows.push([...tr.querySelectorAll('td')].map(td => td.textContent.trim()));
    });

    if (!rows.length) {
        alert('No leads to export.');
        return false;
    }

    const escape = v => `"${String(v).replace(/"/g, '""')}"`;
    const csv = [headers, ...rows].map(r => r.map(escape).join(',')).join('\r\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `priorityhandling-leads-${new Date().toISOString().slice(0, 10)}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    return false;
}

/* --------------------------------------------------------------------------
   14. PAGE INITIALIZATION
   -------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {
    checkAgentSession();
    initTrackingPage();
    initNotificationSoundSetting();
    initAppearanceSetting();

    // Seed default store on first load
    getStore();

    // Page-specific renderers
    if (document.getElementById('shipmentsTableBody')) renderShipmentsTable();
    if (document.getElementById('invoicesTableBody')) renderInvoicesTable();
    if (document.getElementById('documentsContainer')) renderDocumentsGrid();
    if (document.getElementById('ticketsContainer')) renderTicketsList();
    if (document.getElementById('breachLogBody')) {
        renderBreachLog();
        renderSlaMetrics();
        renderSlaBars();
    }
    if (document.getElementById('customersTableBody')) renderCustomersTable();
    if (document.getElementById('dashboardManifestBody')) {
        renderDashboardManifest();
        renderRecentTickets();
    }
    if (document.getElementById('overallCompliance')) renderSlaMetrics();
    if (document.getElementById('pickupSlider')) renderSettingsSlaSliders();

    updateDashboardKPIs();

    // Fix reveal animation: activate all .reveal elements on page load
    const revealElements = document.querySelectorAll('.reveal');
    revealElements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('active');
        }, index * 150);
    });
});

function initTrackingPage() {
    const scheduleSelect = document.getElementById('waybillSelect');
    if (scheduleSelect) {
        populateWaybillSelect();
    }
}

function openPrivacyModal() {
    const modal = document.getElementById('privacyModal');
    if (modal) modal.classList.remove('opacity-0', 'pointer-events-none');
}

function closePrivacyModal() {
    const modal = document.getElementById('privacyModal');
    if (modal) modal.classList.add('opacity-0', 'pointer-events-none');
}

function openTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.remove('opacity-0', 'pointer-events-none');
}

function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.add('opacity-0', 'pointer-events-none');
}