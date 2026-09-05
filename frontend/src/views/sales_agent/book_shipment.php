<?php
$page_title = "Book New Shipment - Sales Agent Dashboard";
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<!-- MAIN CONTENT AREA -->
<main data-brand="priority" class="flex-1 overflow-y-auto bg-slate-50 p-6 lg:p-8">

  <?php 
  $header_title = "Book New Shipment";
  $header_subtitle = "Initiate a new shipment booking by completing the steps below.";
  include_once 'components/dashboard_header.php'; 
  ?>
  
  <div class="p-6 lg:p-8">

    <!-- STEPPER PROGRESS BAR -->
    <div class="mb-8 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 text-brand-blue">
                <span class="w-10 h-10 rounded-full bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-box"></i></span>
                <span class="font-semibold text-slate-900">Cargo</span>
            </div>
            <div class="flex-1 h-px bg-slate-200 mx-4"></div>
            <div class="flex items-center gap-3 text-slate-400">
                <span class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-truck-fast"></i></span>
                <span class="font-medium">Route</span>
            </div>
            <div class="flex-1 h-px bg-slate-200 mx-4"></div>
            <div class="flex items-center gap-3 text-slate-400">
                <span class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-file-invoice"></i></span>
                <span class="font-medium">Submit</span>
            </div>
        </div>
    </div>

    <!-- BOOKING FORM -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- FORM FIELDS -->
        <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-box-open text-brand-blue"></i>
                Cargo Information
            </h2>
            <form id="bookShipmentForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cargo Type</label>
                        <select class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none transition-all">
                            <option>40ft Container · Dry Van</option>
                            <option>20ft Container · Reefer</option>
                            <option>LCL · Break-bulk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Weight (kg)</label>
                        <input type="number" placeholder="e.g. 5000" class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cargo Description</label>
                    <textarea rows="3" class="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none transition-all" placeholder="Enter cargo details..."></textarea>
                </div>
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="button" class="px-6 py-3 bg-brand-blue text-white rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        Next Step <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- SUMMARY CARD -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm sticky top-8">
                <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-slate-400"></i>
                    Booking Summary
                </h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cargo Type:</span>
                        <span class="font-semibold text-slate-900">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Weight:</span>
                        <span class="font-semibold text-slate-900">-</span>
                    </div>
                    <div class="border-t border-slate-100 pt-4 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium">Estimated Cost:</span>
                            <span class="text-xl font-bold text-brand-blue font-mono">₱0.00</span>
                        </div>
                    </div>
                </div>
                <button class="w-full mt-8 py-3 bg-slate-50 text-slate-600 rounded-xl font-semibold hover:bg-slate-100 transition-all border border-slate-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save as Draft
                </button>
            </div>
        </div>
    </div>
</div>

</main>

<?php include_once '../../includes/footer.php'; ?>
