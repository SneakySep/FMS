<?php
/* ==========================================================================
    DELIVERIES  —  deliveries.php  (DeliveriesModule::render())
    --------------------------------------------------------------------------
    Carved 1:1 out of the old dashboard.php "ROW 4: ACTIVE DELIVERIES TABLE":
    the vehicle select, the status pills, the header search box, the work table
    and the empty state.

    Reads: $deliveries, $total_deliveries, $slots_in_transit, $status_badges,
    $vehicle_meta - all from DeliveriesModule::prepare(), which is also where
    ?vehicle= / ?status= narrow the row set before this runs.

    The only line that is not original markup is the row loop's body: each
    <tr> now comes from views/partials/delivery_row.php, which dashboard.php's
    compact table includes too, so a row can never render differently on the
    two pages. js/dashboard.js clones that same partial when it
    replays a booking saved on this device into the top of the table.
    -------------------------------------------------------------------------- */
?>


        <!-- ROW 4: ACTIVE DELIVERIES TABLE -->
        <section id="active-deliveries" class="scroll-mt-24">
            <div class="crm-card overflow-hidden">
                <div class="crm-panel-head flex-wrap">
                    <div>
                        <h2 class="crm-panel-title">Deliveries</h2>
                        <span class="crm-panel-sub"><span id="deliveryVisibleCount"><?= $total_deliveries ?></span> of <?= $total_deliveries ?> shown &middot; <?= $slots_in_transit ?> parcels moving</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Vehicle filter -->
                        <select id="vehicleFilter" class="crm-select !h-8 !w-auto !text-[11px] !pl-2.5 !pr-7" aria-label="Filter by vehicle">
                            <option value="all">All vehicles</option>
                            <?php foreach ($vehicle_meta as $v_key => $v): ?>
                                <option value="<?= htmlspecialchars($v_key) ?>"><?= htmlspecialchars($v['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Status pills -->
                        <div id="statusPills" class="flex flex-wrap items-center gap-1.5">
                            <button type="button" class="crm-pill is-active" data-status="all">All</button>
                            <button type="button" class="crm-pill" data-status="scheduled">Scheduled</button>
                            <button type="button" class="crm-pill" data-status="in_transit">In transit</button>
                            <button type="button" class="crm-pill" data-status="out_for_delivery">Out for delivery</button>
                            <button type="button" class="crm-pill" data-status="delivered">Delivered</button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto crm-scroll">
                    <table class="crm-table" id="deliveriesTable">
                        <thead>
                            <tr>
                                <th>Waybill</th>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Courier</th>
                                <th>Status</th>
                                <th>ETA</th>
                                <th class="text-right">Load</th>
                                <th class="text-right">Feedback</th>
                            </tr>
                        </thead>
                        <tbody id="deliveriesBody">
                            <?php foreach ($deliveries as $d): ?>
                            <?php include __DIR__ . '/partials/delivery_row.php'; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="deliveriesEmpty" class="crm-empty" style="display: none;">
                    <span class="crm-empty-ico"><i class="fa-solid fa-filter"></i></span>
                    <p class="crm-empty-title">No delivery matches those filters</p>
                    <p class="crm-empty-sub">Clear the search box or switch the status pill back to &ldquo;All&rdquo;.</p>
                    <button type="button" id="clearFiltersBtn" class="crm-btn crm-btn-ghost !h-9 !text-xs mt-2">Reset filters</button>
                </div>
            </div>
        </section>
