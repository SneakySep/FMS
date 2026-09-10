<?php

namespace App\NewDash;

/**
 * DELIVERIES  —  deliveries.php
 * ---------------------------------------------------------------------------
 * The full work table with the vehicle select, the status pills, the header
 * search box and the empty state. Row markup comes from
 * views/partials/delivery_row.php, which js/dashboard.js
 * clones from when a booking saved on this device is replayed into the table.
 *
 * The page also doubles as a filtered view, so the sidebar's vehicle entries
 * and the notification bell can deep-link into it:
 *     deliveries.php?vehicle=van      only van jobs visible on load
 *     deliveries.php?status=scheduled only that status visible on load
 * The filters stay applied client-side by the module; PHP only seeds them.
 */
class DeliveriesModule extends DashboardModule
{
    protected string $key      = 'deliveries';
    protected string $title    = 'Active Deliveries';
    protected string $subtitle = 'Every job on the board, filterable by vehicle and status';
    protected string $template = 'deliveries';
    protected array  $css      = [];
    protected array  $js       = [];
    protected bool   $needsRatingModal = true;

    protected function prepare(): void
    {
        $vehicles   = DemoData::vehicleMeta();
        $statuses   = array_keys(DemoData::statusBadges());
        $deliveries = DemoData::deliveries();

        /* If the sidebar asks for one vehicle type, render only those rows and
           lock the select to it - the same result as filtering by hand, minus
           the click. */
        $vehicle = $this->queryParam('vehicle', null, array_keys($vehicles));
        $status  = $this->queryParam('status', null, $statuses);
        $rows    = $deliveries;

        if ($vehicle !== null) {
            $rows = DemoData::deliveriesFor($vehicle);
        }

        $this->vars = [
            'deliveries'       => $rows,
            'total_deliveries' => count($deliveries),
            'vehicle'          => $vehicle,
            'status'           => $status,
            'vehicles'         => $vehicles,
            'vehicle_meta'     => $vehicles,
            'status_badges'    => DemoData::statusBadges(),
            'statuses'         => $statuses,
            'slots_in_transit' => array_sum(array_column(
                array_filter($deliveries, fn($d) => $d['status'] !== 'delivered'), 'slots'
            )),
            'searchBox'        => true,
        ];
    }
}
