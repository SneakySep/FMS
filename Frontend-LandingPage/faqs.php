<?php
$pageTitle  = 'FAQs';
$activePage = 'faqs';
include 'includes/header.php';
?>

<!-- ================= DASHBOARD BANNER ================= -->
<section class="dashboard-banner relative overflow-hidden text-white py-16 px-6">
    <div class="max-w-7xl mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-sky-300 text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">
            <i class="fa-solid fa-circle-question"></i> FAQs Dashboard
        </div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">Frequently Asked Questions</h1>
        <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">Everything you need to know about shipping with Priority — by category.</p>
        <nav class="mt-6 text-xs text-slate-400 flex items-center gap-1.5">
            <a href="index.php" class="hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-sky-300 font-semibold">FAQs</span>
        </nav>
    </div>
</section>

<!-- ================= FAQ ACCORDIONS ================= -->
<section class="py-20 px-6 max-w-4xl mx-auto">
    <div class="grid gap-8">
        <!-- GENERAL -->
        <div class="reveal">
            <h3 class="flex items-center gap-2 font-extrabold text-slate-900 text-lg mb-4">
                <span class="w-8 h-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center text-sm"><i class="fa-solid fa-building"></i></span>
                General
            </h3>
            <div class="space-y-3">
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm" open>
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        Are you accredited?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">Yes. We are SEC Registered (Cert. No. CS200502125), DOST-ICTO Certified, and PEZA Accredited.</p>
                </details>
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        How long has Priority been in business?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">We were incorporated in 2005 — more than two decades of dependable Philippine logistics service.</p>
                </details>
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        Where is your head office?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">1618-B Copernico Street, Barangay San Isidro, Makati City, Philippines.</p>
                </details>
            </div>
        </div>
<!-- SERVICES & SHIPPING -->
        <div class="reveal">
            <h3 class="flex items-center gap-2 font-extrabold text-slate-900 text-lg mb-4">
                <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-sm"><i class="fa-solid fa-truck-fast"></i></span>
                Services & Shipping
            </h3>
            <div class="space-y-3">
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm" open>
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        What services do you offer?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">We provide domestic and international courier, air freight, trucking, and customs brokerage.</p>
                </details>
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        Do you handle international shipments?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">Yes — we ship to 140+ countries and territories around the world via partner carriers and our own network.</p>
                </details>
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        How can I track my shipment?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">Use the Shipment Tracker on the Forms or Home dashboard, or contact our support team with your tracking number.</p>
                </details>
            </div>
        </div>

        <!-- QUOTES & BILLING -->
        <div class="reveal">
            <h3 class="flex items-center gap-2 font-extrabold text-slate-900 text-lg mb-4">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-money-bill-wave"></i></span>
                Quotes & Billing
            </h3>
            <div class="space-y-3">
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm" open>
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        How do I get a quote?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">Use the Rate Calculator on the Forms dashboard for an instant estimate, or submit the Online Inquiry form and our team will respond promptly.</p>
                </details>
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        Are rates fixed or estimates?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">Calculator results are estimates. Final rates depend on actual weight, volume, destination, and applicable customs duties.</p>
                </details>
            </div>
        </div>
<!-- PROMOS -->
        <div class="reveal">
            <h3 class="flex items-center gap-2 font-extrabold text-slate-900 text-lg mb-4">
                <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-sm"><i class="fa-solid fa-gift"></i></span>
                Promos
            </h3>
            <div class="space-y-3">
                <details class="faq-item bg-amber-50/60 p-5 rounded-2xl border border-amber-200 shadow-sm" open>
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        Is there a promo for new clients?
                        <i class="fa-solid fa-chevron-down faq-chevron text-amber-500 text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3"><strong class="text-slate-700">Yes!</strong> New clients get a free Priority coffee mug for transactions of at least Php 500, or a free Priority umbrella for transactions of at least Php 1,000. Click the <span class="text-amber-600 font-bold">PROMO</span> button in the menu to see the details.</p>
                </details>
                <details class="faq-item bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <summary class="flex justify-between items-center gap-4 text-sm font-bold text-slate-900">
                        How do I claim my free gift?
                        <i class="fa-solid fa-chevron-down faq-chevron text-brand-blue text-xs"></i>
                    </summary>
                    <p class="text-sm text-slate-500 leading-relaxed mt-3">Call 843-7484, mention the promo when you book your shipment, and our team will reserve your gift. Promos run while supplies last.</p>
                </details>
            </div>
        </div>
    </div>
</section>

<!-- ================= STILL STUCK? ================= -->
<section class="py-20 px-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="dash-card reveal bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center">
            <div class="w-12 h-12 mx-auto mb-4 bg-brand-blue/10 text-brand-blue rounded-xl flex items-center justify-center"><i class="fa-solid fa-headset text-lg"></i></div>
            <h4 class="font-bold text-slate-900 mb-2">Live Chat</h4>
            <p class="text-xs text-slate-500 mb-4">Talk to our support bot anytime.</p>
            <button onclick="toggleChat()" class="text-brand-blue font-semibold text-sm hover:underline">Open Chat &rarr;</button>
        </div>
        <div class="dash-card reveal bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center">
            <div class="w-12 h-12 mx-auto mb-4 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fa-solid fa-phone text-lg"></i></div>
            <h4 class="font-bold text-slate-900 mb-2">Call Us</h4>
            <p class="text-xs text-slate-500 mb-4">24/7 hotline for urgent inquiries.</p>
            <a href="tel:+6328437484" class="text-brand-blue font-semibold text-sm hover:underline">843-7484 &rarr;</a>
        </div>
        <div class="dash-card reveal bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center">
            <div class="w-12 h-12 mx-auto mb-4 bg-sky-100 text-sky-600 rounded-xl flex items-center justify-center"><i class="fa-solid fa-envelope text-lg"></i></div>
            <h4 class="font-bold text-slate-900 mb-2">Email Us</h4>
            <p class="text-xs text-slate-500 mb-4">We reply within one business day.</p>
            <a href="mailto:cs@priority-ph.com" class="text-brand-blue font-semibold text-sm hover:underline">cs@priority-ph.com &rarr;</a>
        </div>
    </div>
</section>

<!-- ================= CTA BAND ================= -->
<section class="py-16 px-6">
    <div class="max-w-7xl mx-auto dashboard-banner relative overflow-hidden rounded-3xl text-white p-10 sm:p-14 text-center">
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">Still Have a Question?</h2>
            <p class="text-slate-300 text-sm max-w-2xl mx-auto mb-8">Send us your inquiry through the Forms dashboard and get an answer from a real specialist.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="forms.php" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Ask a Question
                </a>
                <button onclick="openPromoModal()" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-black text-sm px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fa-solid fa-gift"></i> View Promos
                </button>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>