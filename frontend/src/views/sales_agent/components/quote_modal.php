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

<div id="quoteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 transition-all duration-300">
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
<script>
  /* ==================================================================
     SEND PDF QUOTATION — client-side logic (My Leads)
     ================================================================== */
  var _quoteLead = null;

  function openQuoteModal(lead) {
    _quoteLead = lead;

    document.getElementById('quoteLeadId').value = lead.id || lead.lead_id || '';
    var inquiryCode = lead.inquiry_code || lead.code || ('INQ-' + String(lead.id || '').substring(0, 8));
    document.getElementById('quoteInquiryCode').value = inquiryCode;
    document.getElementById('quoteInquiryBadge').innerText = inquiryCode;

    document.getElementById('quoteEmail').value = lead.email || '';
    document.getElementById('quoteCompany').value = lead.company_name || lead.company || '';
    document.getElementById('quoteRoute').value = (lead.origin && lead.destination) ? (lead.origin + ' ➔ ' + lead.destination) : '';

    // Prefill Base Freight from the lead's estimated price
    var est = parseFloat(lead.estimated_amount ?? lead.estimated_price ?? 0);
    document.getElementById('qBaseFreight').value = est > 0 ? est : '';

    // Default validity: +14 days (local date, avoids UTC day-shift)
    var d = new Date();
    d.setDate(d.getDate() + 14);
    var isoDate = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    document.getElementById('quoteValidity').value = isoDate;

    // Reset other charges
    ['qDiscount', 'qPickupTrucking', 'qDeliveryTrucking', 'qDocFee', 'qHandlingFee', 'qVat'].forEach(function (id) {
      document.getElementById(id).value = '';
    });
    document.getElementById('quoteTerms').value = '';

    recalcQuoteTotals();

    var modal = document.getElementById('quoteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeQuoteModal() {
    var modal = document.getElementById('quoteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function _qval(id) {
    var el = document.getElementById(id);
    return el ? (parseFloat(el.value) || 0) : 0;
  }

  function _qfmt(n) {
    return '₱' + (n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalcQuoteTotals() {
    var base = _qval('qBaseFreight');
    var disc = _qval('qDiscount');
    var other = _qval('qPickupTrucking') + _qval('qDeliveryTrucking') + _qval('qDocFee') + _qval('qHandlingFee');
    var vat = _qval('qVat');
    var total = Math.max(0, base + other - disc + vat);

    document.getElementById('qsBase').innerText = _qfmt(base);
    document.getElementById('qsOther').innerText = _qfmt(other);
    document.getElementById('qsDiscount').innerText = '-' + _qfmt(disc);
    document.getElementById('qsVat').innerText = _qfmt(vat);
    document.getElementById('qsTotal').innerText = _qfmt(total);
  }
  function buildQuotePdfCss() {
    return '  * { margin: 0; padding: 0; box-sizing: border-box; }'
      + '  body { font-family: "Segoe UI", Arial, sans-serif; color: #1e293b; background: #f8fafc; padding: 32px; }'
      + '  .sheet { max-width: 800px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }'
      + '  .head { background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; padding: 28px 36px; display: flex; justify-content: space-between; align-items: center; }'
      + '  .head h1 { font-size: 20px; letter-spacing: .5px; }'
      + '  .head p { font-size: 11px; opacity: .85; margin-top: 4px; }'
      + '  .head .doc { text-align: right; }'
      + '  .head .doc .t { font-size: 15px; font-weight: 800; letter-spacing: 2px; }'
      + '  .head .doc .n { font-size: 11px; opacity: .85; margin-top: 4px; }'
      + '  .body { padding: 28px 36px; }'
      + '  .meta { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }'
      + '  .meta .box { flex: 1; min-width: 160px; background: #eef2ff; border: 1px solid #e0e7ff; border-radius: 10px; padding: 10px 14px; }'
      + '  .meta .box .l { font-size: 9px; font-weight: 700; letter-spacing: 1px; color: #6366f1; text-transform: uppercase; }'
      + '  .meta .box .v { font-size: 12px; font-weight: 700; margin-top: 3px; color: #1e293b; }'
      + '  .billto { border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; }'
      + '  .billto .l { font-size: 9px; font-weight: 700; letter-spacing: 1px; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; }'
      + '  .billto .c { font-size: 15px; font-weight: 800; color: #1e293b; }'
      + '  .billto .s { font-size: 11px; color: #64748b; margin-top: 3px; }'
      + '  table.charges { width: 100%; border-collapse: collapse; font-size: 12px; }'
      + '  table.charges th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding: 9px 14px; border-bottom: 2px solid #e2e8f0; text-align: left; }'
      + '  table.charges th.r, table.charges td.r { text-align: right; }'
      + '  table.charges td { padding: 9px 14px; border-bottom: 1px solid #f1f5f9; }'
      + '  table.charges td.disc { color: #f43f5e; font-weight: 700; }'
      + '  .total-row td { background: linear-gradient(90deg, #eef2ff, #fff); border-top: 2px solid #6366f1; border-bottom: none; padding: 13px 14px; }'
      + '  .total-row .lbl { font-size: 13px; font-weight: 800; color: #1e293b; }'
      + '  .total-row .amt { font-size: 17px; font-weight: 800; color: #4f46e5; }'
      + '  .terms { margin-top: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; }'
      + '  .terms h3 { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }'
      + '  .terms p { font-size: 12px; color: #334155; line-height: 1.6; white-space: pre-wrap; }'
      + '  .foot { padding: 22px 36px 28px; display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; }'
      + '  .foot .sig { font-size: 11px; color: #64748b; }'
      + '  .foot .sig .line { width: 190px; border-top: 1px solid #cbd5e1; margin-top: 34px; padding-top: 5px; font-weight: 700; color: #334155; }'
      + '  .foot .note { font-size: 10px; color: #94a3b8; max-width: 320px; text-align: right; line-height: 1.5; }'
      + '  .brandfoot { background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center; padding: 12px; font-size: 9px; color: #94a3b8; letter-spacing: 1px; }'
      + '  .printbar { position: fixed; top: 14px; right: 14px; z-index: 10; }'
      + '  .printbar button { background: #4f46e5; color: #fff; border: none; border-radius: 10px; padding: 10px 16px; font-size: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 16px rgba(79, 70, 229, .35); }'
      + '  @media print { body { background: #fff; padding: 0; } .sheet { border: none; border-radius: 0; max-width: 100%; } .printbar { display: none; } }';
  }
  function buildQuotePdfHtml(data) {
    var esc = function (s) {
      return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
    var termsBlock = data.terms
      ? '<div class="terms"><h3>Terms &amp; Remarks</h3><p>' + esc(data.terms) + '</p></div>'
      : '';

    return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
      + '<title>Freight Quotation ' + esc(data.inquiryCode) + ' — ' + esc(data.company) + '</title>'
      + '<style>' + buildQuotePdfCss() + '</style>'
      + '</head><body onload="setTimeout(function(){ try { window.print(); } catch (e) {} }, 450);">'
      + '<div class="printbar"><button onclick="window.print()">Save as PDF</button></div>'
      + '<div class="sheet">'
      + '  <div class="head">'
      + '    <div>'
      + '      <h1>PRIORITY HANDLING LOGISTICS INC.</h1>'
      + '      <p>1618-B Copernico St., Makati City</p>'
      + '    </div>'
      + '    <div class="doc"><div class="t">QUOTATION</div><div class="n">Ref: ' + esc(data.inquiryCode) + '-Q</div></div>'
      + '  </div>'
      + '  <div class="body">'
      + '    <div class="meta">'
      + '      <div class="box"><div class="l">Date Issued</div><div class="v">' + esc(data.issuedDate) + '</div></div>'
      + '      <div class="box"><div class="l">Valid Until</div><div class="v">' + esc(data.validityLabel) + '</div></div>'
      + '      <div class="box"><div class="l">Service</div><div class="v">' + esc(data.service || 'General Freight') + '</div></div>'
      + '    </div>'
      + '    <div class="billto">'
      + '      <div class="l">Prepared For</div>'
      + '      <div class="c">' + esc(data.company) + '</div>'
      + '      <div class="s">' + esc(data.email) + '</div>'
      + (data.route ? '<div class="s"><b>Route:</b> ' + esc(data.route) + '</div>' : '')
      + '    </div>'
      + '    <table class="charges">'
      + '      <thead><tr><th>Charge Item</th><th class="r">Amount (₱)</th></tr></thead>'
      + '      <tbody>'
      + '        <tr><td>Base Freight</td><td class="r">' + esc(data.fmt(data.base)) + '</td></tr>'
      + '        <tr><td>Pickup Trucking</td><td class="r">' + esc(data.fmt(data.pickup)) + '</td></tr>'
      + '        <tr><td>Delivery Trucking</td><td class="r">' + esc(data.fmt(data.delivery)) + '</td></tr>'
      + '        <tr><td>Documentation Fee</td><td class="r">' + esc(data.fmt(data.docs)) + '</td></tr>'
      + '        <tr><td>Handling Fee</td><td class="r">' + esc(data.fmt(data.handling)) + '</td></tr>'
      + '        <tr><td>Value-Added Tax (VAT)</td><td class="r">' + esc(data.fmt(data.vat)) + '</td></tr>'
      + (data.discount > 0 ? '        <tr><td>Discount</td><td class="r disc">-' + esc(data.fmt(data.discount)) + '</td></tr>' : '')
      + '      </tbody>'
      + '      <tfoot><tr class="total-row"><td class="lbl">TOTAL QUOTATION</td><td class="r amt">' + esc(data.fmt(data.total)) + '</td></tr></tfoot>'
      + '    </table>'
      + termsBlock
      + '  </div>'
      + '  <div class="foot">'
      + '    <div class="sig">Prepared by:<div class="line">Sales Agent — Priority Handling</div></div>'
      + '    <div class="note">This quotation is valid until <b>' + esc(data.validityLabel) + '</b>. Rates are subject to change based on fuel surcharge and market conditions. Please attach this PDF when replying to your sales representative.</div>'
      + '  </div>'
      + '  <div class="brandfoot">PRIORITY HANDLING LOGISTICS INC. • OFFICIAL FREIGHT QUOTATION • ' + esc(data.inquiryCode) + '-Q</div>'
      + '</div></body></html>';
  }
  async function sendQuotePdf() {
    var leadId = document.getElementById('quoteLeadId').value;
    var inquiryCode = document.getElementById('quoteInquiryCode').value;
    var company = document.getElementById('quoteCompany').value.trim();
    var email = document.getElementById('quoteEmail').value.trim();
    var route = document.getElementById('quoteRoute').value.trim();
    var service = (_quoteLead && (_quoteLead.service_type || _quoteLead.service)) || '';

    var base = _qval('qBaseFreight');
    var disc = _qval('qDiscount');
    var pickup = _qval('qPickupTrucking');
    var delivery = _qval('qDeliveryTrucking');
    var docs = _qval('qDocFee');
    var handling = _qval('qHandlingFee');
    var vat = _qval('qVat');
    var total = Math.max(0, base + pickup + delivery + docs + handling - disc + vat);

    var validity = document.getElementById('quoteValidity').value;
    var validityLabel = validity
      ? new Date(validity + 'T00:00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
      : 'N/A';
    var terms = document.getElementById('quoteTerms').value.trim();

    // --- Validation ---
    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
      if (typeof SwiftAlert !== 'undefined') {
        SwiftAlert.fire({ icon: 'warning', title: 'Invalid Email', text: 'Please enter a valid customer email address.' });
      } else { alert('Please enter a valid customer email address.'); }
      return;
    }
    if (base <= 0) {
      if (typeof SwiftAlert !== 'undefined') {
        SwiftAlert.fire({ icon: 'warning', title: 'Missing Charges', text: 'Base Freight is required to generate the quotation.' });
      } else { alert('Base Freight is required to generate the quotation.'); }
      return;
    }

    var issuedDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

    // --- 1. Print-ready quotation window (Save as PDF) ---
    var pdfWindow = window.open('', '_blank', 'width=920,height=1040');
    if (!pdfWindow) {
      if (typeof SwiftAlert !== 'undefined') {
        SwiftAlert.fire({ icon: 'error', title: 'Popup Blocked', text: 'Please allow popups to generate the PDF quotation.' });
      } else { alert('Please allow popups to generate the PDF quotation.'); }
      return;
    }
    pdfWindow.document.open();
    pdfWindow.document.write(buildQuotePdfHtml({
      inquiryCode: inquiryCode, company: company || 'Customer', email: email,
      route: route, service: service, issuedDate: issuedDate, validityLabel: validityLabel,
      base: base, pickup: pickup, delivery: delivery, docs: docs, handling: handling,
      vat: vat, discount: disc, total: total, terms: terms, fmt: _qfmt
    }));
    pdfWindow.document.close();

    // --- 2. Prefilled mailto draft ---
    var subject = 'Freight Quotation ' + inquiryCode + ' — ' + (company || 'Priority Handling');
    var body = 'Dear ' + (company || 'Customer') + ',\n\n'
      + 'Thank you for your inquiry with Priority Handling Logistics Inc.\n\n'
      + 'ROUTE: ' + (route || 'N/A') + '\n'
      + 'SERVICE: ' + (service || 'General Freight') + '\n'
      + 'TOTAL QUOTATION: ' + _qfmt(total) + '\n'
      + 'VALID UNTIL: ' + validityLabel + '\n'
      + (terms ? '\nTERMS & REMARKS:\n' + terms + '\n' : '')
      + '\nPlease find attached the complete PDF quotation. Do not hesitate to reach out for any clarifications.\n\n'
      + 'Best regards,\nSales Team\nPriority Handling Logistics Inc.';
    window.location.href = 'mailto:' + email + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);

    // --- 3. Mark lead as quote_sent (only if it is still before that stage) ---
    if (_quoteLead && leadId) {
      var order = ['new_inquiry', 'qualifying', 'quote_sent', 'negotiation', 'closed_won', 'closed_lost'];
      var cur = (typeof normalizeStatus === 'function') ? normalizeStatus(_quoteLead.status) : (_quoteLead.status || 'new_inquiry');
      if (order.indexOf(cur) !== -1 && order.indexOf(cur) < order.indexOf('quote_sent')) {
        try {
          await fetch(API_URL + '/api/v1/leads/' + leadId + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'quote_sent' })
          });
        } catch (err) {
          console.error('Failed to update lead status to quote_sent:', err);
        }
      }
    }

    closeQuoteModal();
    if (typeof SwiftAlert !== 'undefined') {
      SwiftAlert.fire({
        icon: 'success',
        title: 'Quotation Ready',
        text: 'PDF opened in a new tab and a mail draft was started for ' + email + '.'
      });
    }
  }

  // Wire up live total recalculation
  document.addEventListener('DOMContentLoaded', function () {
    ['qBaseFreight', 'qDiscount', 'qPickupTrucking', 'qDeliveryTrucking', 'qDocFee', 'qHandlingFee', 'qVat'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.addEventListener('input', recalcQuoteTotals);
    });
  });
</script>