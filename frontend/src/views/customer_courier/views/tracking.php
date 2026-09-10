<?php
/* ==========================================================================
    TRACKING  —  tracking.php  (TrackingModule::render())
    --------------------------------------------------------------------------
    Carved 1:1 out of the old dashboard.php "ROW 2: LIVE TRACKING" section, so
    the map card, the courier telemetry and the milestone timeline are the exact
    markup that shipped before - only the enclosing page changed.

    Reads: $delivery (the waybill record), $vehicle (its label + icon from
    DemoData::vehicleMeta()), $milestones (the timeline). TrackingModule::prepare()
    resolves ?wb= through DemoData::find() and falls back to the first open job,
    so a hand-typed tracking.php shows the same WB-90412 story it always did.

    js/dashboard.js owns the rAF walk along the polyline, the
    pause / recenter buttons and every telemetry node (#trackEta,
    #trackDistance, #trackProgress*, #trackSpeed, #trackStop and the
    .dlv-ms-item dots). It boots only when #trackingMap exists, so this view can
    stand alone without the booking or table code coming along with it.
    -------------------------------------------------------------------------- */
?>

        <!-- ROW 2: LIVE TRACKING  —  map + courier telemetry + milestones -->
        <section id="live-tracking" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start scroll-mt-24">

            <!-- MAP CARD -->
            <div class="lg:col-span-8 crm-card overflow-hidden">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Live Tracking</h2>
                        <span class="crm-panel-sub">
                            <span class="crm-badge crm-badge-amber"><span class="crm-badge-dot"></span> Courier moving</span>
                            <span class="ml-1">Waybill <span id="trackWaybill" class="font-semibold text-navy-700 dark:text-slate-200"><?= htmlspecialchars($delivery['id']) ?></span> &middot; <?= htmlspecialchars($delivery['from']) ?> to <?= htmlspecialchars($delivery['to']) ?></span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="trackPauseBtn" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]">
                            <i class="fa-solid fa-pause text-[10px]"></i> Pause
                        </button>
                        <button type="button" id="trackRecenterBtn" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[10px]" title="Recenter on courier">
                            <i class="fa-solid fa-crosshairs"></i>
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Map surface -->
                    <div class="relative h-[340px] sm:h-[400px] rounded-xl overflow-hidden border border-line">
                        <div id="trackingMap" class="dlv-map"></div>
                        <!-- Floating ETA chip -->
                        <div class="absolute top-3 left-3 z-[500] crm-card !rounded-xl px-3 py-2 flex items-center gap-2.5">
                            <span class="crm-kpi-ico !w-8 !h-8 !text-[13px]"><i class="fa-solid fa-stopwatch"></i></span>
                            <div class="leading-tight">
                                <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Arrival in</span>
                                <span id="trackEta" class="block text-base font-black" style="color: var(--fg-heading);"><?= htmlspecialchars($delivery['eta']) ?></span>
                            </div>
                        </div>
                        <!-- Vehicle chip -->
                        <div class="absolute top-3 right-3 z-[500] crm-badge crm-badge-navy !rounded-xl !px-3 !py-2">
                            <i class="fa-solid <?= htmlspecialchars($vehicle['icon']) ?> text-sm"></i>
                            <span class="text-[11px]"><?= htmlspecialchars($vehicle['label']) ?> &middot; <?= htmlspecialchars($delivery['courier']) ?></span>
                        </div>
                    </div>

                    <!-- Progress rail -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider" style="color: var(--fg-muted);">
                            <span>Route progress</span>
                            <span><span id="trackProgressPct"><?= (int) $delivery['progress'] ?></span>% &middot; <span id="trackDistance"><?= htmlspecialchars($delivery['distance']) ?></span> left</span>
                        </div>
                        <div class="dlv-bar !h-2">
                            <div id="trackProgressBar" class="dlv-bar-fill !bg-brand-blue" style="width: <?= (int) $delivery['progress'] ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- COURIER TELEMETRY -->
            <div class="lg:col-span-4 space-y-6">
                <div class="crm-card">
                    <div class="crm-panel-head">
                        <div>
                            <h2 class="crm-panel-title">Assigned Courier</h2>
                            <span class="crm-panel-sub">Live from the dispatch board</span>
                        </div>
                        <span class="crm-badge crm-badge-green"><span class="crm-badge-dot"></span> On route</span>
                    </div>
                    <div class="crm-panel-body space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="crm-avatar !w-11 !h-11 !rounded-xl !text-sm"><?= htmlspecialchars(strtoupper(substr(str_replace(['. ', '.'], ['', ' '], $delivery['courier']), 0, 2))) ?></span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate" style="color: var(--fg-heading);"><?= htmlspecialchars($delivery['courier']) ?></p>
                                <span class="crm-panel-sub"><?= htmlspecialchars($vehicle['label']) ?> &middot; <?= htmlspecialchars($delivery['id']) ?></span>
                            </div>
                            <div class="ml-auto text-right">
                                <span class="dlv-stars" aria-hidden="true">
                                    <i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i><i class="fa-solid fa-star is-on"></i>
                                </span>
                                <span class="block text-[10px] font-bold" style="color: var(--fg-muted);">4.9 &middot; 312 drops</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-xl border border-line p-2.5" style="background: var(--surface-muted);">
                                <span class="block text-[9px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Speed</span>
                                <span id="trackSpeed" class="block text-sm font-black" style="color: var(--fg-heading);">28</span>
                                <span class="block text-[9px] font-bold" style="color: var(--fg-muted);">km/h</span>
                            </div>
                            <div class="rounded-xl border border-line p-2.5" style="background: var(--surface-muted);">
                                <span class="block text-[9px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Stop</span>
                                <span id="trackStop" class="block text-sm font-black" style="color: var(--fg-heading);">1</span>
                                <span class="block text-[9px] font-bold" style="color: var(--fg-muted);">of 3</span>
                            </div>
                            <div class="rounded-xl border border-line p-2.5" style="background: var(--surface-muted);">
                                <span class="block text-[9px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Parcels</span>
                                <span class="block text-sm font-black" style="color: var(--fg-heading);"><?= (int) $delivery['slots'] ?></span>
                                <span class="block text-[9px] font-bold" style="color: var(--fg-muted);"><?= htmlspecialchars($delivery['weight']) ?></span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="crm-btn crm-btn-primary flex-1 !h-9 !text-xs">
                                <i class="fa-solid fa-phone text-[10px]"></i> Call courier
                            </button>
                            <button type="button" class="crm-btn crm-btn-ghost !h-9 !text-xs" title="Message courier">
                                <i class="fa-solid fa-comment-dots text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>


                <!-- MILESTONE TIMELINE -->
                <div class="crm-card">
                    <div class="crm-panel-head">
                        <div>
                            <h2 class="crm-panel-title">Milestones</h2>
                            <span class="crm-panel-sub">Advances as the courier moves</span>
                        </div>
                    </div>
                    <div class="crm-panel-body">
                        <ol id="trackTimeline" class="space-y-4">
                            <?php foreach ($milestones as $ms_idx => $ms): ?>
                                <?php $ms_last = ($ms_idx === count($milestones) - 1); ?>
                                <li class="dlv-ms-item flex items-start gap-3" data-ms-state="<?= htmlspecialchars($ms['state']) ?>">
                                    <span class="dlv-ms-dot <?= $ms['state'] === 'done' ? 'is-done' : '' ?> <?= $ms['state'] === 'active' ? 'is-active' : '' ?>"></span>
                                    <?php if (!$ms_last): ?>
                                        <span class="dlv-ms-line <?= $ms['state'] === 'done' ? 'is-done' : '' ?>"></span>
                                    <?php endif; ?>
                                    <span class="min-w-0 flex-1 pb-1">
                                        <span class="block text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($ms['label']) ?></span>
                                        <span class="block text-[11px] font-semibold" style="color: var(--fg-muted);"><?= htmlspecialchars($ms['time']) ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
