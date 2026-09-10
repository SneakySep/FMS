<?php

namespace App\NewDash;

/**
 * FLEET  —  fleet.php?vehicle=motorcycle|van|tricycle|pickup
 * ---------------------------------------------------------------------------
 * The vehicle board: one card per vehicle type with capacity, availability,
 * starting rate and the "Use" hand-off into the booking form. The sidebar has
 * two entries pointing here (Motorcycles and Vans) with ?vehicle= set, so a
 * single page serves both while the rail still reads as separate modules.
 *
 * ?vehicle=<type> scopes the board to that type. It falls back to the whole
 * fleet when absent or unknown, so a hand-typed fleet.php still works.
 */
class FleetModule extends DashboardModule
{
    protected string $key      = 'motorcycles';
    protected string $title    = 'Vehicle Variety';
    protected string $subtitle = 'What the dispatch board can release right now';
    protected string $template = 'fleet';
    protected array  $css      = [];
    protected array  $js       = [];

    public function needsRatingSeed(): bool
    {
        return false;
    }

    public function headerSearch(): array|bool|null
    {
        return ['placeholder' => 'Search a vehicle type...', 'id' => 'fleetSearch'];
    }

    /** Both sidebar rows share this page; the query decides which is lit up. */
    public function key(): string
    {
        return ($this->queryParam('vehicle') === 'van') ? 'vans' : 'motorcycles';
    }

    /** The board's heading follows the scoped vehicle type. */
    public function title(): string
    {
        $vehicle = $this->queryParam('vehicle', null, array_keys(DemoData::fleet()));
        if ($vehicle === null) return 'Vehicle Variety';

        return DemoData::vehicleMeta()[$vehicle]['label'] . ' Fleet';
    }

    protected function prepare(): void
    {
        $fleet      = DemoData::fleet();
        $allowed    = array_keys($fleet);
        $vehicle    = $this->queryParam('vehicle', null, $allowed);
        $rows       = $vehicle !== null ? [$vehicle => $fleet[$vehicle]] : $fleet;

        $available  = array_sum(array_column($rows, 'available'));
        $total      = array_sum(array_column($rows, 'total'));

        $this->vars = [
            'fleet'     => $rows,
            'vehicle'   => $vehicle,
            'available' => $available,
            'total'     => $total,
            /* The extras the board advertises next to the numbers. */
            'perks'     => [
                ['icon' => 'fa-temperature-low',  'text' => 'Cold chain on request'],
                ['icon' => 'fa-boxes-stacked',    'text' => 'Pallet jack with van'],
                ['icon' => 'fa-shield-halved',    'text' => 'Insured to ₱25k'],
            ],
        ];
    }
}
