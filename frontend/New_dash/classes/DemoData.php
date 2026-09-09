<?php

namespace App\NewDash;

/* ==========================================================================
    DEMO DATA  —  single source of truth for every New_dash page
    --------------------------------------------------------------------------
    Everything that used to live as loose arrays at the top of dashboard.php
    now lives here as static accessors, so the overview KPIs, the deliveries
    table, the fleet board, the booking picker and the rating summary all read
    the exact same records and can never disagree with each other.

    FRONTEND ONLY / NO BACKEND: every value below is a literal. There is no
    session_start(), no helpers/api_helper.php require and no
    make_api_request() call anywhere in this class. When the API is wired up,
    these methods are the seam - each one maps to a single endpoint and the
    views never change.

    The accessors return a copy (arrays are copied by value in PHP), so a view
    can filter or merge into a result without mutating the shared record set.
    ========================================================================== */

class DemoData
{
    /** Human name used in the welcome banner and the sidebar profile card. */
    public const DISPLAY_NAME = 'D. Cruz';

    public const INITIALS    = 'DC';
    public const DISPATCH_ID = 'CV-07';

    /* ----------------------------------------------------------------------
       Vehicles: label + icon + capacity + base rate.
       Reused by the fleet board, the deliveries table, the booking form's
       radio cards and (via js/core/data.js) the live fare quote, so the
       wording never drifts between the four.
       ---------------------------------------------------------------------- */
    public static function vehicleMeta(): array
    {
        return [
            'motorcycle' => ['label' => 'Motorcycle', 'icon' => 'fa-motorcycle',   'capacity' => 'up to 5 kg',       'rate' => 79],
            'tricycle'   => ['label' => 'Tricycle',   'icon' => 'fa-bicycle',      'capacity' => 'up to 25 kg',      'rate' => 110],
            'van'        => ['label' => 'L300 Van',   'icon' => 'fa-truck',        'capacity' => 'up to 600 kg',     'rate' => 420],
            'pickup'     => ['label' => 'Pickup',     'icon' => 'fa-truck-pickup', 'capacity' => 'up to 1,200 kg',   'rate' => 680],
        ];
    }

    /* ----------------------------------------------------------------------
       Status -> shared badge class, so the pills match the rest of the
       portal (theme.css .crm-badge-*).
       ---------------------------------------------------------------------- */
    public static function statusBadges(): array
    {
        return [
            'scheduled'        => ['badge' => 'crm-badge-slate',  'label' => 'Scheduled'],
            'picked_up'        => ['badge' => 'crm-badge-violet', 'label' => 'Picked up'],
            'in_transit'       => ['badge' => 'crm-badge-blue',   'label' => 'In transit'],
            'out_for_delivery' => ['badge' => 'crm-badge-amber',  'label' => 'Out for delivery'],
            'delivered'        => ['badge' => 'crm-badge-green',  'label' => 'Delivered'],
        ];
    }

    /* ----------------------------------------------------------------------
       Fleet availability (the vehicle-variety board).
       Keyed by vehicle type and merged over vehicleMeta() so the booking
       picker, the fleet cards and the fare quote all read one source.
       ---------------------------------------------------------------------- */
    public static function fleet(): array
    {
        static $merged = null;

        if ($merged === null) {
            $availability = [
                'motorcycle' => ['available' => 14, 'total' => 18, 'note' => 'Single parcels and documents, beats the traffic.'],
                'tricycle'   => ['available' => 6,  'total' => 8,  'note' => 'Short hops inside barangays and narrow alleys.'],
                'van'        => ['available' => 5,  'total' => 9,  'note' => 'Palletised retail drops and multi-stop routes.'],
                'pickup'     => ['available' => 3,  'total' => 6,  'note' => 'Oversized freight, warehouse-to-warehouse runs.'],
            ];
            $meta = self::vehicleMeta();
            foreach ($availability as $key => $row) {
                $availability[$key] = array_merge($meta[$key] ?? [], $row);
            }
            $merged = $availability;
        }

        return $merged;
    }

    /** Fleet row for one vehicle type - what fleet.php?vehicle=motorcycle reads. */
    public static function fleetFor(string $vehicle): array
    {
        return self::fleet()[$vehicle] ?? [];
    }

    /** Open (not-yet-delivered) jobs - the sidebar badge and KPI 2. */
    public static function activeCount(): int
    {
        return count(array_filter(self::deliveries(), fn($d) => $d['status'] !== 'delivered'));
    }

