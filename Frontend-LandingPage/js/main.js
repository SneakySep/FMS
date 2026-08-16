/* ==========================================================================
   PRIORITY HANDLING LOGISTICS JAVASCRIPT LOGIC
   Multi-page dashboard frontend (Home | About | Services | Forms | FAQs |
   Why Us | Careers | Contact) + Promos pop-up ad.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. LEAFLET MAP INTEGRATION (Home + Contact dashboards)
   -------------------------------------------------------------------------- */
let leafletMap;
let routePolyline;

const waypoints = [
    { pos: [14.5995, 120.9842], title: "Manila, PH", status: "Origin", date: "May 24, 2024 • 08:30", color: "#38bdf8" },
    { pos: [1.3521, 103.8198], title: "Singapore, SG", status: "In Transit", date: "May 28, 2024 • 14:20", color: "#f59e0b" },
    { pos: [31.2304, 121.4737], title: "Shanghai, CN", status: "Customs Clearance", date: "May 30, 2024 • 11:00", color: "#a855f7" },
    { pos: [34.0522, -118.2437], title: "Los Angeles, USA", status: "Estimated Arrival", date: "June 01, 2024 • 07:00", color: "#10b981" }
];

function initLeafletMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement || typeof L === 'undefined') return;

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
        color: '#1D2E6A',
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
   2. HERO SLIDESHOW / CAROUSEL (Home dashboard)
   -------------------------------------------------------------------------- */
let currentHeroSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots = document.querySelectorAll('.hero-dot');
let heroTimer;

const slideMetadata = [
    { name: 'Courier & Freight Forwarding', icon: 'fa-truck-fast' },
    { name: 'Smart Warehousing', icon: 'fa-warehouse' },
    { name: 'International Shipping', icon: 'fa-ship' },
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

if (slides.length) startHeroTimer();
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
   4. PHILIPPINE LEGAL MODALS
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

/* --------------------------------------------------------------------------
   5. PROMOS POP-UP AD (modal + floating badge + nav triggers)
   -------------------------------------------------------------------------- */
function openPromoModal() {
    const modal = document.getElementById('promoModal');
    if (!modal) return;
    modal.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
}

function closePromoModal() {
    const modal = document.getElementById('promoModal');
    if (!modal) return;
    modal.classList.remove('modal-open');
    document.body.style.overflow = '';
}

function initPromoModal() {
    const modal = document.getElementById('promoModal');
    if (!modal) return;

    /* Close when the dark backdrop is clicked */
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closePromoModal();
    });

    /* Any [data-promo-trigger] element opens the ad */
    document.querySelectorAll('[data-promo-trigger]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openPromoModal();
        });
    });
}

if (document.getElementById('promoModal')) initPromoModal();

/* --------------------------------------------------------------------------
   6. MOBILE MENU
   -------------------------------------------------------------------------- */
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const icon = document.getElementById('mobileMenuIcon');
    if (!menu) return;
    menu.classList.toggle('hidden');
    if (icon) {
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-xmark');
    }
}

/* Global Escape closes everything */
window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (document.getElementById('mobileMenu') && !document.getElementById('mobileMenu').classList.contains('hidden')) {
            toggleMobileMenu();
        }
        closePrivacyModal();
        closeTermsModal();
        closePromoModal();
    }
});
/* --------------------------------------------------------------------------
   7. CONTACT FORM SUBMISSION (WITH COMPANY NAME)
   -------------------------------------------------------------------------- */
async function handleContactSubmit(e) {
    e.preventDefault();

    const btn = document.getElementById('contactSubmitBtn');
    const status = document.getElementById('contactFormStatus');
    if (!btn || !status) return;

    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> Sending Message...`;

    const payload = {
        name: document.getElementById('contactName').value,
        email: document.getElementById('contactEmail').value,
        company: document.getElementById('contactCompany').value,
        phone: document.getElementById('contactPhone').value,
        message: document.getElementById('contactMessage').value
    };

    /* ----------------------------------------------------------------------
       BACKEND CONTACT FORM INTEGRATION POINT:
       In production, post to your Express/Node.js backend endpoint:
       fetch('/api/v1/contact/ph', { method: 'POST', body: JSON.stringify(payload) })
       ---------------------------------------------------------------------- */

    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Send Inquiry Message`;

        status.classList.remove('hidden');
        status.innerText = "✓ Maraming Salamat! Your message has been received. A Priority Handling Logistics specialist will contact you shortly.";

        e.target.reset();

        setTimeout(() => {
            status.classList.add('hidden');
        }, 6000);
    }, 1200);
}

