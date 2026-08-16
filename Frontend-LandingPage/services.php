<?php
$pageTitle  = 'Services';
$activePage = 'services';
include 'includes/header.php';
?>

<!-- ================= DASHBOARD BANNER ================= -->
<section class="dashboard-banner relative overflow-hidden text-white py-16 px-6">
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-300 text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
            <i class="fa-solid fa-truck-fast"></i> Services Dashboard
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">Our Complete Service Portfolio</h1>
        <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">From door-to-door couriers to full customs brokerage — one partner for your entire supply chain.</p>
        <nav class="mt-6 text-xs text-slate-400 flex items-center gap-1.5">
            <a href="index.php" class="hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-sky-300 font-semibold">Services</span>
        </nav>
    </div>
</section>

<!-- ================= KPI STAT CARDS ================= -->
<section class="relative z-20 -mt-10 pb-6">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0"><i class="fa-solid fa-layer-group text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">6</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Service Lines</div>
            </div>
        </div>
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-earth-asia text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">140+</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Countries Covered</div>
            </div>
        </div>
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-route text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">4</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Transport Modes</div>
            </div>
        </div>
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-headset text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">24/7</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Support Desk</div>
            </div>
        </div>
    </div>
</section>
<!-- ================= SERVICE CARDS ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto">
    <div class="text-center reveal mb-12">
        <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">WHAT WE DO</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">End-to-End Logistics, Under One Roof</h2>
        <p class="text-slate-500 text-sm mt-3 max-w-xl mx-auto">Six specialized service lines coordinated by a single, experienced team.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="service-icon w-12 h-12 mb-5 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center"><i class="fa-solid fa-truck-fast text-lg"></i></div>
            <h3 class="font-extrabold text-slate-900 text-base mb-2">Courier & Express Delivery</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">Rapid same-day and next-day door-to-door courier solutions across Metro Manila, Luzon, Visayas, and Mindanao.</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Same-day & next-day options</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Nationwide door-to-door coverage</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Real-time tracking available</li>
            </ul>
        </div>

        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="service-icon w-12 h-12 mb-5 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fa-solid fa-ship text-lg"></i></div>
            <h3 class="font-extrabold text-slate-900 text-base mb-2">Ocean Freight</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">Full container load (FCL) and less-than-container load (LCL) shipping to and from major global ports.</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> FCL & LCL consolidations</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Competitive global carrier rates</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Port-to-port & door-to-door</li>
            </ul>
        </div>

        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="service-icon w-12 h-12 mb-5 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-plane text-lg"></i></div>
            <h3 class="font-extrabold text-slate-900 text-base mb-2">Air Freight</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">Express and economy air cargo for time-critical shipments, with full visibility from pickup to delivery.</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Express & economy options</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Temperature-sensitive handling</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> End-to-end shipment updates</li>
            </ul>
        </div>
<div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="service-icon w-12 h-12 mb-5 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-truck text-lg"></i></div>
            <h3 class="font-extrabold text-slate-900 text-base mb-2">Land Freight & Trucking</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">Reliable overland haulage for full truckloads, partial loads, and oversized cargo across the archipelago.</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> FTL & LTL trucking</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Inter-island roll-on/roll-off</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Heavy & oversized cargo</li>
            </ul>
        </div>

        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="service-icon w-12 h-12 mb-5 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fa-solid fa-warehouse text-lg"></i></div>
            <h3 class="font-extrabold text-slate-900 text-base mb-2">Warehousing & Storage</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">Secure, monitored storage and smart inventory management for retail, e-commerce, and manufacturing partners.</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Short & long-term storage</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Pick, pack & fulfillment</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Inventory monitoring</li>
            </ul>
        </div>

        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="service-icon w-12 h-12 mb-5 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><i class="fa-solid fa-file-shield text-lg"></i></div>
            <h3 class="font-extrabold text-slate-900 text-base mb-2">Customs Brokerage</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">Professional import and export clearances, documentation, and compliance handled end-to-end.</p>
            <ul class="space-y-2 text-xs text-slate-600">
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Import & export clearances</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> Tariff classification & duties</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-500"></i> RA 10863 compliant filing</li>
            </ul>
        </div>
    </div>
