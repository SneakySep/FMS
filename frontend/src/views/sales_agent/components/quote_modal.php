<!-- ======================================================================
     SEND PDF QUOTATION MODAL (Sales Agent — My Leads)
     100% client-side: pre-fills lead details, live totals, opens a
     print-ready quotation window (Save as PDF) + prefilled mailto draft.
     No element IDs clash with view_lead_modal.php / lead_modal.php.
     ====================================================================== -->
<style>
  /* Entrance animations (mirrors view_lead_modal.php conventions) */
  @keyframes qmBackdropIn { from { opacity: 0; } to { opacity: 1; } }
  @keyframes qmPanelIn {
    from { opacity: 0; transform: translateY(18px) scale(.96); }
    to   { opacity: 1; transform: translateY(0)    scale(1);   }
  }
  #quoteModal:not(.hidden) { animation: qmBackdropIn .25s ease-out both; }
  #quoteModal:not(.hidden) > .qm-panel { animation: qmPanelIn .32s cubic-bezier(.22,1,.36,1) both; }
</style>

<div id="quoteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300" style="display: none;">
  <div class="absolute inset-0 bg-slate-900/55 backdrop-blur-sm" onclick="closeQuoteModal()"></div>

  <div class="qm-panel relative flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl shadow-indigo-900/20 ring-1 ring-slate-900/5">

    <!-- HEADER BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-indigo-600 to-indigo-700 px-6 py-5 sm:px-8">
      <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10"></div>
      <div class="pointer-events-none absolute -bottom-12 -left-8 h-40 w-40 rounded-full bg-white/5"></div>

      <div class="relative flex items-center gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-white ring-1 ring-white/30 backdrop-blur">
          <i class="fa-solid fa-file-pdf text-lg"></i>
        </div>
        <div class="min-w-0 flex-1">
          <h3 class="text-lg font-bold text-white">Send PDF Quotation</h3>
          <p class="mt-0.5 truncate text-xs font-medium text-indigo-100">Configure quotation details for customer email</p>
        </div>
        <span class="hidden shrink-0 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white ring-1 ring-white/30 sm:inline-block" id="quoteInquiryBadge">INQ-CODE</span>
      </div>

      <button type="button" onclick="closeQuoteModal()" class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-white/15 text-white ring-1 ring-white/30 transition hover:bg-white hover:text-indigo-700">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <!-- BODY -->
    <div class="flex-1 overflow-y-auto px-6 py-6 sm:px-8">

      <input type="hidden" id="quoteLeadId" value="">
      <input type="hidden" id="quoteInquiryCode" value="">

      <!-- CUSTOMER EMAIL -->
      <div class="mb-5">
        <label for="quoteEmail" class="mb-1 block text-xs font-bold text-slate-700">Customer Email <span class="text-rose-500">*</span></label>
        <div class="relative">
          <i class="fa-regular fa-envelope pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
          <input type="email" id="quoteEmail" placeholder="customer@company.com" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3.5 text-xs font-semibold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
      </div>

      <!-- COMPANY / ROUTE -->
      <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="quoteCompany" class="mb-1 block text-xs font-bold text-slate-700">Company / Customer</label>
          <input type="text" id="quoteCompany" placeholder="e.g. ProEdge Computing Corp." class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label for="quoteRoute" class="mb-1 block text-xs font-bold text-slate-700">Route</label>
          <div class="relative">
            <i class="fa-solid fa-route pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            <input type="text" id="quoteRoute" placeholder="Manila ➔ Cebu" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3.5 text-xs font-semibold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
          </div>
        </div>
      </div>
      <!-- CHARGES GRID -->
      <div class="mb-5">
        <h4 class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
          <i class="fa-solid fa-calculator text-indigo-400"></i> Quotation Charges
        </h4>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="qBaseFreight" class="mb-1 block text-xs font-bold text-slate-700">Base Freight (₱) <span class="text-rose-500">*</span></label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qBaseFreight" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div>
            <label for="qDiscount" class="mb-1 block text-xs font-bold text-slate-700">Discount (₱)</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qDiscount" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div>
            <label for="qPickupTrucking" class="mb-1 block text-xs font-bold text-slate-700">Pickup Trucking (₱)</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qPickupTrucking" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div>
            <label for="qDeliveryTrucking" class="mb-1 block text-xs font-bold text-slate-700">Delivery Trucking (₱)</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qDeliveryTrucking" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div>
            <label for="qDocFee" class="mb-1 block text-xs font-bold text-slate-700">Documentation Fee (₱)</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qDocFee" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div>
            <label for="qHandlingFee" class="mb-1 block text-xs font-bold text-slate-700">Handling Fee (₱)</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qHandlingFee" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div class="sm:col-span-2">
            <label for="qVat" class="mb-1 block text-xs font-bold text-slate-700">Value-Added Tax / VAT (₱)</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">₱</span>
              <input type="number" step="0.01" min="0" id="qVat" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-8 pr-3.5 text-xs font-bold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
        </div>
      </div>
      <!-- LIVE TOTALS SUMMARY -->
      <div class="mb-5 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 to-white p-4">
        <div class="flex items-center justify-between py-1 text-xs">
          <span class="font-medium text-slate-500">Base Freight:</span>
          <span class="font-bold text-slate-700" id="qsBase">₱0.00</span>
        </div>
        <div class="flex items-center justify-between py-1 text-xs">
          <span class="font-medium text-slate-500">Total Other Charges (Trucking/Docs/Handling):</span>
          <span class="font-bold text-slate-700" id="qsOther">₱0.00</span>
        </div>
        <div class="flex items-center justify-between py-1 text-xs">
          <span class="font-medium text-slate-500">Discount:</span>
          <span class="font-bold text-rose-500" id="qsDiscount">-₱0.00</span>
        </div>
        <div class="flex items-center justify-between py-1 text-xs">
          <span class="font-medium text-slate-500">Value-Added Tax:</span>
          <span class="font-bold text-slate-700" id="qsVat">₱0.00</span>
        </div>
        <div class="mt-2 flex items-center justify-between border-t border-indigo-100 pt-3">
          <span class="text-sm font-extrabold text-slate-800">Total Quotation:</span>
          <span class="text-lg font-extrabold text-indigo-600" id="qsTotal">₱0.00</span>
        </div>
      </div>

      <!-- VALIDITY DATE -->
      <div class="mb-5">
        <label for="quoteValidity" class="mb-1 block text-xs font-bold text-slate-700">Quotation Validity Date</label>
        <div class="relative">
          <i class="fa-regular fa-calendar pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
          <input type="date" id="quoteValidity" class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3.5 text-xs font-semibold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
      </div>

      <!-- TERMS & REMARKS -->
      <div>
        <label for="quoteTerms" class="mb-1 block text-xs font-bold text-slate-700">Terms &amp; Remarks</label>
        <textarea id="quoteTerms" rows="3" placeholder="e.g. Price inclusive of fuel surcharge and handling fees..." class="w-full resize-none rounded-xl border border-slate-200 bg-white p-2.5 text-xs text-slate-700 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
      </div>

    </div>

    <!-- FOOTER ACTIONS -->
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4 sm:px-8">
      <button type="button" onclick="closeQuoteModal()" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 active:scale-[0.98]">
        Cancel
      </button>
      <button type="button" onclick="sendQuotePdf()" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-emerald-500/30 transition hover:from-emerald-600 hover:to-teal-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-500/30 active:scale-[0.98]">
        <i class="fa-solid fa-paper-plane"></i> Generate &amp; Send PDF
      </button>
    </div>

  </div>
</div>
