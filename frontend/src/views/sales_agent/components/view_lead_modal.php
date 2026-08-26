<!-- UNIFIED LEAD DETAILS, CONTACT & STATUS MODAL -->
<div id="viewModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4 transition-all">
  <div class="bg-white rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-200">
    
    <!-- CLOSE BUTTON -->
    <button type="button" onclick="closeViewModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition-colors">
      <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- MODAL HEADER -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
      <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-bold">
        <i class="fa-solid fa-folder-open"></i>
      </div>
      <div>
        <h3 class="text-base font-bold text-slate-900" id="modalCompany">Company Name</h3>
        <p class="text-xs text-indigo-600 font-semibold" id="modalCode">INQ-CODE</p>
      </div>
    </div>

    <!-- FORM WRAPPER (SAKOP ANG BUONG 2-COLUMN GRID) -->
    <form id="statusUpdateForm" onsubmit="handleStatusUpdate(event)">
      <input type="hidden" id="modalLeadId" value="">

      <!-- 2-COLUMN GRID LAYOUT -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- LEFT COLUMN: LEAD INFORMATION & CONTACT OPTIONS -->
        <div class="space-y-4">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Lead & Contact Information</h4>
          
          <!-- LEAD DATA CONTAINER -->
          <div class="space-y-2.5 text-xs text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100">
            <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
              <span class="font-medium text-slate-400">Contact Person:</span>
              <span class="font-bold text-slate-800" id="modalContact">--</span>
            </div>

            <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
              <span class="font-medium text-slate-400">Email Address:</span>
              <span class="font-semibold text-slate-800" id="modalEmail">--</span>
            </div>

            <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
              <span class="font-medium text-slate-400">Phone Number:</span>
              <span class="font-semibold text-slate-800" id="modalPhone">--</span>
            </div>

            <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
              <span class="font-medium text-slate-400">Platform:</span>
              <span class="font-semibold text-slate-800" id="modalPlatform">--</span>
            </div>

            <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
              <span class="font-medium text-slate-400">Service Requested:</span>
              <span class="font-semibold text-slate-800" id="modalService">--</span>
            </div>

            <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
              <span class="font-medium text-slate-400">Route:</span>
              <span class="font-semibold text-slate-800" id="modalRoute">--</span>
            </div>
          </div>

          <!-- QUICK CONTACT ACTION BUTTONS -->
          <div class="space-y-2 pt-1">
            <label class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Direct Actions</label>
            
            <div class="grid grid-cols-2 gap-2">
              <!-- SEND EMAIL -->
              <a id="contactModalEmailBtn" href="#" target="_blank" class="flex items-center gap-2.5 p-2.5 bg-slate-50 hover:bg-purple-50 hover:border-purple-200 border border-slate-200 rounded-xl transition group">
                <div class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L12 9.545l8.073-6.052C21.69 2.28 24 3.434 24 5.457z"/>
                  </svg>
                </div>
                <div class="overflow-hidden">
                  <div class="text-[11px] font-bold text-slate-800 group-hover:text-purple-700 truncate">Send Email</div>
                  <div class="text-[10px] text-slate-400 truncate" id="contactModalEmailText">email@example.com</div>
                </div>
              </a>

              <!-- CALL PHONE -->
              <a id="contactModalPhoneBtn" href="#" class="flex items-center gap-2.5 p-2.5 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 border border-slate-200 rounded-xl transition group">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                </div>
                <div class="overflow-hidden">
                  <div class="text-[11px] font-bold text-slate-800 group-hover:text-emerald-700 truncate">Call Phone</div>
                  <div class="text-[10px] text-slate-400 truncate" id="contactModalPhoneText">+63 900 000 0000</div>
                </div>
              </a>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN: UPDATE INPUTS (CARGO, STATUS, PRICE, PICKUP) -->
        <div class="space-y-4">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Update Lead & Quote</h4>

          <div class="space-y-4 bg-white p-4 rounded-2xl border border-slate-100">
            <!-- EDITABLE CARGO DETAILS -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Cargo Details</label>
              <textarea 
                id="modalCargo" 
                name="cargo_details" 
                rows="3" 
                placeholder="Enter cargo details..." 
                class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-slate-700"
              ></textarea>
            </div>

            <!-- STATUS SELECT -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Update Status</label>
              <select id="modalStatusSelect" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                <option value="new_inquiry">NEW INQUIRY</option>
                <option value="qualifying">QUALIFYING</option>
                <option value="quote_sent">QUOTE SENT</option>
                <option value="negotiation">NEGOTIATION</option>
                <option value="closed_won">CLOSED WON</option>
                <option value="closed_lost">CLOSED LOST</option>
              </select>
            </div>

            <!-- PRICE / QUOTE -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Agreed Price / Quote (₱)</label>
              <input 
                type="number" 
                step="0.01" 
                id="modalPriceInput" 
                placeholder="0.00" 
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
              >
            </div>

            <!-- DYNAMIC PICKUP FIELDS SECTION -->
            <div id="pickupFieldsSection" class="hidden space-y-3 pt-3 border-t border-slate-100">
              <div class="text-xs font-bold text-indigo-600 flex items-center gap-1.5">
                <i class="fa-solid fa-truck-ramp-box"></i> Pickup Details <span class="text-rose-500">*</span>
              </div>
              
              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Full Pickup Address</label>
                <textarea 
                  id="modalPickupAddress"
                  name="pickup_address" 
                  rows="2" 
                  placeholder="Enter complete street address, landmark, floor/unit..."
                  class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                ></textarea>
              </div>

              <div>
                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Pickup Date & Time</label>
                <input 
                  type="datetime-local" 
                  id="modalPickupDateTime" 
                  name="pickup_datetime"
                  class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                />
              </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all shadow-md shadow-indigo-100 active:scale-95 flex items-center justify-center gap-2 mt-2">
              <i class="fa-solid fa-floppy-disk"></i> Save Status Update
            </button>
          </div>
        </div>

      </div>
    </form>

  </div>
</div>