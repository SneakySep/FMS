<?php
/* ==========================================================================
    FLEET  —  fleet.php  (FleetModule::render())
    --------------------------------------------------------------------------
    Carved 1:1 out of the old dashboard.php "Vehicle Variety / Fleet" card.

    Reads: $fleet, $available, $total - all three from
    DemoData::fleet() and its sums, so the board, the "x of y" counters and the
    header badge can never disagree with the booking form's rates.

    ?vehicle=<type> scopes the board to one row (that is how the sidebar's
    Motorcycles and Vans entries reach it); the module filters $fleet before
    the view runs. Each "Use" button links straight to book.php?vehicle=<type>,
    where BookingModule preselects that vehicle server-side.
    -------------------------------------------------------------------------- */
?>

        <!-- VEHICLE VARIETY / FLEET -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div id="fleet" class="lg:col-span-5 crm-card scroll-mt-24">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Vehicle Variety</h2>
                        <span class="crm-panel-sub">What the dispatch board can release right now</span>
                    </div>
                    <span class="crm-badge crm-badge-navy"><span class="crm-badge-dot"></span> <?= $available ?> / <?= $total ?> free</span>
                </div>
                <div class="crm-panel-body space-y-3">
                    <?php foreach ($fleet as $f_key => $f): ?>
                        <?php
                        $f_pct = $f['total'] > 0 ? round(($f['available'] / $f['total']) * 100) : 0;
                        $f_low = $f['available'] <= 3;
                        ?>
                        <div class="dlv-vehicle" data-vehicle="<?= htmlspecialchars($f_key) ?>">
                            <span class="dlv-vehicle-ico"><i class="fa-solid <?= htmlspecialchars($f['icon']) ?>"></i></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($f['label']) ?></span>
                                    <span class="text-[11px] font-extrabold whitespace-nowrap" style="color: var(--fg-muted);">from ₱<?= number_format($f['rate']) ?></span>
                                </div>
                                <span class="block text-[11px] leading-snug mt-0.5" style="color: var(--fg-muted);"><?= htmlspecialchars($f['note']) ?></span>
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="dlv-bar flex-1">
                                        <div class="dlv-bar-fill <?= $f_low ? '!bg-amber-400' : '!bg-emerald-500' ?>" style="width: <?= $f_pct ?>%;"></div>
                                    </div>
                                    <span class="text-[10px] font-extrabold whitespace-nowrap <?= $f_low ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' ?>">
                                        <?= $f['available'] ?> of <?= $f['total'] ?>
                                    </span>
                                </div>
                            </div>
                            <a href="book.php?vehicle=<?= htmlspecialchars($f_key) ?>"
                               class="crm-btn crm-btn-ghost !h-8 !px-2.5 !text-[11px] shrink-0"
                               title="Preselect <?= htmlspecialchars($f['label']) ?> in the booking form">
                                <i class="fa-solid fa-arrow-pointer text-[9px]"></i>
                                <span class="hidden xl:inline">Use</span>
                            </a>
                        </div>
                    <?php endforeach; ?>

                    <div class="pt-1 flex flex-wrap items-center gap-2">
                        <span class="crm-badge crm-badge-slate"><i class="fa-solid fa-temperature-low text-[9px]"></i> Cold chain on request</span>
                        <span class="crm-badge crm-badge-slate"><i class="fa-solid fa-boxes-stacked text-[9px]"></i> Pallet jack with van</span>
                        <span class="crm-badge crm-badge-slate"><i class="fa-solid fa-shield-halved text-[9px]"></i> Insured to ₱25k</span>
                    </div>
                </div>
            </div>
        </section>
