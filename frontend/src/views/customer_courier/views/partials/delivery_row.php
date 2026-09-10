<?php
/* ==========================================================================
    PARTIAL  —  one row of the deliveries table
    --------------------------------------------------------------------------
    Extracted 1:1 from the table body that used to be inlined in dashboard.php
    so the same row renders in three places without a copy of the markup:
      1. views/deliveries.php   (the board)
      2. views/ratings.php      (the already-scored / awaiting-feedback lists)
      3. views/overview.php     (the "moving right now" strip, minus the
                                 feedback column - see $row_compact)

    The markup carries the data-* attributes js/dashboard.js
    filters on, and the identical block is mirrored inside a hidden
    <template id="deliveryRowTemplate"> on the deliveries page so a booking
    saved in localStorage can be replayed into the table client-side with the
    exact same shape as the server-rendered rows.

    Expected variables:
      $d              array   one DemoData::deliveries() record
      $status_badges  array   DemoData::statusBadges()
      $vehicle_meta   array   DemoData::vehicleMeta()
      $row_compact    bool    optional - hides the Feedback column (overview)
    -------------------------------------------------------------------------- */

$badge   = $status_badges[$d['status']] ?? ['badge' => 'crm-badge-slate', 'label' => ucfirst($d['status'])];
$veh     = $vehicle_meta[$d['vehicle']] ?? ['label' => ucfirst($d['vehicle']), 'icon' => 'fa-truck'];
$compact = $row_compact ?? false;
?>
<tr class="dlv-row"
    data-status="<?= htmlspecialchars($d['status']) ?>"
    data-vehicle="<?= htmlspecialchars($d['vehicle']) ?>"
    data-waybill="<?= htmlspecialchars($d['id']) ?>"
    data-search="<?= htmlspecialchars(strtolower($d['id'] . ' ' . $d['from'] . ' ' . $d['to'] . ' ' . $d['recipient'] . ' ' . $d['courier'])) ?>">
    <td>
    <span class="cell-strong font-mono !text-[11px]"><?= htmlspecialchars($d['id']) ?></span>
    <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= htmlspecialchars($d['service']) ?> &middot; <?= htmlspecialchars($d['picked']) ?></span>
</td>
<td>
    <span class="block text-xs font-semibold" style="color: var(--fg-heading);"><?= htmlspecialchars($d['to']) ?></span>
    <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">from <?= htmlspecialchars($d['from']) ?> &middot; <?= htmlspecialchars($d['distance']) ?></span>
</td>
<td>
    <span class="inline-flex items-center gap-2 text-xs font-semibold">
        <i class="fa-solid <?= htmlspecialchars($veh['icon']) ?> text-[11px]" style="color: var(--navy-400);"></i>
        <?= htmlspecialchars($veh['label']) ?>
    </span>
</td>
<td>
    <span class="block text-xs font-semibold" style="color: var(--fg-heading);"><?= htmlspecialchars($d['courier']) ?></span>
    <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">to <?= htmlspecialchars($d['recipient']) ?></span>
</td>
<td>
    <span class="crm-badge <?= $badge['badge'] ?>"><span class="crm-badge-dot"></span> <?= htmlspecialchars($badge['label']) ?></span>
</td>
<td>
    <span class="text-xs font-bold whitespace-nowrap" style="color: var(--fg-heading);"><?= htmlspecialchars($d['eta']) ?></span>
    <div class="dlv-bar w-24 mt-1.5">
        <div class="dlv-bar-fill !bg-brand-blue" style="width: <?= (int) $d['progress'] ?>%;"></div>
    </div>
</td>
<td class="text-right whitespace-nowrap">
    <span class="text-xs font-bold" style="color: var(--fg-heading);"><?= htmlspecialchars($d['weight']) ?></span>
    <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);"><?= (int) $d['slots'] ?> parcel<?= $d['slots'] == 1 ? '' : 's' ?></span>
</td>
<?php if (!$compact): ?>
<td class="text-right whitespace-nowrap">
    <?php if ($d['status'] === 'delivered'): ?>
        <?php if ($d['rating'] !== null): ?>
            <span class="dlv-stars" title="Rated <?= (int) $d['rating'] ?> out of 5" data-rating-for="<?= htmlspecialchars($d['id']) ?>">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-solid fa-star <?= $i <= (int) $d['rating'] ? 'is-on' : '' ?>"></i>
                <?php endfor; ?>
            </span>
            <span class="block text-[10px] font-semibold" style="color: var(--fg-muted);">Rated</span>
        <?php else: ?>
            <button type="button"
                    class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]"
                    data-rate="<?= htmlspecialchars($d['id']) ?>"
                    data-courier="<?= htmlspecialchars($d['courier']) ?>"
                    data-route="<?= htmlspecialchars($d['from'] . ' to ' . $d['to']) ?>">
                <i class="fa-regular fa-star text-[10px]"></i> Rate courier
            </button>
        <?php endif; ?>
    <?php else: ?>
        <button type="button" class="crm-btn crm-btn-ghost !h-8 !px-3 !text-[11px]" data-track="<?= htmlspecialchars($d['id']) ?>">
            <i class="fa-solid fa-location-crosshairs text-[10px]"></i> Track
        </button>
    <?php endif; ?>
</td>
<?php endif; ?>
</tr>

