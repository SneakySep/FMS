<?php

namespace App\NewDash;

/**
 * TRACKING  —  tracking.php
 * ---------------------------------------------------------------------------
 * The live map plus the courier telemetry and the milestone timeline, i.e.
 * ROW 2 of the old single-page dashboard. It is the only module that pulls
 * Leaflet, and the only one whose CSS owns the .dlv-map / .dlv-courier-pin
 * surface.
 *
 * ?wb=WB-90412 opens the map on a specific waybill - that is what the "Track"
 * button in the deliveries table links to now that the table and the map are
 * separate pages. Unknown or missing values fall back to the first open job.
 */
class TrackingModule extends DashboardModule
{
    protected string $key      = 'live';
    protected string $title    = 'Live Tracking';
    protected string $subtitle = 'Every route moving right now, refreshed as the courier drives';
    protected string $template = 'tracking';
    protected array  $css      = ['css/modules/tracking.css'];
    protected array  $js       = [
        'js/core/dom.js',
        'js/core/storage.js',
        'js/core/toast.js',
        'js/core/data.js',
        'js/modules/tracking.module.js',
        'js/modules/ratings.module.js',
    ];
    protected bool   $needsMap = true;
    protected bool   $needsRatingModal = true;

    /** Waybill ids are free-form, so no whitelist - just a length guard. */
    protected function prepare(): void
    {
        $requested = $this->queryParam('wb');
        $delivery  = $requested !== null ? DemoData::find($requested) : null;

        if ($delivery === null) {
            $open     = DemoData::deliveriesFor('motorcycle');
            $delivery = $open[0] ?? DemoData::deliveries()[0];
        }

        $this->vars = [
            'delivery'   => $delivery,
            'vehicle'    => DemoData::vehicleMeta()[$delivery['vehicle']] ?? ['label' => 'Courier', 'icon' => 'fa-truck'],
            'milestones' => $this->milestones($delivery),
            'summary'    => array_values(array_filter(
                DemoData::deliveries(),
                fn($d) => $d['status'] !== 'delivered'
            )),
            'statusBadges' => DemoData::statusBadges(),
        ];
    }

    /**
     * Milestone timeline for a job. The two completed stamps come from the
     * record itself; the rest are estimates, which is what the desk sees while
     * a courier is mid-lane.
     *
     * @return array<int, array{label:string,time:string,state:string}>
     */
    private function milestones(array $delivery): array
    {
        $picked = $delivery['picked'] !== '—' ? $delivery['picked'] : 'Awaiting pickup';

        return [
            ['label' => 'Order accepted',                    'time' => '09:31 AM',      'state' => 'done'],
            ['label' => 'Picked up at ' . $delivery['from'], 'time' => $picked,         'state' => $delivery['status'] === 'scheduled' ? 'todo' : 'done'],
            ['label' => 'In transit to ' . $delivery['to'],  'time' => '09:55 AM',      'state' => $delivery['progress'] > 10 ? 'active' : 'todo'],
            ['label' => 'Out for final handover',            'time' => 'Est. 10:38 AM', 'state' => 'todo'],
            ['label' => 'Delivered + rate courier',          'time' => 'Est. 10:46 AM', 'state' => 'todo'],
        ];
    }
}
