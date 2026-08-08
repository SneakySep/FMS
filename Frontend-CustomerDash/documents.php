<?php
$pageTitle = 'SwiftFreight - Document Vault';
$activePage = 'documents';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help locating or uploading a Bill of Lading or Customs Clearance form? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Documents</h2>

            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="docSearchInput" onkeyup="searchDocVault()" placeholder="Track, a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">
                    Help Desk <i class="fa-solid fa-headset text-xs"></i>
                </button>
                <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + Book Shipment
                </button>
            </div>
        </header>

        <!-- DOCUMENTS CONTENT BODY -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <!-- DOCUMENT CATEGORY FILTER TABS -->
            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-200/50 rounded-2xl w-fit text-xs font-semibold">
                <button onclick="filterDocuments('all', this)" class="doc-filter-tab bg-brand-blue text-white px-4 py-2 rounded-xl shadow-sm transition-all">All</button>
                <button onclick="filterDocuments('bol', this)" class="doc-filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Bills of Lading</button>
                <button onclick="filterDocuments('customs', this)" class="doc-filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Customs Forms</button>
                <button onclick="filterDocuments('pod', this)" class="doc-filter-tab bg-white text-slate-600 hover:text-slate-900 px-4 py-2 rounded-xl transition-all">Proof of Delivery</button>
            </div>

            <!-- DOCUMENT VAULT CARD -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Document Vault</h3>
                        <p class="text-xs text-slate-400">All shipment paperwork in one place</p>
                    </div>
                    <button onclick="triggerUploadDoc()" class="text-xs font-semibold text-brand-blue hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload
                    </button>
                </div>

                <!-- DOCUMENT LIST -->
                <div class="space-y-3" id="docListContainer">
                    
                    <!-- Document 1: Bill of Lading -->
                    <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer" 
                         data-category="bol" 
                         onclick="alert('Downloading Bill of Lading — WB-208841 (PDF)...')">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors">Bill of Lading — WB-208841</h4>
                                <span class="text-[10px] text-slate-400 font-medium">PDF · Uploaded Jul 26</span>
                            </div>
                        </div>
                        <button class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                            <i class="fa-solid fa-arrow-down-long text-xs"></i>
                        </button>
                    </div>

                    <!-- Document 2: Customs Declaration -->
                    <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer" 
                         data-category="customs" 
                         onclick="alert('Downloading Customs Declaration — WB-208835 (PDF)...')">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors">Customs Declaration — WB-208835</h4>
                                <span class="text-[10px] text-slate-400 font-medium">PDF · Uploaded Jul 25</span>
                            </div>
                        </div>
                        <button class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                            <i class="fa-solid fa-arrow-down-long text-xs"></i>
                        </button>
                    </div>

                    <!-- Document 3: Proof of Delivery -->
                    <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer" 
                         data-category="pod" 
                         onclick="alert('Downloading Proof of Delivery — WB-208790 (PDF)...')">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors">Proof of Delivery — WB-208790</h4>
                                <span class="text-[10px] text-slate-400 font-medium">PDF · Uploaded Jul 25</span>
                            </div>
                        </div>
                        <button class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                            <i class="fa-solid fa-arrow-down-long text-xs"></i>
                        </button>
                    </div>

                    <!-- Document 4: Packing List -->
                    <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer" 
                         data-category="customs" 
                         onclick="alert('Downloading Packing List — WB-208712 (XLSX)...')">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm">
                                <i class="fa-solid fa-file-excel"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors">Packing List — WB-208712</h4>
                                <span class="text-[10px] text-slate-400 font-medium">XLSX · Uploaded Jul 24</span>
                            </div>
                        </div>
                        <button class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                            <i class="fa-solid fa-arrow-down-long text-xs"></i>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- Hidden File Input for Upload Simulation -->
    <input type="file" id="hiddenFileInput" class="hidden" onchange="handleFileSelected(event)">

    <!-- Scripts -->
    <script src="js/main.js"></script>
    <script src="js/store-bridge.js"></script>
</body>
</html>