/* --------------------------------------------------------------------------
   8. LIVE TRACKING SEARCH & DEMO
   -------------------------------------------------------------------------- */
function fillDemoTracking() {
    const input = document.getElementById('trackingInput');
    if (!input) return;
    input.value = "PH-2024-001";
    trackShipment();
}

async function trackShipment() {
    const input = document.getElementById('trackingInput');
    if (!input) {
        alert("Live tracking is available on the Home or Forms dashboard.");
        return;
    }
    const trackingNum = input.value.trim();
    if (!trackingNum) {
        alert("Please enter a tracking number.");
        return;
    }

    // Belt-and-suspenders validation (HTML pattern already enforces this on submit).
    if (!/^[A-Za-z0-9\-]{4,30}$/.test(trackingNum)) {
        alert("Tracking numbers must be 4–30 characters using letters, numbers, or dashes (e.g. PH-2024-001).");
        return;
    }

    if (leafletMap && routePolyline) {
        leafletMap.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
    }

    alert(`Fetching realtime position for tracking ID: ${trackingNum}\nCurrent Status: In Transit (Customs Clearance Manila, PH)`);
}
/* --------------------------------------------------------------------------
   9. RATE CALCULATOR LOGIC
   -------------------------------------------------------------------------- */
async function calculateQuote(e) {
    e.preventDefault();

    const origin = document.getElementById('origin');
    const destination = document.getElementById('destination');
    const weightEl = document.getElementById('weight');
    const resultDiv = document.getElementById('calcResult');
    if (!origin || !destination || !weightEl || !resultDiv) return;

    const originVal = origin.value.trim();
    const destVal = destination.value.trim();
    const weight = parseFloat(weightEl.value);

    // Guard against invalid / non-positive / out-of-range weight and empty fields.
    if (!originVal || !destVal || !isFinite(weight) || weight <= 0) {
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'mt-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-center text-xs font-semibold';
        resultDiv.textContent = 'Please enter a valid origin, destination, and a positive weight (kg) to estimate your rate.';
        return;
    }

    const multiplier = 2.2;
    const estimatedCost = (weight * multiplier + 200).toFixed(2);

    // Build the result with textContent / createElement to avoid HTML injection from user input.
    resultDiv.classList.remove('hidden');
    resultDiv.className = 'mt-4 p-3 bg-sky-50 border border-sky-200 text-sky-800 rounded-lg text-center text-xs font-semibold';
    resultDiv.replaceChildren(
        document.createTextNode('Estimated Quote: '),
        Object.assign(document.createElement('strong'), { textContent: originVal }),
        document.createTextNode(' to '),
        Object.assign(document.createElement('strong'), { textContent: destVal }),
        document.createTextNode(' = '),
        Object.assign(document.createElement('span'), { className: 'text-brand-blue text-sm', textContent: '$' + estimatedCost + ' USD' })
    );
}

/* --------------------------------------------------------------------------
   10. FLOATING CHAT WIDGET (with notification sound)
   -------------------------------------------------------------------------- */
// Default notification sound played whenever the chat support widget is used
const DEFAULT_NOTIFICATION_SOUND = 'notification-1.mp3';

function getNotificationSound() {
    return DEFAULT_NOTIFICATION_SOUND;
}

function playNotificationSound() {
    try {
        const audio = new Audio('audio/' + getNotificationSound());
        audio.volume = 0.6;
        audio.play().catch(() => {});
    } catch (e) {
        console.warn('[Notification Sound] Unable to play audio:', e);
    }
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
    if (!input) return;
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
        playNotificationSound();
    }, 700);
}

function getBotReply(input) {
    const low = input.toLowerCase();
    if (low.includes('track') || low.includes('status')) {
        return "You can view real-time updates on our live map by typing your tracking code above.";
    } else if (low.includes('price') || low.includes('quote')) {
        return "Please check our Rate Calculator on the Forms dashboard for an instant estimate.";
    } else if (low.includes('promo') || low.includes('free') || low.includes('gift')) {
        return "Great news! New clients can claim FREE gifts — click the PROMO button on any page to see the details.";
    } else {
        return "Maraming salamat! A Priority Handling Logistics specialist will assist you shortly.";
    }
}

/* --------------------------------------------------------------------------
   11. UTILITIES
   -------------------------------------------------------------------------- */
function subscribeNewsletter(e) {
    e.preventDefault();
    alert("Thank you for subscribing to Priority Handling Logistics insights!");
}

function scrollToCalc() {
    const calc = document.getElementById('calculator');
    if (calc) calc.scrollIntoView({ behavior: 'smooth' });
}