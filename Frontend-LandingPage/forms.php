<?php
$pageTitle  = 'Forms';
$activePage = 'forms';
include 'includes/header.php';
?>

<!-- ================= DASHBOARD BANNER ================= -->
<section class="dashboard-banner relative overflow-hidden text-white py-16 px-6">
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-300 text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
            <i class="fa-solid fa-file-lines"></i> Forms Dashboard
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">Inquiry, Quote & Tracking Tools</h1>
        <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">Request a quote, estimate your rate, and follow your shipment â€” all in one place.</p>
        <nav class="mt-6 text-xs text-slate-400 flex items-center gap-1.5">
            <a href="index.php" class="hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-sky-300 font-semibold">Forms</span>
        </nav>
    </div>
</section>

<!-- ================= ONLINE INQUIRY FORM ================= -->
<section id="claim" class="py-20 px-6 bg-slate-950 text-white relative overflow-hidden">
    <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-brand-blue/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto relative z-10 bg-slate-900/80 border border-white/10 rounded-3xl p-8 lg:p-12 backdrop-blur-xl shadow-2xl grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

        <div class="lg:col-span-5">
            <div class="inline-flex items-center gap-2 bg-brand-blue/20 border border-brand-blue/30 text-sky-400 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
                <i class="fa-solid fa-pen-to-square"></i> ONLINE INQUIRY
            </div>
            <h3 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight mb-4">
                Send Us a Direct Message
            </h3>
            <p class="text-slate-400 text-sm leading-relaxed mb-8">
                Fill out the form and a logistics specialist will respond promptly â€” usually within one business day.
                Need it faster? Call us at <a href="tel:+6328437484" class="text-sky-400 font-bold hover:underline">843-7484</a>.
            </p>
            <div class="space-y-4">
                <div class="group bg-white/5 hover:bg-brand-blue/20 border border-white/10 hover:border-brand-blue/50 p-4 rounded-xl transition-all flex items-center gap-4">
                    <i class="fa-solid fa-phone text-sky-400"></i>
                    <div>
                        <h5 class="text-[10px] text-slate-400 font-semibold">Call Us</h5>
                        <p class="text-xs font-bold text-white group-hover:text-sky-300 transition-colors">(632) 843-7484</p>
                    </div>
                </div>
                <div class="group bg-white/5 hover:bg-brand-blue/20 border border-white/10 hover:border-brand-blue/50 p-4 rounded-xl transition-all flex items-center gap-4">
                    <i class="fa-solid fa-envelope text-sky-400"></i>
                    <div>
                        <h5 class="text-[10px] text-slate-400 font-semibold">Email Us</h5>
                        <p class="text-xs font-bold text-white group-hover:text-sky-300 transition-colors">cs@priority-ph.com</p>
                    </div>
                </div>
                <div class="group bg-white/5 hover:bg-brand-blue/20 border border-white/10 hover:border-brand-blue/50 p-4 rounded-xl transition-all flex items-center gap-4">
                    <i class="fa-solid fa-location-dot text-sky-400"></i>
                    <div>
                        <h5 class="text-[10px] text-slate-400 font-semibold">HQ Location</h5>
                        <p class="text-xs font-bold text-white group-hover:text-sky-300 transition-colors">Makati City, Philippines</p>
                    </div>
                </div>
            </div>
        </div>
<div class="lg:col-span-7">
            <form onsubmit="handleContactSubmit(event)" class="bg-slate-950/80 border border-white/10 p-6 sm:p-8 rounded-2xl shadow-2xl">
                <h4 class="text-xl font-extrabold text-white mb-1">Online Inquiry</h4>
                <p class="text-slate-400 text-xs mb-6">All fields marked * are required.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Full Name *</label>
                        <input type="text" id="contactName" required minlength="2" maxlength="100" placeholder="Juan Dela Cruz" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Email Address *</label>
                        <input type="email" id="contactEmail" required maxlength="150" placeholder="juan@company.ph" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Company Name</label>
                        <input type="text" id="contactCompany" maxlength="120" placeholder="Your Company Name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Phone Number</label>
                        <input type="tel" id="contactPhone" maxlength="20" pattern="[0-9+()\-\s]{7,20}" title="Enter 7â€“20 digits, spaces, + ( ) or - only (e.g. +63 917 000 0000)" placeholder="+63 (917) 000-0000" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-medium text-slate-300 mb-1">Shipment Details / Message *</label>
                    <textarea id="contactMessage" rows="4" required minlength="10" maxlength="2000" placeholder="Tell us about your cargo requirements, origin, destination, timeline, or any questions..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue"></textarea>
                </div>

                <button type="submit" id="contactSubmitBtn" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Send Inquiry Message
                </button>

                <div id="contactFormStatus" class="hidden mt-4 p-3 bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 rounded-xl text-center text-xs font-medium"></div>
            </form>
        </div>
    </div>