</section>
<!-- ================= HOW IT WORKS ================= -->
<section class="py-20 px-6 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto">
        <div class="text-center reveal mb-12">
            <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">HOW IT WORKS</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">From Inquiry to Arrival in 4 Easy Steps</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-7 relative">
                <span class="absolute top-5 right-6 text-5xl font-black text-slate-100">01</span>
                <div class="w-11 h-11 mb-4 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-pen-to-square"></i></div>
                <h4 class="font-bold text-slate-900 text-sm mb-2">Submit an Inquiry</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Fill out the online form on our Forms dashboard or call 843-7484 for an instant quote.</p>
            </div>
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-7 relative">
                <span class="absolute top-5 right-6 text-5xl font-black text-slate-100">02</span>
                <div class="w-11 h-11 mb-4 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fa-solid fa-cubes"></i></div>
                <h4 class="font-bold text-slate-900 text-sm mb-2">We Pick Up Your Shipment</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Our team arranges pickup from your location, packed and documented correctly.</p>
            </div>
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-7 relative">
                <span class="absolute top-5 right-6 text-5xl font-black text-slate-100">03</span>
                <div class="w-11 h-11 mb-4 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-truck-fast"></i></div>
                <h4 class="font-bold text-slate-900 text-sm mb-2">Ship & Track Live</h4>
                <p class="text-xs text-slate-500 leading-relaxed">We move your cargo by land, air, or sea — follow every step on the live map.</p>
            </div>
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-7 relative">
                <span class="absolute top-5 right-6 text-5xl font-black text-slate-100">04</span>
                <div class="w-11 h-11 mb-4 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center"><i class="fa-solid fa-circle-check"></i></div>
                <h4 class="font-bold text-slate-900 text-sm mb-2">Delivery & Confirmation</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Your shipment arrives on time, confirmed with a delivery receipt and proof note.</p>
            </div>
        </div>
    </div>
</section>
<!-- ================= COVERAGE PANEL ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
    <div class="lg:col-span-7 reveal">
        <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">GLOBAL COVERAGE</span>
        <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-6">One Partner for the Philippines & the World</h3>
        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            Whether your cargo is crossing EDSA or crossing the Pacific, our network keeps it moving.
            We partner with trusted international carriers and local hubs so your shipments enjoy the
            same priority service in every corner of the globe.
        </p>
        <div class="flex items-center gap-3 bg-brand-navy text-white rounded-2xl p-5">
            <i class="fa-solid fa-earth-asia text-sky-400 text-3xl"></i>
            <div>
                <div class="font-black text-lg leading-none">140+</div>
                <div class="text-[11px] text-slate-400 mt-1">Countries & territories reachable from our Makati HQ</div>
            </div>
        </div>
    </div>
    <div class="lg:col-span-5 reveal">
        <div class="grid grid-cols-2 gap-4">
            <div class="dash-card bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-location-dot text-brand-blue text-xl mb-2"></i>
                <h4 class="text-xs font-bold text-slate-900">ASEAN</h4>
                <p class="text-[10px] text-slate-500 mt-1">SG, MY, TH, VN, ID & more</p>
            </div>
            <div class="dash-card bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-star-of-life text-brand-blue text-xl mb-2"></i>
                <h4 class="text-xs font-bold text-slate-900">EAST ASIA</h4>
                <p class="text-[10px] text-slate-500 mt-1">CN, JP, KR, HK & Taiwan</p>
            </div>
            <div class="dash-card bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-earth-europe text-brand-blue text-xl mb-2"></i>
                <h4 class="text-xs font-bold text-slate-900">EUROPE & AMERICAS</h4>
                <p class="text-[10px] text-slate-500 mt-1">US, CA, EU hubs</p>
            </div>
            <div class="dash-card bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-earth-oceania text-brand-blue text-xl mb-2"></i>
                <h4 class="text-xs font-bold text-slate-900">OCEANIA & ME</h4>
                <p class="text-[10px] text-slate-500 mt-1">AU, NZ, UAE, SA & more</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= CTA BAND ================= -->
<section class="py-16 px-6">
    <div class="max-w-7xl mx-auto dashboard-banner relative overflow-hidden rounded-3xl text-white p-10 sm:p-14 text-center">
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">Which Service Do You Need?</h2>
            <p class="text-slate-300 text-sm max-w-2xl mx-auto mb-8">Tell us about your shipment and get a tailored quote within minutes.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="forms.php" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Request a Quote
                </a>
                <button onclick="openPromoModal()" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-black text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-gift"></i> Claim New Client Promos
                </button>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>