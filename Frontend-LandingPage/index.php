<?php
$pageTitle  = 'Home';
$activePage = 'home';
include 'includes/header.php';
?>

<!-- ================= HERO SLIDESHOW (HOME) ================= -->
<section id="home" class="relative h-[88vh] min-h-[560px] overflow-hidden">
    <!-- Slide 1: Courier & Freight Forwarding -->
    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=1600&q=80" alt="Courier & Freight Forwarding" class="w-full h-full object-cover">
    </div>
    <!-- Slide 2: Smart Warehousing -->
    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1600&q=80" alt="Smart Warehousing" class="w-full h-full object-cover">
    </div>
    <!-- Slide 3: International Shipping -->
    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1559297434-fae8a1916a79?auto=format&fit=crop&w=1600&q=80" alt="International Shipping" class="w-full h-full object-cover">
    </div>
    <!-- Slide 4: Express Air Freight -->
    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1600&q=80" alt="Express Air Freight" class="w-full h-full object-cover">
    </div>

    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/65 to-slate-950/30 z-10"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30 z-10"></div>

    <div class="relative z-20 max-w-7xl mx-auto px-6 w-full py-24">
        <div class="max-w-2xl reveal">
            <div class="inline-flex items-center gap-2 bg-brand-blue/90 backdrop-blur-md text-white text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-6 border border-white/20 shadow-lg">
                <i id="heroBadgeIcon" class="fa-solid fa-truck-fast"></i>
                <span id="heroBadgeText">Courier & Freight Forwarding</span>
            </div>

            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight mb-5">
                Delivering Nationwide.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-blue-500">Shipping Worldwide.</span>
            </h2>

            <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-xl mb-8">
                Welcome to Priority Handling Logistics Inc. — your trusted partner for domestic and
                international courier and freight forwarding solutions since 2005. Track, quote, and book
                everything right from this dashboard.
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="forms.php" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:-translate-y-0.5">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Get a Free Quote
                </a>
                <a href="#tracking" class="border border-white/30 hover:border-white/70 hover:bg-white/10 backdrop-blur-md text-white font-semibold text-sm px-7 py-3.5 rounded-xl transition-all">
                    <i class="fa-solid fa-satellite-dish mr-2"></i>Live Tracking
                </a>
                <button onclick="openPromoModal()" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-black text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-amber-500/30 hover:-translate-y-0.5">
                    <i class="fa-solid fa-gift mr-2"></i>View Promos
                </button>
            </div>
        </div>
    </div>

    <!-- Hero Controls -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-4 bg-black/40 backdrop-blur-md rounded-full px-5 py-3 border border-white/15">
        <button onclick="prevHeroSlide()" class="text-white/70 hover:text-white transition-colors">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <div class="flex items-center gap-2">
            <button onclick="setHeroSlide(0)" class="hero-dot w-2 h-2 rounded-full bg-white transition-all duration-300"></button>
            <button onclick="setHeroSlide(1)" class="hero-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300"></button>
            <button onclick="setHeroSlide(2)" class="hero-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300"></button>
            <button onclick="setHeroSlide(3)" class="hero-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300"></button>
        </div>
        <button onclick="nextHeroSlide()" class="text-white/70 hover:text-white transition-colors">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</section>
<!-- ================= KPI STAT CARDS ================= -->
<section class="relative -mt-14 pb-6 z-30">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="kpi-card reveal bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-calendar-check text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">21+</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Years of Service</div>
            </div>
        </div>
        <div class="kpi-card reveal bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-earth-asia text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">140+</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Countries Covered</div>
            </div>
        </div>
        <div class="kpi-card reveal bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-boxes-stacked text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">500K+</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Deliveries Completed</div>
            </div>
        </div>
        <div class="kpi-card reveal bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0"><i class="fa-solid fa-headset text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">24/7</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Customer Support</div>
            </div>
        </div>
    </div>
