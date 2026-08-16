<?php
$pageTitle = 'Customer Accounts - Priority Handling Logistics';
$activePage = 'customers';
$chatMessage = trim(<<<'MSG'
Mabuhay! Internal Priority Handling Logistics support channel; how can we help?
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>

<main class="flex-1 flex flex-col min-w-0 bg-[#f4f7fa] dark:bg-[#0a1628]">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h2 class="text-2xl font-bold italic text-slate-900 tracking-wide">Customer</h2>

            <div class="flex items-center gap-4">
                <!-- Search Input -->
                <div class="relative w-80">
                    <i class="fa-solid fa-magnifying-glass w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        placeholder="Search leads, customer, quotes..." 
                        class="w-full pl-9 pr-4 py-1.5 text-sm bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue text-slate-700"
                    >
                </div>

                <!-- Notification Bell -->
                <button class="p-2 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 relative">
                    <i class="fa-solid fa-bell w-4 h-4"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- New Quote Button -->
                <button onclick="openModal('newCustomerModal')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-medium text-sm px-4 py-2 rounded-md transition flex items-center gap-1.5 shadow-sm">
<i class="fa-solid fa-plus w-4"></i>
<span>Add Customer</span>
</button>
            </div>
        </header>

        <!-- CONTENT BODY CONTAINER -->
        <div class="p-8 w-full">

            <!-- MAIN CUSTOMER CARD -->
            <div class="bg-white rounded-xl border border-gray-300/80 shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-slate-900 mb-0.5">My Customer Accounts</h3>
                    <p class="text-xs text-gray-400">Accounts you've onboarded or manage</p>
                </div>

                <!-- CUSTOMER TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-semibold text-gray-400 tracking-wider">
                                <th class="py-3.5 px-6">COMPANY</th>
                                <th class="py-3.5 px-6">CONTACT</th>
                                <th class="py-3.5 px-6">ACCOUNT STATUS</th>
                                <th class="py-3.5 px-6">SHIPMENTS</th>
                                <th class="py-3.5 px-6 text-right">LIFETIME VALUE</th>
<th class="py-3.5 px-6 text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody" class="divide-y divide-gray-200 text-sm text-slate-800">
<!-- Rows rendered by js/main.js -->
</tbody>
                    </table>
                </div>

                <!-- Bottom empty padding inside card -->
                <div class="p-12"></div>
            </div>

        </div>
    
</main>



    <!-- NEW CUSTOMER MODAL -->
    <div id="newCustomerModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 space-y-6">
            <div class="flex justify-between items-start"><div><h3 class="text-base font-extrabold text-slate-900">New Customer Account</h3><p class="text-xs text-slate-400">Portal access is enabled by default</p></div><button onclick="closeModal('newCustomerModal')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fa-solid fa-xmark"></i></button></div>
            <form onsubmit="createNewCustomer(event)" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Company Name</label><input type="text" id="newCustCompany" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors" placeholder="ACME Trading"></div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Contact Person</label><input type="text" id="newCustContact" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors" placeholder="J. Sison"></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Login Email</label>
                    <input type="email" id="newCustEmail" required placeholder="client@company.ph" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Default Warehouse</label>
                        <select id="newCustWarehouse" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                            <option value="Caloocan Hub">Caloocan Hub</option>
                            <option value="Cebu Gateway">Cebu Gateway</option>
                            <option value="Davao North">Davao North</option>
                            <option value="Clark Economic Zone">Clark Economic Zone</option>
                        </select>
                    </div>
                    <div><label class="block text-xs font-semibold text-slate-600 mb-1">Account Number</label><input type="text" id="newCustAccount" placeholder="Auto-generated" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors font-mono"></div>
                </div>
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="closeModal('newCustomerModal')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Create Customer</button>
                </div>
            </form>
        </div>
    </div>


<script src="js/main.js"></script>
</body>
</html>
