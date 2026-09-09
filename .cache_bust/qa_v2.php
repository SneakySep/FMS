<?php
/* ==========================================================================
    OVERVIEW  —  the quick-actions card of dashboard.php (OverviewModule)
    --------------------------------------------------------------------------
    Carved 1:1 out of the old dashboard.php "ROW 5" quick-actions column.

    Reads: $active_count and $fleet_available, both from DemoData::kpis(), for
    the two figures printed inside the copy.

    Its action buttons used to be anchor jumps to #book-delivery / #fleet /
    #active-deliveries on the same page. Now that each section is a page of its
    own they are real links, which is the only edit made to this block - see the
    hrefs below.
    -------------------------------------------------------------------------- */
?>

                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="lg:col-span-4 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Quick Actions</h2>
                        <span class="crm-panel-sub">Everything the desk does most</span>
                    </div>
                </div>
                <div class="crm-panel-body grid grid-cols-2 gap-3">
                    <a href="#book-delivery" class="dlv-vehicle !flex-col !items-start !gap-2">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-bolt"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Book courier</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Instant dispatch</span>
                        </span>
                    </a>
                    <a href="#live-tracking" class="dlv-vehicle !flex-col !items-start !gap-2">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-map-location-dot"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Live map</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= $active_count ?> routes moving</span>
                        </span>
                    </a>
                    <a href="#fleet" class="dlv-vehicle !flex-col !items-start !gap-2">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-warehouse"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Fleet board</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= $fleet_available ?> vehicles free</span>
                        </span>
                    </a>
                    <button type="button" id="openRatingBtnAlt" class="dlv-vehicle !flex-col !items-start !gap-2 text-left">
                        <span class="dlv-vehicle-ico"><i class="fa-solid fa-star"></i></span>
                        <span>
                            <span class="block text-xs font-bold" style="color: var(--fg-heading);">Rate a drop</span>
                            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Leave feedback</span>
                        </span>
                    </button>
                </div>
                <div class="px-5 pb-5">
                    <div class="rounded-xl border p-4" style="border-color: var(--line); background: var(--brand-soft);">
                        <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Saved on this device</span>
                        <p class="text-xs font-semibold mt-1 leading-snug" style="color: var(--fg-body);">
                            <span id="localBookingsCount">0</span> booking(s) and <span id="localRatingsCount">0</span> rating(s) you submitted.
                            <button type="button" id="clearLocalBtn" class="font-bold underline" style="color: var(--danger);">Clear</button>
                        </p>
                    </div>
                </div>
            </div>
        </section>
