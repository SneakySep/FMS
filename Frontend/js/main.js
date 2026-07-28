/* ==========================================================================
   SWIFTFREIGHT JAVASCRIPT LOGIC
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. LEAFLET MAP INTEGRATION
   -------------------------------------------------------------------------- */
let leafletMap;
let routePolyline;

const waypoints = [
    { pos: [31.2304, 121.4737], title: "Shanghai, CN", status: "Departed", date: "May 24, 2024 • 08:30", color: "#38bdf8" },
    { pos: [1.3521, 103.8198], title: "Singapore, SG", status: "In Transit", date: "May 28, 2024 • 14:20", color: "#f59e0b" },
    { pos: [14.5995, 120.9842], title: "Manila, PH", status: "Customs Clearance", date: "May 30, 2024 • 11:00", color: "#a855f7" },
    { pos: [34.0522, -118.2437], title: "Los Angeles, USA", status: "Estimated Arrival", date: "June 01, 2024 • 07:00", color: "#10b981" }
];

function initLeafletMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    leafletMap = L.map('map', {
        center: [20, 150],
        zoom: 2,
        zoomControl: true,
        attributionControl: false
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd'
    }).addTo(leafletMap);

    const latLngs = waypoints.map(w => w.pos);
    routePolyline = L.polyline(latLngs, {
        color: '#0066ff',
        weight: 3.5,
        opacity: 0.9,
        dashArray: '8, 8'
    }).addTo(leafletMap);

    waypoints.forEach(wp => {
        const customIcon = L.divIcon({
            className: 'custom-pin',
            html: `<div style="background-color: ${wp.color}; width: 14px; height: 14px; border-radius: 50%; border: 2.5px solid #ffffff; box-shadow: 0 0 10px ${wp.color};"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        const popupContent = `
            <div style="font-family: Inter, sans-serif; padding: 2px;">
                <span style="color:${wp.color}; font-size:10px; font-weight:bold; text-transform:uppercase;">● ${wp.status}</span>
                <h4 style="font-size:12px; font-weight:800; margin:2px 0; color:#0f172a;">${wp.title}</h4>
                <span style="font-size:10px; color:#64748b;">${wp.date}</span>
            </div>
        `;

        L.marker(wp.pos, { icon: customIcon })
            .addTo(leafletMap)
            .bindPopup(popupContent);
    });
}

document.addEventListener('DOMContentLoaded', initLeafletMap);

/* --------------------------------------------------------------------------
   2. HERO SLIDESHOW / CAROUSEL LOGIC
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
            dot.classList.remove('bg-white/50', 'w-2');
            dot.classList.add('bg-white', 'w-6');
        } else {
            dot.classList.remove('bg-white', 'w-6');
            dot.classList.add('bg-white/50', 'w-2');
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
    heroTimer = setInterval(() => { nextHeroSlide(); }, 3800);
}
function stopHeroTimer() { clearInterval(heroTimer); }

startHeroTimer();

/* --------------------------------------------------------------------------
   3. SCROLL REVEAL ANIMATIONS
   -------------------------------------------------------------------------- */
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('active');
    });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

/* --------------------------------------------------------------------------
   4. PHILIPPINE LEGAL MODALS (PRIVACY POLICY & TERMS)
   -------------------------------------------------------------------------- */
function openPrivacyModal() {
    const modal = document.getElementById('privacyModal');
    if (modal) modal.classList.add('modal-open');
}

function closePrivacyModal() {
    const modal = document.getElementById('privacyModal');
    if (modal) modal.classList.remove('modal-open');
}

function openTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.add('modal-open');
}

function closeTermsModal() {
    const modal = document.getElementById('termsModal');
    if (modal) modal.classList.remove('modal-open');
}

// Close modals when pressing ESC or clicking backdrop
window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closePrivacyModal();
        closeTermsModal();
    }
});

/* --------------------------------------------------------------------------
   5. CONTACT FORM SUBMISSION LOGIC
   -------------------------------------------------------------------------- */
async function handleContactSubmit(e) {
    e.preventDefault();

    const btn = document.getElementById('contactSubmitBtn');
    const status = document.getElementById('contactFormStatus');

    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Sending Message...`;

    const payload = {
        name: document.getElementById('contactName').value,
        email: document.getElementById('contactEmail').value,
        phone: document.getElementById('contactPhone').value,
        service: document.getElementById('contactService').value,
        message: document.getElementById('contactMessage').value
    };

    /* ----------------------------------------------------------------------
       BACKEND API INTEGRATION COMMENT:
       In production, post to your Philippine Express/Node.js API endpoint:
       fetch('/api/v1/contact/ph', { method: 'POST', body: JSON.stringify(payload) })
       ---------------------------------------------------------------------- */

    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Send Inquiry Message`;

        status.classList.remove('hidden');
        status.innerText = "✓ Maraming Salamat! Your message has been received. A SwiftFreight specialist will contact you shortly.";

        e.target.reset();

        setTimeout(() => {
            status.classList.add('hidden');
        }, 6000);
    }, 1200);
}

/* --------------------------------------------------------------------------
   6. LIVE TRACKING SEARCH & DEMO
   -------------------------------------------------------------------------- */
function fillDemoTracking() {
    document.getElementById('trackingInput').value = "SF-889420-CN";
    trackShipment();
}

async function trackShipment() {
    const trackingNum = document.getElementById('trackingInput').value.trim();
    if (!trackingNum) {
        alert("Please enter a tracking number.");
        return;
    }

    if (leafletMap && routePolyline) {
        leafletMap.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
    }

    alert(`Fetching realtime position for tracking ID: ${trackingNum}\nCurrent Status: In Transit (Customs Clearance Manila, PH)`);
}

/* --------------------------------------------------------------------------
   7. RATE CALCULATOR LOGIC
   -------------------------------------------------------------------------- */
async function calculateQuote(e) {
    e.preventDefault();

    const origin = document.getElementById('origin').value;
    const destination = document.getElementById('destination').value;
    const weight = parseFloat(document.getElementById('weight').value);
    const shipmentType = document.getElementById('shipmentType').value;

    let multiplier = shipmentType === 'air' ? 4.8 : (shipmentType === 'land' ? 1.3 : 2.2);
    let estimatedCost = (weight * multiplier + 200).toFixed(2);

    const resultDiv = document.getElementById('calcResult');
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = `Estimated Quote: <strong>${origin}</strong> to <strong>${destination}</strong> = <span class="text-brand-blue text-sm">$${estimatedCost} USD</span>`;
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
        botMsg.innerText = getBotReply(text);
        chatBody.appendChild(botMsg);
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 700);
}

function getBotReply(input) {
    const low = input.toLowerCase();
    if (low.includes('track') || low.includes('status')) {
        return "You can view real-time updates on our live map by typing your tracking code above.";
    } else if (low.includes('price') || low.includes('quote')) {
        return "Please check our Rate Calculator section above for an instant estimate.";
    } else {
        return "Maraming salamat! A SwiftFreight Philippine customer specialist will assist you shortly.";
    }
}

/* --------------------------------------------------------------------------
   9. UTILITIES
   -------------------------------------------------------------------------- */
function subscribeNewsletter(e) {
    e.preventDefault();
    alert("Thank you for subscribing to SwiftFreight insights!");
}

function scrollToCalc() {
    document.getElementById('calculator').scrollIntoView({ behavior: 'smooth' });
}