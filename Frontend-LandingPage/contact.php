<?php
$pageTitle  = 'Contact Us';
$activePage = 'contact';
include 'includes/header.php';
?>

<!-- ================= DASHBOARD BANNER ================= -->
<section class="dashboard-banner relative overflow-hidden text-white py-16 px-6">
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-300 text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
            <i class="fa-solid fa-map-location-dot"></i> Contact Dashboard
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">Let's Move Your Business Forward</h1>
        <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">Have questions or need a custom logistics solution? Our dedicated team is available around the clock to support your supply chain needs.</p>
        <nav class="mt-6 text-xs text-slate-400 flex items-center gap-1.5">
            <a href="index.php" class="hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-sky-300 font-semibold">Contact Us</span>
        </nav>
    </div>
</section>

<!-- ================= CONTACT INFO CARDS ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="w-12 h-12 mb-4 rounded-xl bg-brand-blue/10 text-brand-blue flex items-center justify-center"><i class="fa-solid fa-location-dot text-xl"></i></div>
            <h4 class="font-bold text-slate-900 text-sm mb-2">Head Office</h4>
            <p class="text-xs text-slate-500 leading-relaxed">1618-B Copernico Street,<br>Barangay San Isidro,<br>Makati City, Philippines 1234</p>
        </div>
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="w-12 h-12 mb-4 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fa-solid fa-phone-volume text-xl"></i></div>
            <h4 class="font-bold text-slate-900 text-sm mb-2">Landline (24/7)</h4>
            <p class="text-xs text-slate-500 leading-relaxed">(632) 843-7484<br>(632) 843-7639<br>(632) 844-2851</p>
        </div>
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="w-12 h-12 mb-4 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-mobile-screen text-xl"></i></div>
            <h4 class="font-bold text-slate-900 text-sm mb-2">Mobile / Smart</h4>
            <p class="text-xs text-slate-500 leading-relaxed">(+63) 926-287-5279<br>(+63) 925-819-3172<br>(+63) 998-989-9068</p>
        </div>
        <div class="dash-card reveal bg-white border border-slate-200 rounded-2xl p-7">
            <div class="w-12 h-12 mb-4 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fa-solid fa-envelope-open-text text-xl"></i></div>
            <h4 class="font-bold text-slate-900 text-sm mb-2">Email</h4>
            <p class="text-xs text-slate-500 leading-relaxed break-all">cs@priority-ph.com<br><br>Replies within 1 business day</p>
        </div>
    </div>
</section>
<!-- ================= MAP + INQUIRY FORM ================= -->
<section class="py-10 pb-20 px-6">
    <div class="max-w-7xl mx-auto bg-slate-950 text-white relative overflow-hidden rounded-3xl">
        <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-brand-blue/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="relative z-10 bg-slate-900/80 border border-white/10 rounded-3xl p-8 lg:p-12 backdrop-blur-xl shadow-2xl grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <div class="lg:col-span-5 flex flex-col justify-between h-full">
                <div>
                    <div class="inline-flex items-center gap-2 bg-brand-blue/20 border border-brand-blue/30 text-sky-400 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
                        <i class="fa-solid fa-headset"></i> FIND US
                    </div>
                    <h3 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight mb-4">Drop By Our Makati HQ</h3>
                    <p class="text-slate-400 text-sm leading-relaxed mb-8">
                        Visit our head office at Barangay San Isidro, Makati City â€” or send us a direct message and
                        a logistics specialist will get back to you promptly.
                    </p>
                </div>

                <div class="relative my-4 h-[340px] rounded-2xl border border-white/10 overflow-hidden shadow-2xl bg-brand-navycard">
                    <iframe src="https://maps.google.com/maps?q=14.5551104,121.0046699&z=16&output=embed" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="Priority Handling Logistics HQ - Makati City"></iframe>
                    <a href="https://www.google.com/maps?q=14.5551104,121.0046699" target="_blank" rel="noopener noreferrer" class="absolute top-4 right-4 z-[400] bg-brand-blue hover:bg-brand-darkblue text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 transition-colors"><i class="fa-solid fa-location-arrow"></i> Open in Maps</a>
                    <div class="absolute bottom-4 left-4 bg-brand-navycard/90 backdrop-blur-md p-3 rounded-xl border border-white/15 text-xs text-white shadow-xl flex items-center gap-3">
                        <div class="w-3 h-3 bg-cyan-400 rounded-full animate-pulse-glow"></div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Priority HQ</span>
                            <strong class="text-white text-xs">1618-B Copernico St., Makati City</strong>
                        </div>
                    </div>
                </div>
            </div>
<div class="lg:col-span-7">
                <form onsubmit="handleContactSubmit(event)" class="bg-slate-950/80 border border-white/10 p-6 sm:p-8 rounded-2xl shadow-2xl">
                    <h4 class="text-xl font-extrabold text-white mb-1">Send Us a Direct Message</h4>
                    <p class="text-slate-400 text-xs mb-6">Fill out the form below and a logistics specialist will respond promptly.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Full Name *</label>
                            <input type="text" id="contactName" required placeholder="Juan Dela Cruz" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Email Address *</label>
                            <input type="email" id="contactEmail" required placeholder="juan@company.ph" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Company Name</label>
                            <input type="text" id="contactCompany" placeholder="Your Company Name" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Phone Number</label>
                            <input type="tel" id="contactPhone" placeholder="+63 (917) 000-0000" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                        </div>
                    </div>

                                        <div class="mb-5">
                        <label class="block text-xs font-medium text-slate-300 mb-1">Shipment Details / Message *</label>
                        <textarea id="contactMessage" rows="4" required placeholder="Tell us about your cargo requirements, origin, destination, timeline, or any questions..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue"></textarea>
                    </div>

                    <button type="submit" id="contactSubmitBtn" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Send Inquiry Message
                    </button>
                    <div id="contactFormStatus" class="hidden mt-4 p-3 bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 rounded-xl text-center text-xs font-medium"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>