<?php
$page_title = "Documents · Priority Handling Logistics";
$activePage = 'documents';
require_once __DIR__ . '/../../includes/header.php';
include_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../helpers/api_helper.php';

// --- Fetch live documents from the backend API, with demo fallback ---
$docs_res  = make_api_request('/api/v1/portal/documents', 'GET');
$docs_raw  = $docs_res['data']['data'] ?? $docs_res['data'] ?? null;

if (!empty($docs_raw) && is_array($docs_raw)) {
    $documents = $docs_raw;
} else {
    // Demo fallback when API is unreachable
    $documents = [
        ['name' => 'PH-WB-208841 - BOL.pdf',        'type' => 'Bill of Lading',  'size' => '2.4 MB', 'date' => 'Jul 28, 2026', 'status' => 'uploaded'],
        ['name' => 'PH-WB-208841 - Invoice.pdf',     'type' => 'Invoice',        'size' => '1.1 MB', 'date' => 'Jul 28, 2026', 'status' => 'uploaded'],
        ['name' => 'PH-WB-208835 - POD.pdf',         'type' => 'Proof of Delivery','size' => '3.2 MB','date' => 'Jul 25, 2026', 'status' => 'uploaded'],
        ['name' => 'PH-WB-208812 - Customs.pdf',     'type' => 'Customs Clearance','size' => '1.8 MB','date' => 'Jul 23, 2026', 'status' => 'uploaded'],
        ['name' => 'PH-WB-208712 - Insurance.pdf',   'type' => 'Insurance Cert', 'size' => '0.9 MB', 'date' => 'Jul 20, 2026', 'status' => 'uploaded'],
        ['name' => 'Service Agreement 2026',         'type' => 'Contract',       'size' => '2.1 MB', 'date' => 'Jan 15, 2026', 'status' => 'pending'],
    ];
}

