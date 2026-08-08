<?php
$pageTitle = 'SwiftFreight - Document Vault Control';
$activePage = 'documents';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help with document vault control? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Document Vault Control</h2>
            <div class="flex-1 max-w-md mx-8"><div class="relative"><i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i><input type="text" id="docSearchInput" onkeyup="searchDocVault()" placeholder="Search documents..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all"></div></div>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors"><i class="fa-solid fa-bell text-xs"></i><span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span></button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">Help Desk <i class="fa-solid fa-headset text-xs"></i></button>
                <button onclick="openModal('newDocumentModal')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Document</button>
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            <!-- FILTER TABS -->
            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <button onclick="filterDocuments('all', this)" class="doc-filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm">All</button>
                <button onclick="filterDocuments('bill-lading', this)" class="doc-filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">Bill of Lading</button>
                <button onclick="filterDocuments('customs', this)" class="doc-filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">Customs</button>
                <button onclick="filterDocuments('proof-delivery', this)" class="doc-filter-tab bg-white text-slate-600 border border-slate-200 px-4 py-2 rounded-xl">Proof of Delivery</button>
            </div>

            <!-- DOCUMENTS CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Customer Document Vault</h3>
                    <p class="text-xs text-slate-400">Documents published here appear instantly in the customer portal</p>
                </div>
                <div id="documentsContainer" class="space-y-3"></div>
            </div>
        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- UPLOAD DOCUMENT MODAL -->
    <div id="newDocumentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 space-y-6">
            <div class="flex justify-between items-start"><div><h3 class="text-base font-extrabold text-slate-900">Upload Document to Customer Vault</h3><p class="text-xs text-slate-400">Published instantly to the customer portal</p></div><button onclick="closeModal('newDocumentModal')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="fa-solid fa-xmark"></i></button></div>
            <form onsubmit="createNewDocument(event)" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Document Name</label>
                    <input type="text" id="newDocName" required placeholder="e.g. Bill of Lading — WB-208841" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                        <select id="newDocCategory" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                            <option value="bill-lading">Bill of Lading</option>
                            <option value="customs">Customs Declaration</option>
                            <option value="proof-delivery">Proof of Delivery</option>
                            <option value="invoice">Invoice</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">File Type</label>
                        <select id="newDocType" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:border-brand-blue focus:outline-none transition-colors cursor-pointer">
                            <option value="PDF">PDF</option>
                            <option value="DOCX">DOCX</option>
                            <option value="XLSX">XLSX</option>
                            <option value="JPG">JPG</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" onclick="closeModal('newDocumentModal')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-6 py-2.5 rounded-xl transition-colors shadow-md shadow-blue-500/20">Upload & Publish</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>