</main>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="bg-brand-navy text-slate-400 py-16 px-6 text-xs">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-white/10">
            <div class="lg:col-span-2">
                <a href="index.php" class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-brand-blue text-white rounded-lg flex items-center justify-center p-1.5">
                        <i class="fa-solid fa-cube text-lg"></i>
                    </div>
                    <div class="leading-none">
                        <h1 class="text-base font-black tracking-wider text-white uppercase leading-tight">PRIORITY <span class="text-brand-blue">HANDLING</span></h1>
                        <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">Logistics Inc. • Since 2005</span>
                    </div>
                </a>

                <p class="text-slate-400 max-w-xs leading-relaxed mb-6">
                    Priority Handling Logistics, Inc. is a registered and licensed company operating domestic and international courier and freight forwarding services.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 hover:bg-brand-blue text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com/YouarePriority" target="_blank" class="w-8 h-8 rounded-full bg-white/5 hover:bg-brand-blue text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="http://youarepriority.tumblr.com/" target="_blank" class="w-8 h-8 rounded-full bg-white/5 hover:bg-brand-blue text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-tumblr"></i></a>
                    <a href="https://www.pinterest.com/socmedpriority/" target="_blank" class="w-8 h-8 rounded-full bg-white/5 hover:bg-brand-blue text-white flex items-center justify-center transition-colors"><i class="fa-brands fa-pinterest"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-wider">QUICK LINKS</h4>
                <ul class="space-y-2.5">
                    <li><a href="index.php" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="about.php" class="hover:text-white transition-colors">About</a></li>
                    <li><a href="services.php" class="hover:text-white transition-colors">Services</a></li>
                    <li><button onclick="openPromoModal()" class="hover:text-white transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-gift text-amber-400 text-[10px]"></i> Promos
                    </button></li>
                    <li><a href="forms.php" class="hover:text-white transition-colors">Forms</a></li>
                    <li><a href="faqs.php" class="hover:text-white transition-colors">FAQs</a></li>
                    <li><a href="why-us.php" class="hover:text-white transition-colors">Why Us?</a></li>
                    <li><a href="careers.php" class="hover:text-white transition-colors">Careers</a></li>
                    <li><a href="contact.php" class="hover:text-white transition-colors">Contact Us</a></li>
                </ul>
            </div>
<div>
                <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-wider">LEGAL & PRIVACY (PH)</h4>
                <ul class="space-y-2.5">
                    <li><a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-white text-sky-400 font-semibold transition-colors flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-[10px]"></i> Privacy Policy (RA 10173)</a></li>
                    <li><a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-white text-sky-400 font-semibold transition-colors flex items-center gap-1.5"><i class="fa-solid fa-file-contract text-[10px]"></i> Terms & Conditions (PH)</a></li>
                    <li><a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-white transition-colors">Data Privacy Rights</a></li>
                    <li><a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-white transition-colors">Customs Clearance Policy</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-wider">STAY CONNECTED</h4>
                <ul class="space-y-2.5">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-location-dot text-sky-400 mt-0.5"></i> 1618-B Copernico St., Bgy. San Isidro, Makati City</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-phone text-sky-400 mt-0.5"></i> (632) 843-7484 | 843-7639 | 844-2851</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-mobile-screen text-sky-400 mt-0.5"></i> (+63) 926-287-5279</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-envelope text-sky-400 mt-0.5"></i> cs@priority-ph.com</li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-xs mb-4 uppercase tracking-wider">NEWSLETTER</h4>
                <p class="mb-3 leading-relaxed">Stay updated with the latest news and shipping insights.</p>
                <form onsubmit="subscribeNewsletter(event)" class="flex bg-white/5 border border-white/10 rounded-lg overflow-hidden">
                    <input type="email" placeholder="Enter your email" required class="bg-transparent px-3 py-2 text-white text-xs w-full focus:outline-none">
                    <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white px-3 py-2 transition-colors">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-slate-500">
            <p>&copy; 2026 Priority Handling Logistics Inc. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-slate-300">Privacy Policy</a>
                <span>&bull;</span>
                <a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-slate-300">Terms of Service</a>
            </div>
        </div>
    </footer>
