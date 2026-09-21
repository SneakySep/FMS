<!-- Send Quotation Modal -->
<div id="sendQuoteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4 overflow-y-auto">
  <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 space-y-4 my-auto">
    
    <div class="flex items-center justify-between border-b pb-3 border-slate-100">
      <div>
        <h3 class="font-bold text-slate-800 text-base">Send PDF Quotation</h3>
        <p class="text-xs text-slate-500">Configure quotation details for customer email</p>
      </div>
      <button onclick="closeSendQuoteModal()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
    </div>

    <form onsubmit="handleSendQuotationSubmit(event)" class="space-y-3">
      <!-- Hidden Inputs -->
      <input type="hidden" id="quoteLeadId">
      <input type="hidden" id="quoteService">

      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Customer Email</label>
        <input type="email" id="quoteCustomerEmail" required class="w-full text-xs p-2.5 rounded-xl border border-slate-200 bg-slate-50" readonly>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Company / Customer</label>
          <input type="text" id="quoteCustomerName" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 bg-slate-50" readonly>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Route</label>
          <input type="text" id="quoteRoute" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 bg-slate-50" readonly>
        </div>
      </div>

      <!-- Base Amount & Discount -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Base Freight (₱)</label>
          <input type="number" id="quoteBaseAmount" step="0.01" required class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Discount (₱)</label>
          <input type="number" id="quoteDiscount" step="0.01" value="0" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
        </div>
      </div>

      <!-- Other Charges (Pickup, Delivery, Docs, Handling) -->
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Pickup Trucking (₱)</label>
          <input type="number" id="quotePickup" step="0.01" value="0" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Delivery Trucking (₱)</label>
          <input type="number" id="quoteDelivery" step="0.01" value="0" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Documentation Fee (₱)</label>
          <input type="number" id="quoteDocs" step="0.01" value="0" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Handling Fee (₱)</label>
          <input type="number" id="quoteHandling" step="0.01" value="0" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Value-Added Tax / VAT (₱)</label>
        <input type="number" id="quoteVat" step="0.01" value="0" class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500" oninput="calculateQuoteTotal()">
      </div>

      <!-- Price Breakdown Summary Box -->
      <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1.5 text-xs">
        <div class="flex justify-between text-slate-600">
          <span>Base Freight:</span>
          <span id="summaryBase" class="font-medium">₱0.00</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Total Other Charges (Trucking/Docs/Handling):</span>
          <span id="summaryFees" class="font-medium">₱0.00</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Discount:</span>
          <span id="summaryDiscount" class="font-medium text-rose-600">-₱0.00</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Value-Added Tax:</span>
          <span id="summaryVat" class="font-medium">₱0.00</span>
        </div>
        <div class="border-t border-slate-200 pt-1.5 flex justify-between text-slate-900 font-bold text-sm">
          <span>Total Quotation:</span>
          <span id="summaryTotal" class="text-indigo-600">₱0.00</span>
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Quotation Validity Date</label>
        <input type="date" id="quoteValidUntil" required class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">Terms & Remarks</label>
        <textarea id="quoteRemarks" rows="2" placeholder="e.g. Price inclusive of fuel surcharge and handling fees..." class="w-full text-xs p-2.5 rounded-xl border border-slate-200 focus:ring-indigo-500"></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" onclick="closeSendQuoteModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-md flex items-center gap-1.5">
          <i class="fa-solid fa-paper-plane text-xs"></i> Generate & Send PDF
        </button>
      </div>
    </form>

  </div>
</div>