// Derive counts for KPI cards
$total_docs  = count($documents);
$pending_count = 0;
foreach ($documents as $doc) {
    if (($doc['status'] ?? '') === 'pending') $pending_count++;
}
$uploaded_count = $total_docs - $pending_count;
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER BAR -->
        <?php
        $pageTitle    = 'Documents';
        $pageSubtitle = 'Shipment paperwork, contracts & certificates';
        $headerSearch = [
            'id'          => 'docSearchInput',
            'onkeyup'     => 'searchDocVault()',
            'placeholder' => 'Search a waybill, invoice, or document...',
        ];
        ob_start(); ?>
        <button onclick="toggleChat()" class="crm-btn crm-btn-ghost !h-9 !px-3.5 !text-xs">
            <span class="hidden sm:inline">Help Desk</span>
            <i class="fa-solid fa-headset text-xs"></i>
        </button>
        <button onclick="triggerUploadDoc()" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
            <span class="hidden sm:inline">Upload</span>
        </button>
        <?php $headerActions = ob_get_clean();
        include_once __DIR__ . '/../../components/customer_header.php';
        ?>

        <!-- DOCUMENTS DASHBOARD BODY -->
        <div class="px-4 sm:px-6 lg:px-8 py-8 space-y-6 w-full">

            <!-- KPI STAT CARDS ROW -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Documents -->
                <div class="dashboard-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm fade-in">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">+3 this week</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-4">Total Documents</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight"><?= $total_docs ?></h3>
                </div>

                <!-- Pending Signatures -->
                <div class="dashboard-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm fade-in">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-100/70 text-amber-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fa-solid fa-signature"></i>
                        </div>
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Action needed</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-4">Pending Signatures</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight"><?= $pending_count ?></h3>
                </div>

                <!-- Shared with me -->
                <div class="dashboard-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm fade-in">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-purple-100/70 text-purple-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="text-[10px] font-bold text-purple-600 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded-full">5 shared</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-4">Shared with me</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">5</h3>
                </div>

                <!-- Expiring Soon -->
                <div class="dashboard-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm fade-in">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-rose-100/70 text-rose-600 flex items-center justify-center text-sm shadow-sm">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">Within 30 days</span>
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-4">Expiring Soon</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">3</h3>
                </div>
            </div>


            <!-- MAIN GRID: DOCUMENT VAULT + SIDEBAR WIDGETS -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- LEFT: DOCUMENT VAULT -->
                <div class="xl:col-span-2 space-y-6">

                    <!-- UPLOAD DROP-ZONE -->
                    <div id="uploadZone" onclick="triggerUploadDoc()"
                         class="border-2 border-dashed border-slate-300 bg-slate-50/60 hover:bg-blue-50/40 hover:border-brand-blue rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-center gap-4 text-center cursor-pointer transition-all fade-in">
                        <div class="w-12 h-12 rounded-2xl bg-brand-blue/10 text-brand-blue flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Drag &amp; drop files here, or <span class="text-brand-blue underline">browse</span></p>
                            <p class="text-[11px] text-slate-400 mt-0.5">PDF, XLSX, JPG or PNG &middot; up to 25&nbsp;MB per file</p>
                        </div>
                    </div>

                    <!-- CATEGORY FILTER TABS -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button onclick="filterDocuments('all', this)" class="doc-filter-tab crm-pill is-active">All <span class="opacity-80">18</span></button>
                        <button onclick="filterDocuments('bol', this)" class="doc-filter-tab crm-pill">Bills of Lading <span class="opacity-80">6</span></button>
                        <button onclick="filterDocuments('customs', this)" class="doc-filter-tab crm-pill">Customs Forms <span class="opacity-80">5</span></button>
                        <button onclick="filterDocuments('pod', this)" class="doc-filter-tab crm-pill">Proof of Delivery <span class="opacity-80">4</span></button>
                        <button onclick="filterDocuments('invoice', this)" class="doc-filter-tab crm-pill">Invoices <span class="opacity-80">3</span></button>
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
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm shrink-0">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors truncate">Bill of Lading — WB-208841</h4>
                                            <span class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100">BOL</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Signed</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">PDF · 1.2&nbsp;MB · Uploaded Jul 26</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button title="Preview" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors" onclick="event.stopPropagation(); alert('Preview — Bill of Lading WB-208841');">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button title="Download" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                    </button>
                                </div>
                            </div>


                            <!-- Document 2: Customs Declaration -->
                            <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer"
                                 data-category="customs"
                                 onclick="alert('Downloading Customs Declaration — WB-208835 (PDF)...')">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm shrink-0">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors truncate">Customs Declaration — WB-208835</h4>
                                            <span class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100">Customs</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">PDF · 840&nbsp;KB · Uploaded Jul 25</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button title="Preview" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors" onclick="event.stopPropagation(); alert('Preview — Customs Declaration WB-208835');">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button title="Download" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                    </button>
                                </div>
                            </div>


                            <!-- Document 3: Proof of Delivery -->
                            <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer"
                                 data-category="pod"
                                 onclick="alert('Downloading Proof of Delivery — WB-208790 (PDF)...')">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm shrink-0">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors truncate">Proof of Delivery — WB-208790</h4>
                                            <span class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">POD</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Signed</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">PDF · 610&nbsp;KB · Uploaded Jul 25</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button title="Preview" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors" onclick="event.stopPropagation(); alert('Preview — Proof of Delivery WB-208790');">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button title="Download" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                    </button>
                                </div>
                            </div>


                            <!-- Document 4: Packing List -->
                            <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer"
                                 data-category="customs"
                                 onclick="alert('Downloading Packing List — WB-208712 (XLSX)...')">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100/70 text-emerald-600 flex items-center justify-center text-sm shadow-sm shrink-0">
                                        <i class="fa-solid fa-file-excel"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors truncate">Packing List — WB-208712</h4>
                                            <span class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100">Customs</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Signed</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">XLSX · 320&nbsp;KB · Uploaded Jul 24</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button title="Preview" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors" onclick="event.stopPropagation(); alert('Preview — Packing List WB-208712');">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button title="Download" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                    </button>
                                </div>
                            </div>


                            <!-- Document 5: Commercial Invoice -->
                            <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer"
                                 data-category="invoice"
                                 onclick="alert('Downloading Commercial Invoice — INV-55821 (PDF)...')">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100/70 text-brand-blue flex items-center justify-center text-sm shadow-sm shrink-0">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors truncate">Commercial Invoice — INV-55821</h4>
                                            <span class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-purple-50 text-purple-600 border border-purple-100">Invoice</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Paid</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">PDF · 210&nbsp;KB · Uploaded Jul 22</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button title="Preview" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors" onclick="event.stopPropagation(); alert('Preview — Commercial Invoice INV-55821');">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button title="Download" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Document 6: Delivery Photo (image) -->
                            <div class="doc-item flex items-center justify-between p-4 bg-slate-50/70 border border-slate-100 rounded-xl hover:bg-blue-50/40 transition-colors group cursor-pointer"
                                 data-category="pod"
                                 onclick="alert('Downloading Delivery Photo — WB-208755 (JPG)...')">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100/70 text-rose-600 flex items-center justify-center text-sm shadow-sm shrink-0">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-slate-900 group-hover:text-brand-blue transition-colors truncate">Delivery Photo — WB-208755</h4>
                                            <span class="text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">POD</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100">Expiring</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-medium">JPG · 1.8&nbsp;MB · Uploaded Jul 18</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button title="Preview" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors" onclick="event.stopPropagation(); alert('Preview — Delivery Photo WB-208755');">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button title="Download" class="text-slate-400 group-hover:text-brand-blue p-2 transition-colors">
                                        <i class="fa-solid fa-arrow-down-long text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Empty State (shown when search/filter returns nothing) -->
                            <div id="docEmptyState" class="hidden flex-col items-center justify-center py-10 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                                    <i class="fa-solid fa-folder-open"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-700">No documents found</p>
                                <p class="text-[11px] text-slate-400 mt-1">Try a different search term or category filter.</p>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- RIGHT: SIDEBAR WIDGETS -->
                <div class="space-y-6">

                    <!-- STORAGE USAGE -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 fade-in">
                        <h3 class="text-base font-extrabold text-slate-900">Storage Usage</h3>
                        <p class="text-xs text-slate-400 mb-5">2.4&nbsp;GB of 5&nbsp;GB used</p>

                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-500 mb-1.5">
                                    <span><i class="fa-solid fa-file-pdf text-rose-500 mr-1"></i>Documents</span>
                                    <span>1.6 GB</span>
                                </div>
                                <div class="progress-bar-bg bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="progress-bar-fill bg-brand-blue h-full rounded-full" style="width: 66%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-500 mb-1.5">
                                    <span><i class="fa-solid fa-image text-purple-500 mr-1"></i>Images</span>
                                    <span>0.6 GB</span>
                                </div>
                                <div class="progress-bar-bg bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="progress-bar-fill bg-purple-500 h-full rounded-full" style="width: 25%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[11px] font-semibold text-slate-500 mb-1.5">
                                    <span><i class="fa-solid fa-file-zipper text-emerald-500 mr-1"></i>Archives</span>
                                    <span>0.2 GB</span>
                                </div>
                                <div class="progress-bar-bg bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="progress-bar-fill bg-emerald-500 h-full rounded-full" style="width: 9%"></div>
                                </div>
                            </div>
                        </div>

                        <button onclick="triggerUploadDoc()" class="mt-5 w-full text-xs font-semibold text-brand-blue border border-brand-blue/30 bg-blue-50 hover:bg-blue-100 rounded-xl py-2.5 transition-colors">
                            <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Add more files
                        </button>
                    </div>


                    <!-- NEEDS YOUR ATTENTION -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-extrabold text-slate-900">Needs your attention</h3>
                            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full">3</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-rose-50/60 border border-rose-100">
                                <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-clock"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">Insurance Cert — WB-208700</p>
                                    <p class="text-[10px] text-rose-500 font-medium">Expires in 6 days</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                                <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-signature"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">Customs Declaration — WB-208835</p>
                                    <p class="text-[10px] text-amber-600 font-medium">Awaiting your signature</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                                <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-file-signature"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">Service Agreement 2026</p>
                                    <p class="text-[10px] text-amber-600 font-medium">Signature requested</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- QUICK LINKS -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 fade-in">
                        <h3 class="text-base font-extrabold text-slate-900 mb-4">Quick Links</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="/invoices" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-file-invoice text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">Invoices</span>
                            </a>
                            <a href="/shipments" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-box text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">Shipments</span>
                            </a>
                            <a href="/sla-monitoring" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-gauge-high text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">SLA</span>
                            </a>
                            <a href="/notification" class="group flex flex-col items-center justify-center gap-2 p-3.5 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all text-slate-600">
                                <i class="fa-solid fa-bell text-base group-hover:scale-110 transition-transform"></i>
                                <span class="text-[11px] font-semibold">Alerts</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/../../components/chat_widget.php'; ?>

    <!-- Hidden File Input for Upload Simulation -->
    <input type="file" id="hiddenFileInput" class="hidden" onchange="handleFileSelected(event)">

    <!-- Scripts -->
    <script src="/assets/js/customer/customer_dashboard.js"></script>
    <script>
        /* Open the hidden file picker (wired to handleFileSelected in customer_dashboard.js) */
        function triggerUploadDoc() {
            var input = document.getElementById('hiddenFileInput');
            if (input) input.click();
        }
    </script>

<!-- FOOTER INCLUDE -->
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