    /** Number of jobs stored on this device (localStorage) - overview strip. */
    public static function deliveredCount(): int
    {
        return count(self::deliveries()) - self::activeCount();
    }

    /* ----------------------------------------------------------------------
       Notification bell - the shape components/top_header.php expects.
       ---------------------------------------------------------------------- */
    public static function notifications(): array
    {
        return [
            ['id' => 1, 'type' => 'urgent',  'title' => 'WB-90385 at risk',  'message' => 'Pickup window closes in 40 min; no courier assigned yet.', 'time' => '6 min ago', 'link' => 'deliveries.php?status=scheduled'],
            ['id' => 2, 'type' => 'warning', 'title' => 'Van capacity low',  'message' => 'Only 5 of 9 vans free after 1:00 PM today.',               'time' => '22 min ago', 'link' => 'fleet.php?vehicle=van'],
            ['id' => 3, 'type' => 'success', 'title' => 'POD uploaded',      'message' => 'Photo proof of delivery captured for WB-90361.',           'time' => '1 hr ago',  'link' => 'deliveries.php?vehicle=motorcycle'],
            ['id' => 4, 'type' => 'info',    'title' => 'New 5-star review', 'message' => 'B. Mendoza rated L. Ignacio "On time, careful handling".', 'time' => '2 hr ago',  'link' => 'ratings.php'],
        ];
    }

    /* ----------------------------------------------------------------------
       Active deliveries.
         vehicle: motorcycle | van | pickup | tricycle
         status : scheduled | picked_up | in_transit | out_for_delivery | delivered
         rating : null while the job is open, 1-5 once the recipient scored it
       ---------------------------------------------------------------------- */
    public static function deliveries(): array
    {
        return [
            [
                'id' => 'WB-90412', 'from' => 'Makati CDC', 'to' => 'BGC, Taguig',
                'recipient' => 'N. Alvarez', 'courier' => 'J. Ramos', 'vehicle' => 'motorcycle',
                'status' => 'out_for_delivery', 'eta' => '14 min', 'progress' => 78,
                'distance' => '8.4 km', 'weight' => '2.1 kg', 'slots' => 1, 'rating' => null,
                'service' => 'Same-day', 'picked' => '09:42 AM',
            ],
            [
                'id' => 'WB-90408', 'from' => 'Quezon City Hub', 'to' => 'Alabang, Muntinlupa',
                'recipient' => 'Triya Retail', 'courier' => 'M. Dela Peña', 'vehicle' => 'van',
                'status' => 'in_transit', 'eta' => '52 min', 'progress' => 41,
                'distance' => '23.7 km', 'weight' => '412 kg', 'slots' => 14, 'rating' => null,
                'service' => 'Scheduled', 'picked' => '09:05 AM',
            ],
            [
                'id' => 'WB-90399', 'from' => 'Makati CDC', 'to' => 'Ortigas, Pasig',
                'recipient' => 'K. Ocampo', 'courier' => 'R. Villanueva', 'vehicle' => 'motorcycle',
                'status' => 'picked_up', 'eta' => '1 hr 20 min', 'progress' => 12,
                'distance' => '11.2 km', 'weight' => '0.8 kg', 'slots' => 1, 'rating' => null,
                'service' => 'Express', 'picked' => '10:18 AM',
            ],
            [
                'id' => 'WB-90385', 'from' => 'Navotas Depot', 'to' => 'Cavite City',
                'recipient' => 'Southline Parts', 'courier' => 'A. Bautista', 'vehicle' => 'pickup',
                'status' => 'scheduled', 'eta' => '2 hr 05 min', 'progress' => 0,
                'distance' => '38.9 km', 'weight' => '265 kg', 'slots' => 9, 'rating' => null,
                'service' => 'Scheduled', 'picked' => '—',
            ],
            [
                'id' => 'WB-90377', 'from' => 'Pasay Branch', 'to' => 'Binondo, Manila',
                'recipient' => 'R. Tan', 'courier' => 'E. Gonzales', 'vehicle' => 'tricycle',
                'status' => 'in_transit', 'eta' => '27 min', 'progress' => 63,
                'distance' => '5.6 km', 'weight' => '12 kg', 'slots' => 3, 'rating' => null,
                'service' => 'Same-day', 'picked' => '10:02 AM',
            ],
            [
                'id' => 'WB-90361', 'from' => 'Quezon City Hub', 'to' => 'Fairview, Quezon City',
                'recipient' => 'B. Mendoza', 'courier' => 'L. Ignacio', 'vehicle' => 'motorcycle',
                'status' => 'delivered', 'eta' => 'Delivered 10:41 AM', 'progress' => 100,
                'distance' => '6.9 km', 'weight' => '1.4 kg', 'slots' => 1, 'rating' => 5,
                'service' => 'Express', 'picked' => '09:58 AM',
            ],
            [
                'id' => 'WB-90344', 'from' => 'Makati CDC', 'to' => 'Rockwell, Makati',
                'recipient' => 'C. Sy', 'courier' => 'J. Ramos', 'vehicle' => 'van',
                'status' => 'delivered', 'eta' => 'Delivered 09:12 AM', 'progress' => 100,
                'distance' => '3.2 km', 'weight' => '188 kg', 'slots' => 6, 'rating' => 4,
                'service' => 'Scheduled', 'picked' => '08:20 AM',
            ],
            [
                'id' => 'WB-90330', 'from' => 'Navotas Depot', 'to' => 'SM North, Quezon City',
                'recipient' => 'Northline Ops', 'courier' => 'P. Aquino', 'vehicle' => 'pickup',
                'status' => 'delivered', 'eta' => 'Delivered 08:47 AM', 'progress' => 100,
                'distance' => '14.8 km', 'weight' => '96 kg', 'slots' => 4, 'rating' => null,
                'service' => 'Same-day', 'picked' => '07:55 AM',
            ],
        ];
    }

