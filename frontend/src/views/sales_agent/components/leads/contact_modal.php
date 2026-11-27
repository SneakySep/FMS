<!-- CONTACT OPTIONS MODAL -->
<div id="contactOptionsModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 relative">
    <button onclick="closeContactModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-lg">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="text-center mb-5">
      <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
        <i class="fa-solid fa-address-book"></i>
      </div>
      <h3 class="text-base font-bold text-slate-800" id="contactModalCompany">Company Name</h3>
      <p class="text-xs text-slate-500 mt-0.5" id="contactModalPerson">Contact Person</p>
    </div>

    <div class="space-y-3">
      <!-- EMAIL -->
      <a id="contactModalEmailBtn" href="#" target="_blank" rel="noopener"
         class="w-full flex items-center justify-between p-3 bg-slate-50 hover:bg-purple-50 hover:border-purple-200 border border-slate-200 rounded-xl transition group">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div class="text-left">
            <div class="text-xs font-bold text-slate-800 group-hover:text-purple-700">Send Email</div>
            <div class="text-[11px] text-slate-400 truncate max-w-[180px]" id="contactModalEmailText">email@example.com</div>
          </div>
        </div>
        <span class="text-slate-400 group-hover:text-purple-600 text-xs">&#10132;</span>
      </a>

      <!-- PHONE -->
      <a id="contactModalPhoneBtn" href="#"
         class="w-full flex items-center justify-between p-3 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 border border-slate-200 rounded-xl transition group">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
            <i class="fa-solid fa-phone"></i>
          </div>
          <div class="text-left">
            <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Call Phone</div>
            <div class="text-[11px] text-slate-400 truncate max-w-[180px]" id="contactModalPhoneText">+63 900 000 0000</div>
          </div>
        </div>
        <span class="text-slate-400 group-hover:text-emerald-600 text-xs">&#10132;</span>
      </a>
    </div>
  </div>
</div>

<script src="../../../assets/js/sales_agent/contact_modal.js"></script>
