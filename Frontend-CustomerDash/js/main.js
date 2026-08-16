/* ==========================================================================
   PRIORITY HANDLING LOGISTICS PORTAL JAVASCRIPT LOGIC
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. HERO SLIDESHOW LOGIC
   -------------------------------------------------------------------------- */
let currentHeroSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.hero-dot');
let heroTimer;

const slideMetadata = [
    { name: 'Cross-Border Trucking', icon: 'fa-truck-fast' },
    { name: 'Smart Warehousing', icon: 'fa-warehouse' },
    { name: 'Ocean Freight Logistics', icon: 'fa-ship' },
    { name: 'Express Air Freight', icon: 'fa-plane' }
];

function showHeroSlide(index) {
    if (!slides.length) return;
    currentHeroSlide = (index + slides.length) % slides.length;
    
    slides.forEach((slide, i) => {
        if (i === currentHeroSlide) {
            slide.classList.remove('opacity-0', 'pointer-events-none');
            slide.classList.add('opacity-100');
        } else {
            slide.classList.remove('opacity-100');
            slide.classList.add('opacity-0', 'pointer-events-none');
        }
    });

    dots.forEach((dot, i) => {
        if (i === currentHeroSlide) {
            dot.classList.remove('bg-white/50', 'w-1.5');
            dot.classList.add('bg-white', 'w-5');
        } else {
            dot.classList.remove('bg-white', 'w-5');
            dot.classList.add('bg-white/50', 'w-1.5');
        }
    });

    const badgeIcon = document.getElementById('heroBadgeIcon');
    const badgeText = document.getElementById('heroBadgeText');
    if (badgeIcon && badgeText) {
        badgeIcon.className = `fa-solid ${slideMetadata[currentHeroSlide].icon}`;
        badgeText.innerText = slideMetadata[currentHeroSlide].name;
    }
}

function nextHeroSlide() { showHeroSlide(currentHeroSlide + 1); }
function prevHeroSlide() { showHeroSlide(currentHeroSlide - 1); }
function setHeroSlide(index) { showHeroSlide(index); }

function startHeroTimer() {
    if (slides.length > 0) {
        heroTimer = setInterval(() => { nextHeroSlide(); }, 3800);
    }
}
function stopHeroTimer() { clearInterval(heroTimer); }

/* --------------------------------------------------------------------------
   2. AUTHENTICATION & SETTINGS HANDLERS
   -------------------------------------------------------------------------- */

// Demo OTP constant (in production this would be sent via email/SMS)
const DEMO_OTP = '123456';
let generatedOtp = DEMO_OTP;

function handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
    const loginBtn = document.getElementById('loginBtn');
    const status = document.getElementById('loginStatus');

    loginBtn.disabled = true;
    loginBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...`;

    setTimeout(() => {
        if (email === 'client@company.ph' && password === 'demo1234') {
            // Credentials valid — proceed to OTP verification step
            loginBtn.disabled = false;
            loginBtn.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i> Sign In to Client Portal`;
            status.classList.add('hidden');

            // Generate a random 6-digit OTP for demo purposes
            generatedOtp = String(Math.floor(100000 + Math.random() * 900000));
            console.log(`[Priority Handling Demo] OTP sent to ${email}: ${generatedOtp}`);

            // Show OTP form, hide credentials form
            document.getElementById('credentialsForm').classList.add('hidden');
            document.getElementById('otpForm').classList.remove('hidden');
            document.getElementById('otpEmailDisplay').innerText = email;
            document.getElementById('otpInput').value = '';
            document.getElementById('otpStatus').classList.add('hidden');

            // Auto-focus OTP input
            setTimeout(() => document.getElementById('otpInput').focus(), 100);
        } else {
            loginBtn.disabled = false;
            loginBtn.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i> Sign In to Client Portal`;
            status.classList.remove('hidden');
            status.innerText = 'Invalid credentials. Use client@company.ph / demo1234';
        }
    }, 1000);
}

function handleOtpVerify(e) {
    e.preventDefault();

    const otpInput = document.getElementById('otpInput').value.trim();
    const otpBtn = document.getElementById('otpBtn');
    const otpStatus = document.getElementById('otpStatus');
    const email = document.getElementById('loginEmail').value.trim();

    otpBtn.disabled = true;
    otpBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Verifying...`;

    setTimeout(() => {
        if (otpInput === generatedOtp) {
            // OTP verified — create session and redirect
            localStorage.setItem('priority_session', JSON.stringify({
                user: 'Juan Dela Cruz',
                company: 'Acme Logistics PH',
                email: email,
                token: 'session_token_ph_99218',
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
    localStorage.removeItem('priority_session');
    window.location.href = 'index.php';
}

function scrollToLogin() {
    const loginCard = document.getElementById('login-card');
    if (loginCard) {
        loginCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function openPrivacyModal() {
    const modal = document.getElementById('privacyModal');
    modal.classList.remove('opacity-0', 'pointer-events-none');
}

function closePrivacyModal() {
    const modal = document.getElementById('privacyModal');
    modal.classList.add('opacity-0', 'pointer-events-none');
}

function openTermsModal() {
    const modal = document.getElementById('termsModal');
    modal.classList.remove('opacity-0', 'pointer-events-none');
}

function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    modal.classList.add('opacity-0', 'pointer-events-none');
}

function checkAuthSession() {
    const session = localStorage.getItem('priority_session');
    const isProtectedPage = window.location.pathname.includes('dashboard.php') || 
                            window.location.pathname.includes('shipments.php') || 
                            window.location.pathname.includes('tracking.php') ||
                            window.location.pathname.includes('sla-monitoring.php') ||
                            window.location.pathname.includes('documents.php') ||
                            window.location.pathname.includes('invoices.php') ||
                            window.location.pathname.includes('analytics.php') ||
                            window.location.pathname.includes('tickets.php') ||
                            window.location.pathname.includes('settings.php');

    if (isProtectedPage && !session) {
        window.location.href = 'index.php';
    }
}

/* --------------------------------------------------------------------------
   SETTINGS — deferred "Apply Changes" model
   All edits (appearance/dark mode, account fields, notification sound) are
   staged locally and only persisted when applySettings() is invoked.
   -------------------------------------------------------------------------- */
const DARK_MODE_KEY = 'priority_dark_mode';
let pendingSettings = {
    darkMode: null,   // null => unchanged
    sound: null,     // null => unchanged
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

function stageAccountDetails(e) {
    e.preventDefault();
    pendingSettings.account = {
        company: document.getElementById('settingCompany').value,
        email: document.getElementById('settingEmail').value,
        phone: document.getElementById('settingPhone').value,
        warehouse: document.getElementById('settingWarehouse').value,
        address: document.getElementById('settingAddress').value
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
    // 3. Account details
    if (pendingSettings.account !== null && typeof updateCustomerProfile === 'function') {
        updateCustomerProfile(pendingSettings.account);
    }

    pendingSettings = { darkMode: null, sound: null, account: null };

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
    // Revert any staged UI state by reloading from persisted sources.
    pendingSettings = { darkMode: null, sound: null, account: null };
    const bar = document.getElementById('applyBar');
    if (bar) bar.classList.add('hidden');
    location.reload();
}

function initAppearanceSetting() {
    const toggle = document.getElementById('appearanceDarkToggle');
    if (toggle) toggle.checked = document.documentElement.classList.contains('dark');
}

document.addEventListener('DOMContentLoaded', () => {
    checkAuthSession();
    startHeroTimer();
    initTrackingPage();
    initNotificationSoundSetting();
    initAppearanceSetting();

    // Fix reveal animation: activate all .reveal elements on page load
    const revealElements = document.querySelectorAll('.reveal');
    revealElements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('active');
        }, index * 150);
    });
});

/* --------------------------------------------------------------------------
   3. SUPPORT TICKETS FUNCTIONS
   -------------------------------------------------------------------------- */
function createNewTicket() {
    const title = prompt("Enter a brief description for your new support ticket:");
    if (title && title.trim()) {
        const container = document.getElementById('ticketsContainer');
        if (!container) return;

        const randomNum = Math.floor(1000 + Math.random() * 9000);
        const newTicketHtml = `
            <div class="ticket-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors cursor-pointer" onclick="alert('Opening ticket TCK-${randomNum} details...')">
                <div class="space-y-1">
                    <span class="text-[10px] font-mono text-slate-400 font-bold block">TCK-${randomNum}</span>
                    <h4 class="text-xs font-extrabold text-slate-900 hover:text-brand-blue transition-colors">${title.trim()}</h4>
                </div>
                <span class="bg-blue-100 text-blue-700 font-semibold px-3 py-1 rounded-full text-[10px]">● In Progress</span>
            </div>
        `;
        container.insertAdjacentHTML('afterbegin', newTicketHtml);
        alert(`Ticket TCK-${randomNum} created successfully! Routed to Priority Support PH.`);
    }
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
   4. INVOICES & BILLING FUNCTIONS
   -------------------------------------------------------------------------- */
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
    alert("Downloading invoice package (Robles_Cargo_Invoices_Q3.zip)...");
}

/* --------------------------------------------------------------------------
   5. DOCUMENT VAULT FILTERING & UPLOAD
   -------------------------------------------------------------------------- */
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

function triggerUploadDoc() {
    const hiddenInput = document.getElementById('hiddenFileInput');
    if (hiddenInput) hiddenInput.click();
}

function handleFileSelected(e) {
    const file = e.target.files[0];
    if (file) {
        alert(`Successfully uploaded "${file.name}" to your Priority Handling Document Vault.`);
    }
}

/* --------------------------------------------------------------------------
   6. LIVE TRACKING TIMELINE & LEAFLET MAP LOGIC
   -------------------------------------------------------------------------- */
let trackingMap;
let trackingPolyline;

const trackingWaybills = {
    'PH-WB-208841': {
        route: 'Manila → Cebu',
        statusBadge: '● Vessel In Transit',
        nextCheckpoint: 'Cebu Port Terminal (Jul 29)',
        mapWaypoints: [
            { pos: [14.5995, 120.9842], title: "Manila South Harbor", status: "Departed", date: "Jul 26", color: "#38bdf8" },
            { pos: [10.3157, 123.8854], title: "Cebu Port Terminal", status: "Scheduled", date: "Jul 29", color: "#f59e0b" }
        ],
        milestones: [
            { title: "Booking confirmed", date: "Jul 24, 2026 • 09:12", desc: "Waybill generated, container reserved at Manila South Harbor.", type: "completed" },
            { title: "Picked up from origin", date: "Jul 25, 2026 • 07:40", desc: "Cargo collected from Robles Cargo warehouse, Caloocan.", type: "completed" },
            { title: "In transit — Manila to Cebu", date: "Jul 26, 2026 • 22:05", desc: "Vessel departed on schedule. Next checkpoint: Cebu port, Jul 29.", type: "active" },
            { title: "Customs clearance", date: "Estimated Jul 29", desc: "Pending arrival at Cebu Customs Office.", type: "upcoming", stepNum: 4 },
            { title: "Out for delivery", date: "Estimated Jul 29", desc: "", type: "upcoming", stepNum: 5 },
            { title: "Delivered", date: "Estimated Jul 29, 14:00", desc: "", type: "upcoming", stepNum: 6 }
        ]
    },
    'PH-WB-208835': {
        route: 'Cebu → Manila',
        statusBadge: '● BOC Clearance',
        nextCheckpoint: 'MICP Manila (Jul 30)',
        mapWaypoints: [
            { pos: [10.3157, 123.8854], title: "Cebu Port", status: "Departed", date: "Jul 27", color: "#38bdf8" },
            { pos: [14.5995, 120.9842], title: "Manila MICP Customs", status: "Inspecting", date: "Jul 30", color: "#a855f7" }
        ],
        milestones: [
            { title: "Booking confirmed", date: "Jul 25, 2026 • 10:00", desc: "Waybill generated for 20ft dry van container.", type: "completed" },
            { title: "Picked up from origin", date: "Jul 26, 2026 • 14:15", desc: "Cargo loaded at Cebu Logistics Center.", type: "completed" },
            { title: "In transit — Cebu to Manila", date: "Jul 27, 2026 • 18:30", desc: "Inter-island vessel transit complete.", type: "completed" },
            { title: "Customs clearance", date: "Jul 30, 2026 • 09:00", desc: "Under official Bureau of Customs (BOC) inspection.", type: "active" },
            { title: "Out for delivery", date: "Estimated Jul 30", desc: "", type: "upcoming", stepNum: 5 },
            { title: "Delivered", date: "Estimated Jul 30, 16:00", desc: "", type: "upcoming", stepNum: 6 }
        ]
    },
    'PH-WB-208790': {
        route: 'Davao → Manila',
        statusBadge: '● Cargo Delivered',
        nextCheckpoint: 'Destination Reached',
        mapWaypoints: [
            { pos: [7.1907, 125.4553], title: "Davao Port", status: "Departed", date: "Jul 20", color: "#38bdf8" },
            { pos: [14.5995, 120.9842], title: "Manila Warehouse", status: "Delivered", date: "Jul 25", color: "#10b981" }
        ],
        milestones: [
            { title: "Booking confirmed", date: "Jul 18, 2026 • 08:00", desc: "LCL Palletized shipment booked.", type: "completed" },
            { title: "Picked up from origin", date: "Jul 19, 2026 • 11:30", desc: "Davao Hub pickup completed.", type: "completed" },
            { title: "In transit — Davao to Manila", date: "Jul 20, 2026 • 20:00", desc: "Express vessel transit finished.", type: "completed" },
            { title: "Customs clearance", date: "Jul 24, 2026 • 13:00", desc: "Internal clearance passed.", type: "completed" },
            { title: "Out for delivery", date: "Jul 25, 2026 • 08:00", desc: "Loaded onto final delivery truck.", type: "completed" },
            { title: "Delivered", date: "Jul 25, 2026 • 11:20", desc: "Signed by recipient: J. Sison.", type: "completed" }
        ]
    }
};

function initTrackingPage() {
    const timelineContainer = document.getElementById('timelineContainer');
    if (!timelineContainer) return;

    renderWaybillTimeline('PH-WB-208841');
    initTrackingLeafletMap('PH-WB-208841');
}

const PORT_COORDS = {
    manila: [14.5995, 120.9842],
    cebu: [10.3157, 123.8854],
    davao: [7.1907, 125.4553],
    iloilo: [10.7202, 122.5621],
    'cagayan de oro': [8.4856, 124.6476],
    bacolod: [10.6195, 122.9825]
};

/* Look up a shipment directly in the shared store (written by the Sales Agent
   portal) so waybills the agent created render without hard-coded data. */
function getStoreShipment(wbId) {
    try {
        const raw = localStorage.getItem('priority_dashboard_data');
        if (!raw) return null;
        const store = JSON.parse(raw);
        return (store.shipments || []).find(s => s.id === wbId) || null;
    } catch (e) {
        return null;
    }
}

/* Build tracking data for any waybill: prefer the static rich demo data,
   otherwise generate a timeline + map from the shared store shipment. */
function getWaybillTrackingData(wbId) {
    if (trackingWaybills[wbId]) return trackingWaybills[wbId];

    const shipment = getStoreShipment(wbId);
    if (!shipment) return null;

    const routeParts = (shipment.route || 'Manila → Cebu').split('→').map(p => p.trim());
    const resolveCoord = (name) => {
        if (!name) return null;
        const key = name.toLowerCase();
        return PORT_COORDS[key] || null;
    };

    const fromName = routeParts[0] || null;
    const toName = routeParts[1] || null;
    const from = resolveCoord(fromName);
    const to = resolveCoord(toName);

    const status = shipment.status || 'In Transit';
    const eta = shipment.eta || 'TBA';
    const statusLabel = status === 'Delivered' ? 'Cargo Delivered' : status;

    const milestones = [
        { title: 'Booking confirmed', date: 'On file', desc: 'Waybill generated; container/cargo reserved.', type: 'completed' },
        { title: 'Picked up from origin', date: 'On file', desc: `Cargo collected from ${fromName || 'origin'} hub.`, type: 'completed' },
        { title: `In transit — ${shipment.route || ''}`, date: eta, desc: status === 'Delivered' ? 'Transit complete.' : 'Vessel/cargo en route — see below.', type: status === 'In Transit' ? 'active' : 'completed' },
        { title: 'Customs clearance', date: status === 'Delivered' ? 'Cleared' : 'Estimated ' + eta, desc: status === 'Customs' ? 'Under BOC inspection.' : 'Pending destination clearance.', type: status === 'Customs' ? 'active' : 'upcoming', stepNum: 4 },
        { title: 'Out for delivery', date: 'Estimated ' + eta, desc: status === 'Delivered' ? 'Delivered to consignee.' : '', type: status === 'Delivered' ? 'completed' : 'upcoming', stepNum: 5 },
        { title: 'Delivered', date: eta, desc: status === 'Delivered' ? 'Signed by recipient.' : '', type: status === 'Delivered' ? 'completed' : 'upcoming', stepNum: 6 }
    ];

    const waypoints = [];
    if (from) {
        waypoints.push({ pos: from, title: fromName, status: status === 'Delivered' ? 'Delivered' : 'Departed', date: 'On file', color: status === 'Delivered' ? '#10b981' : '#38bdf8' });
    }
    if (to) {
        waypoints.push({ pos: to, title: toName, status: status === 'Delivered' ? 'Delivered' : status === 'In Transit' ? 'Scheduled' : status, date: eta, color: status === 'Delivered' ? '#10b981' : status === 'Delayed' ? '#ef4444' : '#f59e0b' });
    }
    if (!waypoints.length) {
        waypoints.push({ pos: [12, 122], title: 'Philippines', status: statusLabel, date: eta, color: '#38bdf8' });
    }

    return {
        route: shipment.route || '—',
        statusBadge: '● ' + statusLabel,
        nextCheckpoint: toName ? `${toName} (${eta})` : 'Destination — ETA ' + eta,
        mapWaypoints: waypoints,
        milestones: milestones
    };
}

function renderWaybillTimeline(wbId) {
    const container = document.getElementById('timelineContainer');
    if (!container) return;

    const data = getWaybillTrackingData(wbId);
    if (!data) {
        container.innerHTML = '<div class="text-[11px] text-slate-400 py-2">No tracking data yet for this waybill.</div>';
        return;
    }
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

        const stepCard = `
            <div class="relative pl-4 space-y-1">
                ${iconHtml}
                <div class="flex items-baseline gap-2">
                    <h4 class="text-sm font-extrabold ${m.type === 'upcoming' ? 'text-slate-400' : 'text-slate-900'}">${m.title}</h4>
                    <span class="text-xs text-slate-400 font-mono">${m.date}</span>
                </div>
                ${m.desc ? `<p class="text-xs text-slate-500 leading-relaxed">${m.desc}</p>` : ''}
            </div>
        `;
        container.innerHTML += stepCard;
    });

    const cpEl = document.getElementById('nextCheckpointText');
    const badgeEl = document.getElementById('routeMapBadge');
    if (cpEl) cpEl.innerText = data.nextCheckpoint;
    if (badgeEl) badgeEl.innerText = data.statusBadge;
}

function initTrackingLeafletMap(wbId) {
    const mapElement = document.getElementById('trackingMap');
    if (!mapElement) return;

    if (trackingMap) {
        trackingMap.remove();
    }

    const data = getWaybillTrackingData(wbId);
    if (!data) {
        // Show the map shell without crashing for waybills with no data yet
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
        return;
    }

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

    if (data.mapWaypoints.length > 1) {
        trackingMap.fitBounds(trackingPolyline.getBounds(), { padding: [40, 40] });
    } else {
        trackingMap.setView(latLngs[0] || [12, 122], 9);
    }
}

function switchTrackWaybill(wbId) {
    renderWaybillTimeline(wbId);
    initTrackingLeafletMap(wbId);
}

/* --------------------------------------------------------------------------
   7. SHIPMENT PAGE FILTERING & EXPORT
   -------------------------------------------------------------------------- */
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
    alert("Exporting shipments data to Robles_Cargo_Waybills.csv...");
}

/* --------------------------------------------------------------------------
   8. FLOATING CHAT WIDGET (with notification sound support)
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

function toggleChat() {
    const chatBox = document.getElementById('chatBox');
    if (!chatBox) return;
    const wasClosed = chatBox.classList.contains('opacity-0');
    chatBox.classList.toggle('opacity-0');
    chatBox.classList.toggle('pointer-events-none');
    chatBox.classList.toggle('translate-y-6');
    if (wasClosed) playNotificationSound();
}

function handleChatKeyPress(e) {
    if (e.key === 'Enter') sendMessage();
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;

    const chatBody = document.getElementById('chatBody');

    const userMsg = document.createElement('div');
    userMsg.className = 'bg-brand-blue text-white text-xs p-3 rounded-lg max-w-[85%] self-end shadow-sm leading-relaxed';
    userMsg.innerText = text;
    chatBody.appendChild(userMsg);

    input.value = '';
    chatBody.scrollTop = chatBody.scrollHeight;

    setTimeout(() => {
        const botMsg = document.createElement('div');
        botMsg.className = 'bg-white border border-slate-200 text-slate-800 text-xs p-3 rounded-lg max-w-[85%] self-start shadow-sm leading-relaxed';
        botMsg.innerText = "Mabuhay! A Priority Handling support specialist will assist you with your portal inquiry shortly.";
        chatBody.appendChild(botMsg);
        chatBody.scrollTop = chatBody.scrollHeight;
        playNotificationSound();
    }, 700);
}