    /** Deliveries for one vehicle type - the Motorcycles / Vans nav entries. */
    public static function deliveriesFor(string $vehicle): array
    {
        return array_values(array_filter(self::deliveries(), fn($d) => $d['vehicle'] === $vehicle));
    }

    /** A single delivery record by waybill number (tracking.php?wb=...). */
    public static function find(string $waybill): ?array
    {
        foreach (self::deliveries() as $row) {
            if ($row['id'] === $waybill) return $row;
        }
        return null;
    }

    /* ----------------------------------------------------------------------
       Ratings. js/dashboard.js re-reads this JSON (inlined by
       PageLayout as #ratingSeed) and folds the localStorage entries on top of
       it, so a submitted star updates the average and the distribution bar
       without a page reload.
       ---------------------------------------------------------------------- */
    public static function ratingBreakdown(): array
    {
        return [5 => 38, 4 => 14, 3 => 5, 2 => 2, 1 => 1];
    }

    public static function ratingTotal(): int
    {
        return array_sum(self::ratingBreakdown());
    }

    public static function ratingAverage(): float
    {
        $breakdown = self::ratingBreakdown();
        $sum = array_sum(array_map(fn($stars, $count) => $stars * $count, array_keys($breakdown), $breakdown));
        return round($sum / max(self::ratingTotal(), 1), 2);
    }

    public static function ratingSeed(): array
    {
        return [
            'breakdown' => self::ratingBreakdown(),
            'average'   => self::ratingAverage(),
            'reviews'   => array_values(array_map(
                fn($d) => ['id' => $d['id'], 'rating' => $d['rating'], 'courier' => $d['courier'], 'to' => $d['to']],
                array_filter(self::deliveries(), fn($d) => $d['rating'] !== null)
            )),
        ];
    }

    /* ----------------------------------------------------------------------
       DERIVED  —  KPI figures computed from the records above so the cards,
       the fleet board and the table can never disagree with each other.
       ---------------------------------------------------------------------- */
    public static function kpis(): array
    {
        $fleet          = self::fleet();
        $deliveries     = self::deliveries();
        $total          = count($deliveries);
        $active         = self::activeCount();
        $openDeliveries = array_filter($deliveries, fn($d) => $d['status'] !== 'delivered');

        return [
            'total'          => $total,
            'active'         => $active,
            'delivered'      => $total - $active,
            'openBikes'      => $fleet['motorcycle']['available'],
            'openVans'       => $fleet['van']['available'],
            'slotsMoving'    => array_sum(array_column($openDeliveries, 'slots')),
            'fleetAvailable' => array_sum(array_column($fleet, 'available')),
            'fleetTotal'     => array_sum(array_column($fleet, 'total')),
            'ratingAverage'  => self::ratingAverage(),
            'ratingTotal'    => self::ratingTotal(),
        ];
    }
}
