/* ==========================================================================
   SWIFTFREIGHT PORTAL JAVASCRIPT LOGIC
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
            localStorage.setItem('swift_session', JSON.stringify({
                user: 'Juan Dela Cruz',
                company: 'Acme Logistics PH',
                email: email,
                token: 'session_token_ph_99218'
            }));
            window.location.href = 'dashboard.html';
        } else {
            loginBtn.disabled = false;
            loginBtn.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i> Sign In to SwiftPortal`;
            status.classList.remove('hidden');
            status.innerText = 'Invalid credentials. Use client@company.ph / demo1234';
        }
    }, 1000);
}

function handleLogout() {
    localStorage.removeItem('swift_session');
    window.location.href = 'index.html';
}

function checkAuthSession() {
    const session = localStorage.getItem('swift_session');
    const isProtectedPage = window.location.pathname.includes('dashboard.html') || 
                            window.location.pathname.includes('shipments.html') || 
                            window.location.pathname.includes('tracking.html') ||
                            window.location.pathname.includes('sla-monitoring.html') ||
                            window.location.pathname.includes('documents.html') ||
                            window.location.pathname.includes('invoices.html') ||
                            window.location.pathname.includes('analytics.html') ||
                            window.location.pathname.includes('tickets.html') ||
                            window.location.pathname.includes('settings.html');

    if (isProtectedPage && !session) {
        window.location.href = 'index.html';
    }
}

function saveAccountDetails(e) {
    e.preventDefault();
    const btn = document.getElementById('saveAccountBtn');
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Saving...`;

    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = `Save changes`;
        alert("Account details and notification preferences saved successfully!");
    }, 800);
}

document.addEventListener('DOMContentLoaded', () => {
    checkAuthSession();
    startHeroTimer();
    initTrackingPage();
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
        alert(`Ticket TCK-${randomNum} created successfully! Routed to SwiftSupport PH.`);
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
        alert(`Successfully uploaded "${file.name}" to your SwiftFreight Document Vault.`);
    }
}

/* --------------------------------------------------------------------------
   6. LIVE TRACKING TIMELINE & LEAFLET MAP LOGIC
   -------------------------------------------------------------------------- */
let trackingMap;
let trackingPolyline;

const trackingWaybills = {
    'SF-WB-208841': {
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
    'SF-WB-208835': {
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
    'SF-WB-208790': {
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

    renderWaybillTimeline('SF-WB-208841');
    initTrackingLeafletMap('SF-WB-208841');
}

function renderWaybillTimeline(wbId) {
    const container = document.getElementById('timelineContainer');
    if (!container || !trackingWaybills[wbId]) return;

    const data = trackingWaybills[wbId];
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

    document.getElementById('nextCheckpointText').innerText = data.nextCheckpoint;
    document.getElementById('routeMapBadge').innerText = data.statusBadge;
}

function initTrackingLeafletMap(wbId) {
    const mapElement = document.getElementById('trackingMap');
    if (!mapElement) return;

    if (trackingMap) {
        trackingMap.remove();
    }

    const data = trackingWaybills[wbId];

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
        color: '#0066ff',
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
            .bindPopup(`<b style="color:#0066ff;">${wp.title}</b><br>${wp.status}`);
    });

    trackingMap.fitBounds(trackingPolyline.getBounds(), { padding: [40, 40] });
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
   8. FLOATING CHAT WIDGET
   -------------------------------------------------------------------------- */
function toggleChat() {
    const chatBox = document.getElementById('chatBox');
    chatBox.classList.toggle('opacity-0');
    chatBox.classList.toggle('pointer-events-none');
    chatBox.classList.toggle('translate-y-6');
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
        botMsg.innerText = "Mabuhay! A SwiftFreight support specialist will assist you with your portal inquiry shortly.";
        chatBody.appendChild(botMsg);
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 700);
}