<!-- FLOATING PROMO AD BADGE (opens the Promos pop-up) -->
    <button onclick="openPromoModal()" class="promo-badge fixed bottom-6 left-6 z-50 flex items-center gap-2 text-white font-black text-xs uppercase tracking-widest px-4 py-3 rounded-full shadow-xl hover:scale-110 transition-transform cursor-pointer select-none" title="View Exclusive Promos">
        <i class="fa-solid fa-gift text-base"></i> PROMO
    </button>

    <!-- FLOATING CHAT WIDGET -->
    <div onclick="toggleChat()" class="fixed bottom-6 right-6 w-14 h-14 bg-brand-blue hover:bg-brand-darkblue text-white rounded-full flex items-center justify-center text-2xl shadow-xl shadow-blue-500/30 cursor-pointer z-50 transition-transform hover:scale-110">
        <i class="fa-solid fa-comments"></i>
    </div>

    <div id="chatBox" class="fixed bottom-24 right-6 w-[360px] max-w-[calc(100vw-3rem)] h-[480px] bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 flex flex-col overflow-hidden transition-all duration-300 opacity-0 pointer-events-none translate-y-6">
        <div class="bg-brand-navy text-white p-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-blue flex items-center justify-center text-xs">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold">Priority Support PH</h4>
                    <span class="text-[10px] text-emerald-400 font-semibold">&bull; Online</span>
                </div>
            </div>
            <button onclick="toggleChat()" class="text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div id="chatBody" class="flex-1 p-4 bg-slate-50 overflow-y-auto flex flex-col gap-3">
            <div class="bg-white border border-slate-200 text-slate-800 text-xs p-3 rounded-lg max-w-[85%] self-start shadow-sm leading-relaxed">
                Mabuhay! Welcome to Priority Handling Logistics. How can we assist you with your courier or freight forwarding needs today?
            </div>
        </div>

        <div class="p-3 border-t border-slate-200 bg-white flex gap-2">
            <input type="text" id="chatInput" onkeypress="handleChatKeyPress(event)" placeholder="Type a message..." class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-blue">
            <button onclick="sendMessage()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-4 py-2 rounded-lg text-xs transition-colors">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
