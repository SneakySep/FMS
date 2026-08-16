<?php
$pageTitle  = 'About';
$activePage = 'about';
include 'includes/header.php';
?>

<!-- ================= DASHBOARD BANNER ================= -->
<section class="dashboard-banner relative overflow-hidden text-white py-16 px-6">
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-300 text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
            <i class="fa-solid fa-building"></i> About Dashboard
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">About Priority Handling Logistics</h1>
        <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">Registered, licensed, and accredited — meet the team that has been moving Philippines business forward since 2005.</p>
        <nav class="mt-6 text-xs text-slate-400 flex items-center gap-1.5">
            <a href="index.php" class="hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-sky-300 font-semibold">About</span>
        </nav>
    </div>
</section>

<!-- ================= KPI STAT CARDS ================= -->
<section class="relative z-20 -mt-10 pb-6">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-scroll text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">2005</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">SEC Registered</div>
            </div>
        </div>
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-microchip text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">DOST</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">ICTO Certified</div>
            </div>
        </div>
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center shrink-0"><i class="fa-solid fa-building-columns text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">PEZA</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Accredited</div>
            </div>
        </div>
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 p-5 shadow-lg flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-award text-lg"></i></div>
            <div>
                <div class="text-2xl font-black text-slate-900 leading-none">21+</div>
                <div class="text-[11px] text-slate-500 font-semibold mt-1">Years of Excellence</div>
            </div>
        </div>
    </div>
</section>
<!-- ================= COMPANY OVERVIEW ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
    <div class="lg:col-span-7 reveal">
        <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">OUR COMPANY</span>
        <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-6">A Trusted Name in Philippine Logistics</h3>
        <p class="text-sm text-slate-500 leading-relaxed mb-4">
            <strong class="text-slate-900">Priority Handling Logistics, Inc.</strong> was incorporated on February 14, 2005,
            and has since grown into a full-service logistics provider serving clients across Metro Manila, the provinces,
            and more than 140 countries and territories worldwide.
        </p>
        <p class="text-sm text-slate-500 leading-relaxed mb-6">
            Operating from our headquarters at <strong class="text-slate-900">1618-B Copernico Street, Barangay San Isidro,
            Makati City</strong>, we combine modern processes with a deeply experienced team to deliver dependable courier,
            freight forwarding, warehousing, and customs brokerage solutions for businesses of every size.
        </p>
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
            <h4 class="font-bold text-slate-900 text-sm mb-3 flex items-center gap-2"><i class="fa-solid fa-certificate text-brand-blue"></i> Government Credentials</h4>
            <ul class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <li class="flex items-start gap-2 text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i> SEC Registered — Cert. No. CS200502125</li>
                <li class="flex items-start gap-2 text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i> DOST-ICTO Certified</li>
                <li class="flex items-start gap-2 text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5"></i> PEZA-Accredited</li>
            </ul>
        </div>
    </div>

    <div class="lg:col-span-5 reveal">
        <div class="relative">
            <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=800&q=80" alt="Priority Handling Office Team" class="w-full rounded-2xl shadow-2xl object-cover h-[400px]">
            <div class="absolute -bottom-6 -right-4 bg-white rounded-2xl shadow-2xl border border-slate-200 px-5 py-4">
                <div class="text-2xl font-black text-brand-blue leading-none">21+<span class="text-amber-500"> yrs</span></div>
                <div class="text-[10px] text-slate-500 font-semibold mt-1">Serving the Philippines</div>
            </div>
        </div>
    </div>
</section>

<!-- ================= MISSION / VISION / VALUES ================= -->
<section class="py-20 px-6 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto">
        <div class="text-center reveal mb-12">
            <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">WHY WE EXIST</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Mission, Vision & Values</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-5 bg-brand-blue/10 text-brand-blue rounded-2xl flex items-center justify-center"><i class="fa-solid fa-rocket text-xl"></i></div>
                <h3 class="font-extrabold text-slate-900 mb-3">Our Mission</h3>
                <p class="text-sm text-slate-500 leading-relaxed">To provide quality, dependable, and cost-effective courier and freight forwarding services that exceed client expectations on every single shipment.</p>
            </div>
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-5 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center"><i class="fa-solid fa-eye text-xl"></i></div>
                <h3 class="font-extrabold text-slate-900 mb-3">Our Vision</h3>
                <p class="text-sm text-slate-500 leading-relaxed">To be the Philippines' most trusted and preferred logistics partner for domestic and international shipping by 2030.</p>
            </div>
            <div class="dash-card reveal bg-white rounded-2xl border border-slate-200 p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-5 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center"><i class="fa-solid fa-heart text-xl"></i></div>
                <h3 class="font-extrabold text-slate-900 mb-3">Our Values</h3>
                <p class="text-sm text-slate-500 leading-relaxed"><strong class="text-slate-700">PRIORITY:</strong> Passion, Reliability, Integrity, Ownership, Respect, Initiative, Teamwork, and You-first service.</p>
            </div>
        </div>
    </div>
</section>
<!-- ================= MILESTONES TIMELINE ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto">
    <div class="text-center reveal mb-12">
        <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-2">MILESTONES</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Our Journey Through the Years</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-6 border-t-4 border-t-emerald-500">
            <div class="text-3xl font-black text-emerald-500 mb-2">2005</div>
            <h4 class="font-bold text-slate-900 text-sm mb-1">Incorporation</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Founded in Makati City and registered with SEC (Cert. No. CS200502125).</p>
        </div>
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-6 border-t-4 border-t-sky-500">
            <div class="text-3xl font-black text-sky-500 mb-2">2010</div>
            <h4 class="font-bold text-slate-900 text-sm mb-1">Nationwide Expansion</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Extended domestic courier routes across all major Philippine provinces.</p>
        </div>
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-6 border-t-4 border-t-brand-blue">
            <div class="text-3xl font-black text-brand-blue mb-2">2016</div>
            <h4 class="font-bold text-slate-900 text-sm mb-1">Global Reach</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Grew international partnerships to cover 140+ countries and territories.</p>
        </div>
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-6 border-t-4 border-t-amber-500">
            <div class="text-3xl font-black text-amber-500 mb-2">TODAY</div>
            <h4 class="font-bold text-slate-900 text-sm mb-1">Digital-First Service</h4>
            <p class="text-xs text-slate-500 leading-relaxed">Live tracking, instant rate quotes, and 24/7 support on every dashboard.</p>
        </div>
    </div>
</section>

<!-- ================= CTA BAND ================= -->
<section class="py-16 px-6">
    <div class="max-w-7xl mx-auto dashboard-banner relative overflow-hidden rounded-3xl text-white p-10 sm:p-14 text-center">
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">Ready to Ship With a Team You Can Trust?</h2>
            <p class="text-slate-300 text-sm max-w-2xl mx-auto mb-8">Get a free quote in minutes, or explore the rewards waiting for new clients.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="forms.php" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Get a Free Quote
                </a>
                <button onclick="openPromoModal()" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-black text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-gift"></i> See New Client Promos
                </button>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>