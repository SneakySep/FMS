<?php

namespace App\NewDash;

/**
 * OVERVIEW  —  dashboard.php
 * ---------------------------------------------------------------------------
 * The landing screen stays rich: welcome banner, the four KPI cards, the
 * "moving right now" route strip and the quick-action / saved-on-device panel.
 * What it no longer carries is the heavy machinery that used to be stacked
 * below it on the same page (the full table, the booking form, the map, the
 * review list) - each of those is its own page now, so the banner links point
 * at files instead of anchors.
 */
class OverviewModule extends DashboardModule
{
    protected string $key      = 'dashboard';
    protected string $title    = 'Courier Desk';
    protected string $subtitle = 'Delivery Operations · Metro Manila & Cavite lanes';
    protected string $template = 'overview';
    protected array  $css      = [];
    protected array  $js       = [];
    protected bool   $needsRatingModal = true;

    protected function prepare(): void
    {
        $kpis = DemoData::kpis();

        /* The route strip shows the three busiest open jobs, so the overview
           is a summary of the work rather than a second copy of the table. */
        $this->vars = [
            'kpis'          => $kpis,
            'moving'        => $this->movingNow(),
            'displayName'   => DemoData::DISPLAY_NAME,
            'status_badges' => DemoData::statusBadges(),
            'vehicle_meta'  => DemoData::vehicleMeta(),
            'freeVehicles'  => $kpis['openBikes'] + $kpis['openVans'],
        ];
    }

    /**
     * Open jobs ordered by progress desc, capped at three - the routes a
     * dispatcher is most likely to click into.
     *
     * @return array<int, array>
     */
    private function movingNow(): array
    {
        $open = array_values(array_filter(
            DemoData::deliveries(),
            fn($d) => $d['status'] !== 'delivered' && $d['status'] !== 'scheduled'
        ));

        usort($open, fn($a, $b) => $b['progress'] <=> $a['progress']);

        return array_slice($open, 0, 3);
    }
}