<!-- PRIVACY POLICY MODAL -->
    <div id="privacyModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[70] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-3xl w-full max-h-[85vh] flex flex-col text-slate-200 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-slate-950">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-brand-blue text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Privacy Policy</h3>
                        <span class="text-[10px] text-slate-400">Compliant with Republic Act No. 10173 (Philippine Data Privacy Act of 2012)</span>
                    </div>
                </div>
                <button onclick="closePrivacyModal()" class="text-slate-400 hover:text-white p-2 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-xs leading-relaxed text-slate-300">
                <p class="font-semibold text-white">Effective Date: January 1, 2026</p>
                <p>Priority Handling Logistics Inc. is committed to protecting your personal data under RA 10173 (Data Privacy Act of 2012). We collect only the personal information needed to process shipments, respond to inquiries, and fulfill our service obligations.</p>
                <p><strong class="text-white">What we collect:</strong> Name, contact details, company information, and shipment details provided through our online forms.</p>
                <p><strong class="text-white">How we use it:</strong> To arrange courier and freight forwarding services, respond to inquiries, send order/promo updates (with consent), and comply with customs and regulatory requirements.</p>
                <p><strong class="text-white">Your rights:</strong> Under RA 10173 you may access, correct, or request deletion of your personal data, or withdraw consent, by contacting our Data Protection Officer at cs@priority-ph.com.</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-slate-950 flex justify-end">
                <button onclick="closePrivacyModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-5 py-2 rounded-lg text-xs font-semibold">I Understand</button>
            </div>
        </div>
    </div>

    <!-- TERMS & CONDITIONS MODAL -->
    <div id="termsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[70] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-3xl w-full max-h-[85vh] flex flex-col text-slate-200 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-slate-950">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-brand-blue text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Terms and Conditions</h3>
                        <span class="text-[10px] text-slate-400">Governed by the Laws of the Republic of the Philippines</span>
                    </div>
                </div>
                <button onclick="closeTermsModal()" class="text-slate-400 hover:text-white p-2 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-xs leading-relaxed text-slate-300">
                <p class="font-semibold text-white">Effective Date: January 1, 2026</p>
                <p class="font-semibold text-white mb-2">TERMS AND CONDITIONS OF CARRIAGE</p>
                <p><strong class="text-white">1. In these conditions.</strong> The &quot;Carrier&quot; means Priority Handling Logistics, Inc. carrying on business in its own name and under any business name and unless the context otherwise requires its officers, servants, agents and sub-contractors THE CARRIES IS NOT A COMMON CARRIER and will accept no liability as such.</p>
                <p><strong class="text-white">2. The Carrier reserves the right to:</strong></p>
                <p class="pl-4">a) refuse the carriage or transport of goods to its destination,</p>
                <p class="pl-4">b) carry consignor's documents or goods by any route and procedure, and by successive carriers and according to its handling storage and transportation methods, and</p>
                <p class="pl-4">c) to inspect any goods consigned to ensure that they are capable of carriage to the countries of destination within the standard operating procedures, customs declarations, and handling methods of the Carrier.</p>
                <p><strong class="text-white">4. The Consignor is responsible</strong> for the packing of the goods including the packing in any container that may be supplied to the Consignor by the Carrier. The Carrier accepts no responsibility for loss or damage or to the goods caused by inadequate of inappropriate packaging. It is the responsibility of the Consignor to address adequately each consignment to enable effective delivery to be made. The Carrier shall not be liable for delay in forwarding or delivery resulting from the Consignor's failure to comply with its obligation in this respect.</p>
                <p><strong class="text-white">5. The Consignor warrants</strong> that it is authorized to accept and is accepting these Conditions on behalf of itself but also as an agent for and on behalf of all other person who are or may hereafter become interested in the goods. The Consignor indemnifies the Carrier against any damages, costs and expenses resulting from any breach of this warranty.</p>
                <p><strong class="text-white">6. The Carrier shall have a lien</strong> on all goods shipped, for freight charges and/or advance for other charges of any kind arising out of transportation hereunder and may refuse to surrender possession of the goods until such charges are paid.</p>
                <p><strong class="text-white">7. The Carrier is liable</strong> for damage sustained in the event of the destruction or loss of, or of damage to any cargo, the occurrence which caused the damage so sustained takes place during carriage and the Carrier is also liable for damage occasioned by delay in the carriage of cargo. The Carrier is not liable if it proves that it and its servants and agents have taken all necessary measures to avoid the damage or that it was impossible for it or them to take such measures. PROVIDED ALWAYS THAT the liability of the Carrier under these conditions shall be limited to the payment by the Carrier by way of damages of a sum of not exceeding US$100.00 or its local currency equivalent and Pesos 100.00 for international and domestic carriage respectively per consignment or in the case where transit insurance is affected the amount payable thereunder in the event of loss or damage to the goods.</p>
                <p><strong class="text-white">8. Any claim brought</strong> by a customer against the Carrier hereunder in respect of duties and liabilities must be notified by the customer to an office of the Carrier with in 48 hours and in writing within 14 days of the day when the goods should have reached their destination. No claim for loss or damage may be entertained if o in is filed beyond the allowable period or until the transportation charges have been paid.</p>
                <p><strong class="text-white">9. The Carrier will not carry</strong> dangerous, hazardous, combustible or explosive materials, gold and silver bullion, coin, dust, cyanides, precipitates or any form of uncoined gold and silver bullion, platinum and other precious metals, precious and semi-precious stones, currency (paper or coin) of any nationality, negotiable securities, stocks, bonds, certificates, stamps, blank or endorsed bank cheques, money orders or travelers cheques, antiques, works of art, livestock, plants, drugs, pharmaceuticals, liquor, firearms, tobacco, foodstuff, IATA restricted articles or perishables and in the event that the Consignor should consign such items. with the Carrier the Consignor indemnities the Carrier for all claims, damages costs and expenses which may arise as a result of such carriage and the Carrier shall have the right to abandon carriage of the same immediately upon Carrier having knowledge that the goods infringe this condition.</p>
                <p><strong class="text-white">10. If the carriage involves</strong> an ultimate destination or stop in a country other than the country of departure, the Warsaw Convention may be applicable and the Convention governs and in most cases limits the liability of carriers in respect of lost of or damage to cargo.</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-slate-950 flex justify-end">
                <button onclick="closeTermsModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-5 py-2 rounded-lg text-xs font-semibold">Accept Terms</button>
            </div>
        </div>
    </div>
