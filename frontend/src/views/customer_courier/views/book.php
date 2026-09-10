<?php
/* ==========================================================================
    BOOK  —  book.php  (BookingModule::render())
    --------------------------------------------------------------------------
    Carved 1:1 out of the old dashboard.php "ROW 3" booking card: vehicle radio
    chips, route fields, parcel count, service tier and the live fare quote.

    Reads: $vehicle_meta (DemoData::vehicleMeta()) for the radio chips and
    $selected (BookingModule, from ?vehicle=) for the preselected chip.

    js/dashboard.js drives the form: inline validation, the fare quote
    recomputed on every keystroke, and a localStorage-only submit that stores
    the booking under 'newdash_bookings' for the deliveries board. The fleet
    "Use" buttons on fleet.php preselect a vehicle here through ?vehicle=<type>,
    which BookingModule validates and hands back as $selected.
    -------------------------------------------------------------------------- */
?>

        <!-- ROW 3: COURIER BOOKING  +  VEHICLE VARIETY -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- BOOKING FORM -->
            <div id="book-delivery" class="lg:col-span-7 crm-card scroll-mt-24">
                <div class="crm-panel-head">
                    <div>
                        <h2 class="crm-panel-title">Book a Courier</h2>
                        <span class="crm-panel-sub">Pick the vehicle, we dispatch the nearest free driver</span>
                    </div>
                    <span class="crm-badge crm-badge-blue"><i class="fa-solid fa-flask text-[9px]"></i> Demo only</span>
                </div>

                <div class="crm-panel-body">
                    <!-- BACKEND WIRING: this form posts nowhere. js/dashboard.js validates it,
                         stores the booking in localStorage under 'newdash_bookings' and
                         prepends a row to the deliveries table below. Point the submit
                         handler at POST /api/v1/portal/shipments when that endpoint exists. -->
                    <form id="bookingForm" novalidate class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="crm-label" for="bookSender">Pickup from</label>
                                <input type="text" id="bookSender" name="sender" class="crm-input" placeholder="e.g. Makati CDC" autocomplete="off">
                                <p class="dlv-field-error" data-error-for="bookSender">Tell us where to collect.</p>
                            </div>
                            <div>
                                <label class="crm-label" for="bookRecipient">Deliver to</label>
                                <input type="text" id="bookRecipient" name="recipient_address" class="crm-input" placeholder="e.g. BGC, Taguig" autocomplete="off">
                                <p class="dlv-field-error" data-error-for="bookRecipient">Destination is required.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="crm-label" for="bookName">Recipient name</label>
                                <input type="text" id="bookName" name="recipient_name" class="crm-input" placeholder="e.g. N. Alvarez" autocomplete="off">
                                <p class="dlv-field-error" data-error-for="bookName">Who is receiving this?</p>
                            </div>
                            <div>
                                <label class="crm-label" for="bookContact">Contact number</label>
                                <input type="tel" id="bookContact" name="contact" class="crm-input" placeholder="09XX XXX XXXX" autocomplete="off" inputmode="tel">
                                <p class="dlv-field-error" data-error-for="bookContact">Enter at least 7 digits.</p>
                            </div>
                        </div>

                        <!-- Vehicle picker: radio cards, so the variety is the decision -->
                        <div>
                            <span class="crm-label">Vehicle</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" role="radiogroup" aria-label="Vehicle type">
                                <?php foreach ($vehicle_meta as $v_key => $v): ?>
                                    <label class="dlv-vehicle-opt" data-vehicle="<?= htmlspecialchars($v_key) ?>">
                                        <input type="radio" name="vehicle" value="<?= htmlspecialchars($v_key) ?>" class="sr-only" <?= $v_key === $selected ? 'checked' : '' ?>>
                                        <i class="fa-solid <?= htmlspecialchars($v['icon']) ?> text-base"></i>
                                        <span class="block text-[11px] font-bold leading-tight mt-1"><?= htmlspecialchars($v['label']) ?></span>
                                        <span class="block text-[10px] font-semibold opacity-70"><?= htmlspecialchars($v['capacity']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="crm-label" for="bookService">Service level</label>
                                <select id="bookService" name="service" class="crm-select">
                                    <option value="Express">Express (under 2 hrs)</option>
                                    <option value="Same-day" selected>Same-day</option>
                                    <option value="Scheduled">Scheduled</option>
                                </select>
                            </div>
                            <div>
                                <label class="crm-label" for="bookWeight">Weight (kg)</label>
                                <input type="number" id="bookWeight" name="weight" class="crm-input" min="0.1" step="0.1" value="2.0" inputmode="decimal">
                                <p class="dlv-field-error" data-error-for="bookWeight">Enter a weight above 0.</p>
                            </div>
                            <div>
                                <label class="crm-label" for="bookSlots">Parcels</label>
                                <input type="number" id="bookSlots" name="slots" class="crm-input" min="1" step="1" value="1" inputmode="numeric">
                                <p class="dlv-field-error" data-error-for="bookSlots">At least one parcel.</p>
                            </div>
                        </div>

                        <div>
                            <label class="crm-label" for="bookNote">Handling note (optional)</label>
                            <textarea id="bookNote" name="note" rows="2" class="crm-textarea resize-y" placeholder="Fragile, deliver after 5 PM, gate code 4471..."></textarea>
                        </div>

                        <!-- Live quote -->
                        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-line p-4" style="background: var(--surface-muted);">
                            <div class="flex items-center gap-3">
                                <span class="crm-kpi-ico"><i class="fa-solid fa-receipt"></i></span>
                                <div class="leading-tight">
                                    <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Estimated fare</span>
                                    <span id="bookQuote" class="block text-xl font-black" style="color: var(--fg-heading);">₱79.00</span>
                                </div>
                            </div>
                            <div class="text-right leading-tight">
                                <span class="block text-[10px] font-extrabold uppercase tracking-widest" style="color: var(--fg-muted);">Pickup window</span>
                                <span id="bookWindow" class="block text-sm font-bold" style="color: var(--fg-heading);">within 25 min</span>
                            </div>
                            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                                <button type="reset" class="crm-btn crm-btn-ghost !h-10 !text-xs">Clear</button>
                                <button type="submit" class="crm-btn crm-btn-primary !h-10 !px-5 !text-xs">
                                    <i class="fa-solid fa-bolt text-[10px]"></i> Book courier
                                </button>
                            </div>
                        </div>
                        <p class="text-[11px]" style="color: var(--fg-muted);">
                            <i class="fa-solid fa-circle-info text-[10px]"></i>
                            Nothing leaves this browser &mdash; the booking is stored in localStorage (key <span class="font-bold">newdash_bookings</span>) and shows up on <a href="deliveries.php" class="font-bold underline">Active Deliveries</a>.
                        </p>
                    </form>
                </div>
            </div>
        </section>

