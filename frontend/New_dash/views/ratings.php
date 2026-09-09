<?php
/* ==========================================================================
    RATINGS  —  ratings.php  (RatingsModule::render())
    --------------------------------------------------------------------------
    Carved 1:1 out of the old dashboard.php "ROW 5" feedback card: the big
    average, the 5..1 distribution bars and the recent-comment list.

    Reads: $rating_average, $rating_total, $rating_breakdown, $rating_seed -
    straight from DemoData, which is also what PageLayout inlines as #ratingSeed.

    js/dashboard.js folds the localStorage 'newdash_ratings' entries
    on top of that seed, so a star submitted here moves the average, the bars and
    the review list with no reload and no server. #openRatingBtn opens
    components/rating_modal.php.
    -------------------------------------------------------------------------- */
?>



        <!-- ROW 5: STAR RATING / FEEDBACK  +  QUICK ACTIONS -->
        <section id="feedback" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start scroll-mt-24">

            <!-- RATING SUMMARY -->
            <div class="lg:col-span-8 crm-card">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Ratings &amp; Reviews</h2>
                        <span class="crm-panel-sub">What recipients said about recent handovers</span>
                    </div>
                    <button type="button" id="openRatingBtn" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">
                        <i class="fa-solid fa-star text-[10px]"></i> Rate a delivery
                    </button>
                </div>

                <div class="crm-panel-body grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Score -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center text-center rounded-xl border border-line p-5" style="background: var(--surface-muted);">
                        <span id="ratingAverage" class="text-5xl font-black tracking-tight" style="color: var(--fg-heading);"><?= number_format($rating_average, 1) ?></span>
                        <span class="dlv-stars mt-2 !text-base" id="ratingAverageStars" aria-hidden="true">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star <?= $i <= round($rating_average) ? 'is-on' : '' ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="block text-[11px] font-bold mt-2" style="color: var(--fg-muted);">
                            Based on <span id="ratingTotalCount"><?= $rating_total ?></span> ratings
                        </span>
                        <span class="crm-badge crm-badge-green mt-3"><span class="crm-badge-dot"></span> <span id="ratingPositive"><?= round((($rating_breakdown[4] ?? 0) + ($rating_breakdown[5] ?? 0)) / max($rating_total, 1) * 100) ?></span>% positive</span>
                    </div>

                    <!-- Distribution -->
                    <div class="md:col-span-8 space-y-2.5 self-center">
                        <?php foreach ([5, 4, 3, 2, 1] as $stars): ?>
                            <?php $cnt = $rating_breakdown[$stars] ?? 0; $pct = $rating_total ? round($cnt / $rating_total * 100) : 0; ?>
                            <div class="flex items-center gap-3" data-dist-row="<?= $stars ?>">
                                <span class="w-10 shrink-0 text-[11px] font-bold text-right" style="color: var(--fg-heading);"><?= $stars ?> star<?= $stars > 1 ? 's' : '' ?></span>
                                <div class="dlv-bar flex-1">
                                    <div class="dlv-bar-fill" data-dist-fill="<?= $stars ?>" style="width: <?= $pct ?>%;"></div>
                                </div>
                                <span class="w-9 shrink-0 text-[11px] font-extrabold text-right" style="color: var(--fg-muted);" data-dist-count="<?= $stars ?>"><?= $cnt ?></span>
                            </div>
                        <?php endforeach; ?>

                        <div class="!mt-5 pt-4 space-y-3" style="border-top: 1px solid var(--line);">
                            <span class="crm-section-label">Recent comments</span>
                            <ul id="reviewList" class="space-y-3">
                                <?php foreach ($rating_seed['reviews'] as $rv): ?>
                                    <li class="flex items-start gap-3" data-review-for="<?= htmlspecialchars($rv['id']) ?>">
                                        <span class="crm-avatar !w-8 !h-8 !rounded-lg !text-[10px]"><?= htmlspecialchars(substr($rv['courier'], 0, 2)) ?></span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs font-bold truncate" style="color: var(--fg-heading);"><?= htmlspecialchars($rv['courier']) ?> &middot; <?= htmlspecialchars($rv['id']) ?></span>
                                                <span class="dlv-stars shrink-0" aria-label="<?= (int) $rv['rating'] ?> out of 5">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa-solid fa-star <?= $i <= (int) $rv['rating'] ? 'is-on' : '' ?>"></i>
                                                    <?php endfor; ?>
                                                </span>
                                            </div>
                                            <p class="text-[11px] leading-snug mt-0.5" style="color: var(--fg-body);">
                                                Handover at <?= htmlspecialchars($rv['to']) ?> went smoothly &mdash; <?= (int) $rv['rating'] >= 5 ? 'courier was early and careful with the parcels.' : 'delivery completed as scheduled.' ?>
                                            </p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS  —  same partial the overview page renders -->
            <?php include __DIR__ . '/quick_actions.php'; ?>
        </section>


        <!-- ROW 5b: RATED / NOT YET RATED
             Both lists reuse the deliveries row partial, so the rating widget
             looks identical here and on deliveries.php. -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- ALREADY RATED -->
            <div class="lg:col-span-7 crm-card overflow-hidden">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Rated handovers</h2>
                        <span class="crm-panel-sub"><?= count($reviewed) ?> jobs with feedback recorded</span>
                    </div>
                    <span class="crm-badge crm-badge-green"><i class="fa-solid fa-circle-check text-[9px]"></i> Feedback in</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="crm-table w-full min-w-[980px] text-left">
                        <thead>
                            <tr>
                                <th>Waybill / Route</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviewed as $d): ?>
                                <?php include __DIR__ . '/partials/delivery_row.php'; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- AWAITING A RATING -->
            <div class="lg:col-span-5 crm-card overflow-hidden">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Not yet rated</h2>
                        <span class="crm-panel-sub">Delivered jobs still missing a score</span>
                    </div>
                    <span class="crm-badge crm-badge-amber"><i class="fa-solid fa-hourglass-half text-[9px]"></i> <?= count($awaiting) ?> pending</span>
                </div>
                <div class="crm-panel-body p-0">
                    <?php if (!$awaiting): ?>
                        <div class="px-5 py-8 text-center">
                            <i class="fa-solid fa-mug-hot text-3xl mb-3" style="color: var(--fg-faint);"></i>
                            <p class="text-sm font-semibold" style="color: var(--fg-body);">Every delivered job has been rated.</p>
                            <p class="crm-panel-sub mt-1">New completions will appear here.</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-line">
                            <?php foreach ($awaiting as $d): ?>
                                <li class="flex items-center justify-between gap-3 px-5 py-3.5">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate" style="color: var(--fg-heading);">
                                            <a href="tracking.php?wb=<?= htmlspecialchars($d['id']) ?>" class="hover:underline"><?= htmlspecialchars($d['id']) ?></a>
                                            <span class="font-semibold" style="color: var(--fg-faint);">&middot;</span>
                                            <span class="font-medium" style="color: var(--fg-muted);"><?= htmlspecialchars($d['to']) ?></span>
                                        </p>
                                        <span class="crm-panel-sub"><?= htmlspecialchars($d['courier']) ?> &middot; <?= htmlspecialchars($d['eta']) ?> on the clock</span>
                                    </div>
                                    <button type="button" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-xs" data-rate-waybill="<?= htmlspecialchars($d['id']) ?>">
                                        <i class="fa-solid fa-star text-[10px]" style="color: var(--brand-amber);"></i> Rate
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>