</section>
<!-- ================= RATE CALCULATOR + TRACKER ================= -->
<section id="calculator" class="py-20 px-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="reveal bg-white p-8 lg:p-10 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-brand-blue text-xs font-bold tracking-widest uppercase block mb-1">RATE CALCULATOR</span>
            <h3 class="text-2xl font-extrabold text-slate-900 mb-6">Get an Instant Estimate</h3>
            <form onsubmit="calculateQuote(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">From</label>
                    <input type="text" id="origin" placeholder="Manila" required maxlength="100" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Destination</label>
                    <input type="text" id="destination" placeholder="Los Angeles" required maxlength="100" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Weight (kg)</label>
                    <input type="number" id="weight" placeholder="Enter Weight" required min="0.1" max="100000" step="0.1" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue">
                </div>
                <div class="sm:col-span-2 flex flex-col sm:flex-row justify-between items-center gap-4 mt-2">
                    <button type="submit" class="w-full sm:w-auto bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-6 py-2.5 rounded-md transition-colors">
                        Calculate Rate
                    </button>
                    <a href="#claim" class="text-xs text-brand-blue font-semibold hover:underline">Request a Custom Quote &rarr;</a>
                </div>
            </form>
            <div id="calcResult" class="hidden mt-4 p-3 bg-sky-50 border border-sky-200 text-sky-800 rounded-lg text-center text-xs font-semibold"></div>
        </div>
<div class="reveal bg-brand-navy text-white p-8 lg:p-10 rounded-2xl border border-white/10 shadow-sm relative overflow-hidden">
            <div class="absolute top-1/2 right-0 -translate-y-1/2 w-64 h-64 bg-brand-blue/20 rounded-full blur-[90px] pointer-events-none"></div>
            <span class="text-sky-400 text-xs font-bold tracking-widest uppercase block mb-1 mt-4">SHIPMENT TRACKER</span>
            <h3 class="text-2xl font-extrabold text-white mb-6">Track a Shipment Here</h3>
            <p class="text-slate-400 text-sm mb-6 leading-relaxed">Enter your tracking number below to see the latest real-time position of your cargo.</p>
            <form onsubmit="trackShipment(); return false;" class="bg-white p-1.5 rounded-lg flex gap-2 shadow-lg mb-4">
                <input type="text" id="trackingInput" placeholder="Enter Tracking Number" required maxlength="50" pattern="[A-Za-z0-9\-]{4,30}" title="Tracking numbers use 4-30 letters, numbers, or dashes (e.g. PH-2024-001)" class="w-full px-4 py-2 text-slate-900 text-sm focus:outline-none rounded-md">
                <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-5 py-2.5 rounded-md whitespace-nowrap transition-colors">
                    Track Now
                </button>
            </form>
            <button onclick="fillDemoTracking()" class="text-xs font-semibold text-sky-400 hover:underline inline-flex items-center gap-1">
                View Demo Tracking &rarr;
            </button>
            <div class="mt-8 flex items-center gap-3 bg-white/5 border border-white/10 rounded-xl p-4">
                <div class="w-3 h-3 bg-cyan-400 rounded-full animate-pulse-glow"></div>
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Sample Shipment</span>
                    <strong class="text-white text-xs">PH-2024-001 (Manila &rarr; Los Angeles)</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FORMS CTA ================= -->
<section class="py-16 px-6">
    <div class="max-w-7xl mx-auto dashboard-banner relative overflow-hidden rounded-3xl text-white p-10 sm:p-14 text-center">
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">Refining Your Cargo Plan?</h2>
            <p class="text-slate-300 text-sm max-w-2xl mx-auto mb-8">Our specialists can fine-tune your estimate and design the best route for your shipment.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="tel:+6328437484" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-phone"></i> Call 843-7484
                </a>
                <button onclick="openPromoModal()" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-black text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-gift"></i> See Promos
                </button>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