</section>
<!-- ================= WELCOME + ACCREDITATION ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
    <div class="lg:col-span-7 reveal">
        <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">WELCOME TO PRIORITY HANDLING LOGISTICS, INC.</span>
        <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-6">Over a Decade of Excellence in Logistics</h3>
        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            Since 2005, we have been committed to providing quality and dependable courier and freight forwarding
            services to our valued clients from the Philippines and around the world. Our streamlined processes are
            supported by a dedicated, experienced team that makes sure every shipment — big or small — arrives safe, fast,
            and on schedule.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="dash-card bg-white border border-slate-200 rounded-xl p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center"><i class="fa-solid fa-scroll"></i></div>
                <h4 class="text-xs font-bold text-slate-900">SEC Registered</h4>
                <p class="text-[10px] text-slate-500 mt-1">CS200502125 • Feb 14, 2005</p>
            </div>
            <div class="dash-card bg-white border border-slate-200 rounded-xl p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 bg-sky-100 text-sky-600 rounded-lg flex items-center justify-center"><i class="fa-solid fa-microchip"></i></div>
                <h4 class="text-xs font-bold text-slate-900">DOST Certified</h4>
                <p class="text-[10px] text-slate-500 mt-1">ICTO Certified</p>
            </div>
            <div class="dash-card bg-white border border-slate-200 rounded-xl p-5 text-center">
                <div class="w-10 h-10 mx-auto mb-2 bg-brand-blue/10 text-brand-blue rounded-lg flex items-center justify-center"><i class="fa-solid fa-building-columns"></i></div>
                <h4 class="text-xs font-bold text-slate-900">PEZA Accredited</h4>
                <p class="text-[10px] text-slate-500 mt-1">Serves PEZA enterprises</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-8">
            <a href="about.php" class="text-brand-blue font-semibold text-sm hover:underline inline-flex items-center gap-1.5">Learn More About Us <i class="fa-solid fa-arrow-right text-xs"></i></a>
            <a href="why-us.php" class="text-slate-500 font-semibold text-sm hover:text-brand-blue inline-flex items-center gap-1.5">Why Choose Priority? <i class="fa-solid fa-arrow-right text-xs"></i></a>
        </div>
    </div>

    <div class="lg:col-span-5 reveal">
        <div class="relative">
            <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80" alt="Priority Logistics Team" class="w-full rounded-2xl shadow-2xl object-cover h-[380px]">
            <div class="absolute -bottom-6 -left-6 bg-brand-navy text-white p-4 rounded-2xl shadow-2xl border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center"><i class="fa-solid fa-star text-lg"></i></div>
                    <div>
                        <div class="text-lg font-black leading-none">98.7%<span class="text-sky-400 text-sm"> On-Time</span></div>
                        <div class="text-[10px] text-slate-400 mt-1">On-Time Delivery Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================= PARTNERS STRIP ================= -->
<section class="py-12 border-y border-slate-200 bg-slate-50 px-6">
    <div class="max-w-7xl mx-auto text-center">
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-8">Trusted by leading enterprises across the Philippines</p>
        <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-6 text-slate-400 text-lg font-extrabold tracking-widest opacity-60">
            <span class="flex items-center gap-2"><i class="fa-solid fa-industry text-brand-blue"></i> NORTHPORT</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-gears text-brand-blue"></i> LIGHTHOUSE</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-tower-broadcast text-brand-blue"></i> MANILATRADE</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-bolt text-brand-blue"></i> VOLTAGE</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-anchor text-brand-blue"></i> HARBORLINK</span>
        </div>
    </div>
</section>

