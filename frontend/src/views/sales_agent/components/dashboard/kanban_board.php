<div class="bg-white border border-slate-200 p-6 sm:p-8 rounded-3xl shadow-sm mb-8 font-sans text-[#1A1A1A]">
    
    <!-- BOARD HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Sales Pipeline Kanban</h3>
            <p class="text-xs text-slate-500">I-drag ang cards para i-update ang status o pindutin ang 3 dots para sa full details</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/80 w-fit">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Live Sync Enabled</span>
        </div>
    </div>

    <!-- KANBAN BOARD COLUMNS -->
    <div class="flex gap-6 overflow-x-auto pb-4 custom-scrollbar">
        <?php
        $columns = [
            ['key' => 'new_inquiry', 'title' => 'New Inquiry'],
            ['key' => 'qualifying',  'title' => 'Qualifying'],
            ['key' => 'negotiation', 'title' => 'Negotiation'],
            ['key' => 'closed_won',  'title' => 'Closed Won'],
            ['key' => 'closed_lost', 'title' => 'Closed Lost'],
        ];

        foreach ($columns as $col):
            $items = $kanban_columns[$col['key']] ?? [];
            $count = is_array($items) ? count($items) : 0;
        ?>
            <!-- COLUMN -->
            <div class="flex-none w-80 flex flex-col gap-4 bg-slate-50/70 p-4 rounded-2xl border border-slate-200/60">
                
                <!-- COLUMN HEADER -->
                <div class="flex items-center justify-between px-1">
                    <h4 class="text-base font-bold tracking-tight text-[#1A1A1A]"><?= $col['title'] ?></h4>
                    <div class="flex items-center gap-2 border border-[#E5E5DF] bg-white px-3 py-1 rounded-xl text-xs font-semibold shadow-2xs">
                        <span id="count-<?= $col['key'] ?>"><?= $count ?></span>
                        <svg class="w-3.5 h-3.5 text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                </div>

                <!-- CARDS CONTAINER -->
                <div id="col-<?= $col['key'] ?>" data-status="<?= $col['key'] ?>" class="kanban-dropzone flex flex-col gap-4 min-h-[500px]">
                    <?php 
                    // Kung sakaling may laman na agad galing PHP, i-render na natin para lumitaw agad ang cards
                    if (!empty($items) && is_array($items)):
                        foreach ($items as $item):
                            $contactName = $item['contact_person'] ?? $item['client_name'] ?? 'Unknown Contact';
                            $companyName = $item['company_name'] ?? $item['company'] ?? 'Client Inquiry';
                            $cargoDetails = $item['cargo_details'] ?? $item['description'] ?? $item['service_type'] ?? 'No cargo details provided.';
                            $pickupAddress = $item['pickup_address'] ?? $item['origin'] ?? 'N/A';
                            $email = $item['email'] ?? 'N/A';
                            $phone = $item['phone_number'] ?? $item['phone'] ?? 'N/A';
                            $amount = isset($item['estimated_amount']) ? number_format($item['estimated_amount']) : '0';
                            $initial = strtoupper(substr($contactName, 0, 1));
                            $formattedDate = isset($item['created_at']) ? date('d M', strtotime($item['created_at'])) : 'No date';
                            $itemId = $item['id'] ?? '';
                    ?>
                        <div id="card-<?= $itemId ?>" data-id="<?= $itemId ?>" class="kanban-card bg-white text-[#1A1A1A] p-5 rounded-2xl shadow-sm border border-[#E5E5DF] hover:shadow-md transition-all duration-200 cursor-grab active:cursor-grabbing flex flex-col justify-between gap-4">
                          <div class="flex items-start justify-between gap-2">
                            <h5 id="title-<?= $itemId ?>" class="font-semibold text-base tracking-tight text-[#1A1A1A] leading-snug"><?= htmlspecialchars($companyName) ?></h5>
                            <button type="button" onclick="toggleCardDetails('<?= $itemId ?>')" id="btn-<?= $itemId ?>" class="text-[#666666] hover:text-[#1A1A1A] p-1 rounded-lg hover:bg-[#F0F0EC] transition-colors flex-shrink-0">
                              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                              </svg>
                            </button>
                          </div>
                          <p id="desc-<?= $itemId ?>" class="text-xs text-[#666666] leading-relaxed font-normal line-clamp-2"><?= htmlspecialchars($cargoDetails) ?></p>
                          
                          <div id="details-<?= $itemId ?>" class="hidden flex-col gap-2.5 pt-3 border-t border-gray-700 text-xs text-gray-300">
                            <div class="flex items-start gap-2">
                              <span class="break-words"><?= htmlspecialchars($pickupAddress) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                              <span class="truncate"><?= htmlspecialchars($email) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                              <span><?= htmlspecialchars($phone) ?></span>
                            </div>
                          </div>

                          <div class="flex items-center gap-3 pt-1">
                            <div id="avatar-<?= $itemId ?>" class="w-8 h-8 rounded-full bg-[#1A1A1A] text-white flex items-center justify-center font-medium text-xs flex-shrink-0"><?= $initial ?></div>
                            <div class="flex flex-col">
                              <span id="label-<?= $itemId ?>" class="text-[10px] text-[#888888] uppercase tracking-wider font-semibold">Contact Person</span>
                              <span id="person-<?= $itemId ?>" class="text-xs font-semibold text-[#1A1A1A]"><?= htmlspecialchars($contactName) ?></span>
                            </div>
                          </div>

                          <div id="footer-<?= $itemId ?>" class="flex items-center justify-between border-t border-[#F0F0EC] pt-3 text-xs text-[#666666]">
                            <div id="date-badge-<?= $itemId ?>" class="flex items-center gap-1.5 border border-[#E5E5DF] px-2.5 py-1 rounded-lg bg-white">
                              <span class="text-xs font-medium"><?= $formattedDate ?></span>
                            </div>
                            <span id="amount-<?= $itemId ?>" class="text-xs font-semibold text-emerald-600">₱<?= $amount ?></span>
                          </div>
                        </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>