<!-- PROMOS POP-UP AD (NEW CLIENTS FREE GIFTS) -->
    <div id="promoModal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-[80] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="promo-popup bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl relative overflow-hidden">

            <!-- Promo Header Band -->
            <div class="relative bg-gradient-to-r from-brand-darkblue via-brand-blue to-sky-600 text-white p-8 sm:p-10 text-center overflow-hidden">
                <div class="absolute inset-0 pointer-events-none opacity-25">
                    <i class="fa-solid fa-gift gift-sway absolute -top-4 left-4 text-8xl" style="transform:rotate(-12deg)"></i>
                    <i class="fa-solid fa-gift absolute -bottom-8 right-4 text-7xl" style="transform:rotate(14deg)"></i>
                    <i class="fa-solid fa-star text-2xl top-8 right-1/4"></i>
                    <i class="fa-solid fa-star text-lg bottom-6 left-1/4 text-amber-300"></i>
                </div>
                <div class="relative z-10">
                    <span class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-fire text-amber-300"></i> Special Offer • Limited Time
                    </span>
                    <h3 class="text-2xl sm:text-4xl font-black tracking-tight mb-2">NEW CLIENTS GET FREE GIFTS!</h3>
                    <p class="text-sky-100 text-xs sm:text-sm">Ship with us today and claim your freebie — while supplies last.</p>
                </div>
                <button onclick="closePromoModal()" aria-label="Close promo" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/15 hover:bg-white/30 text-white flex items-center justify-center text-sm transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Promo Offer Cards -->
            <div class="p-6 sm:p-9 bg-slate-50 grid md:grid-cols-2 gap-5">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-mug-hot text-2xl"></i>
                    </div>
                    <span class="inline-block bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-2">Tier 1</span>
                    <h4 class="font-black text-slate-900 text-lg mb-1">FREE PRIORITY MUG</h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-3">Enjoy a free branded Priority Handling coffee mug with a sleeve of coffee when you ship with us.</p>
                    <div class="bg-slate-100 rounded-lg px-3 py-2 text-xs font-bold text-slate-700">Valid for transactions worth <span class="text-amber-600">&ge; Php 500</span></div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 mx-auto mb-4 bg-brand-blue/10 text-brand-blue rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-umbrella text-2xl"></i>
                    </div>
                    <span class="inline-block bg-brand-blue/10 text-brand-blue text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-2">Tier 2</span>
                    <h4 class="font-black text-slate-900 text-lg mb-1">FREE PRIORITY UMBRELLA</h4>
                    <p class="text-xs text-slate-500 leading-relaxed mb-3">Get a premium automatic Priority umbrella to keep you covered on the go — rain or shine.</p>
                    <div class="bg-slate-100 rounded-lg px-3 py-2 text-xs font-bold text-slate-700">Valid for transactions worth <span class="text-brand-blue">&ge; Php 1,000</span></div>
                </div>
            </div>

            <!-- Promo CTA Band -->
            <div class="bg-brand-navy text-white p-6 sm:p-7 text-center">
                <p class="text-[10px] uppercase tracking-widest text-slate-400 mb-1">To claim your free gift, contact us now</p>
                <a href="tel:+6328437484" class="text-3xl sm:text-4xl font-black tracking-wider text-white hover:text-amber-300 transition-colors inline-flex items-center gap-3">
                    <i class="fa-solid fa-phone text-amber-400 text-2xl"></i> 843-7484
                </a>
                <div class="mt-5 flex flex-wrap justify-center gap-3">
                    <a href="forms.php#claim" onclick="closePromoModal()" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-black text-xs px-6 py-3 rounded-xl transition-all shadow-lg shadow-amber-500/25 hover:-translate-y-0.5 inline-flex items-center gap-2">
                        <i class="fa-solid fa-gift"></i> CLAIM THIS OFFER
                    </a>
                    <button onclick="closePromoModal()" class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-6 py-3 rounded-xl transition-colors">
                        Maybe Later
                    </button>
                </div>
                <p class="mt-4 text-[10px] text-slate-500 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i> Promo applies to new clients only. While supplies last. Terms & conditions apply.
                </p>
            </div>
        </div>
    </div>
<!-- Leaflet.js & Custom Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/main.js"></script>
</body>
</html>