<!-- ================= LIVE TRACKING DASHBOARD ================= -->
<section id="tracking" class="bg-brand-navy text-white py-20 px-6 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-brand-blue/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-5 reveal">
            <div class="inline-flex items-center gap-2 bg-brand-blue/30 border border-brand-blue/40 text-sky-300 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
                <i class="fa-solid fa-satellite-dish"></i> LIVE TRACKING
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-4 tracking-tight">Track Your Shipment In Real Time</h2>
            <p class="text-slate-400 text-sm mb-8 leading-relaxed">Enter your tracking number to get the latest updates on your shipment.</p>

            <div class="bg-white p-1.5 rounded-lg flex gap-2 shadow-lg">
                <input type="text" id="trackingInput" placeholder="Enter Tracking Number" class="w-full px-4 py-2 text-slate-900 text-sm focus:outline-none rounded-md">
                <button onclick="trackShipment()" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-5 py-2.5 rounded-md whitespace-nowrap transition-colors">
                    Track Now
                </button>
            </div>
            <button onclick="fillDemoTracking()" class="mt-4 text-xs font-semibold text-sky-400 hover:underline inline-flex items-center gap-1">
                View Demo Tracking &rarr;
            </button>
        </div>

        <div class="lg:col-span-7 reveal relative h-[420px] rounded-2xl border border-white/10 overflow-hidden shadow-2xl bg-brand-navycard z-0">
            <div id="map" class="w-full h-full z-0"></div>
            <div class="absolute top-4 left-4 bg-brand-navycard/90 backdrop-blur-md p-3 rounded-xl border border-white/15 text-xs text-white z-[400] shadow-xl flex items-center gap-3">
                <div class="w-3 h-3 bg-cyan-400 rounded-full animate-pulse-glow"></div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Active Route</span>
                    <strong class="text-white text-xs">PH-2024-001 (Manila &rarr; Los Angeles)</strong>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ================= DASHBOARD QUICK LINKS ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto">
    <div class="text-center reveal mb-12">
        <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">DASHBOARD</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Explore Every Corner of Priority</h2>
        <p class="text-slate-500 text-sm mt-3 max-w-xl mx-auto">Jump straight into the information and tools you need — each portal below is its own dedicated dashboard.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <a href="about.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-building"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">About Us</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Our story, credentials, mission, and milestones since 2005.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <a href="services.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-truck-fast"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">Services</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Courier, air, ocean, trucking, warehousing, and customs.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <a href="forms.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-file-lines"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">Forms & Tools</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Online inquiry form, rate calculator, and shipment tracker.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <a href="careers.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-user-tie"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">Careers</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Join a dynamic team and grow with a decade-old company.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
<a href="faqs.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-circle-question"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">FAQs</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Quick answers on shipping, tracking, billing, and promos.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <a href="why-us.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-medal"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">Why Us?</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Credentials, reasons, and testimonials from happy clients.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <a href="contact.php" class="dash-card reveal group bg-white border border-slate-200 rounded-2xl p-6 hover:border-brand-blue/40 block">
            <div class="w-11 h-11 mb-4 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-map-location-dot"></i></div>
            <h3 class="font-bold text-slate-900 text-sm mb-1">Contact Us</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-3">Makati HQ details, map, and direct message form.</p>
            <span class="text-brand-blue text-xs font-bold group-hover:gap-2 inline-flex items-center gap-1 transition-all">Open Dashboard <i class="fa-solid fa-arrow-right"></i></span>
        </a>
        <button onclick="openPromoModal()" class="dash-card reveal group bg-gradient-to-tr from-brand-darkblue to-brand-blue text-white rounded-2xl p-6 text-left hover:brightness-110 cursor-pointer">
            <div class="w-11 h-11 mb-4 rounded-xl bg-white/15 text-amber-300 flex items-center justify-center group-hover:scale-110 transition-transform"><i class="fa-solid fa-gift"></i></div>
            <h3 class="font-bold text-white text-sm mb-1">Promos</h3>
            <p class="text-xs text-sky-200 leading-relaxed mb-3">FREE gifts for new clients — check the hot offers right now!</p>
            <span class="text-amber-300 text-xs font-black group-hover:gap-2 inline-flex items-center gap-1 transition-all">See Offers <i class="fa-solid fa-fire"></i></span>
        </button>
    </div>
</section>

<?php include 'includes/footer.php'; ?>