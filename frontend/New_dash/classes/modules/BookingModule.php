<?php

namespace App\NewDash;

/**
 * BOOKING  —  book.php
 * ---------------------------------------------------------------------------
 * The courier booking form. It stays frontend-only: js/dashboard.js validates it,
 * writes localStorage 'newdash_bookings' and
 * quotes the live fare. Since the deliveries table now lives on its own page,
 * a successful save offers a "View deliveries" link instead of prepending a
 * row into a table that is not on this screen.
 *
 * ?vehicle=van preselects the vehicle card - that is what fleet.php's "Use"
 * button and the sidebar's fleet links hand off to.
 */
class BookingModule extends DashboardModule
{
    protected string $key      = 'book';
    protected string $title    = 'Book a Courier';
    protected string $subtitle = 'Pick the vehicle, we dispatch the nearest free driver';
    protected string $template = 'book';

    /* Every behaviour file this demo needs is js/dashboard.js, which PageLayout
       loads once on every page, and css/dashboard.css, which holds all the
       delivery rules. The arrays stay as the per-page extension point. */
    protected array  $css      = [];
    protected array  $js       = [];

    /**
     * The ratings machinery is not on this page.
     */
    public function needsRatingSeed(): bool
    {
        return false;
    }

    protected function prepare(): void
    {
        $allowed  = array_keys(DemoData::vehicleMeta());
        $selected = $this->queryParam('vehicle', 'motorcycle', $allowed);

        $this->vars = [
            'vehicle_meta'  => DemoData::vehicleMeta(),
            'fleet'         => DemoData::fleet(),
            'selected'      => $selected,
            'services'      => ['Express' => 'Express (under 2 hrs)', 'Same-day' => 'Same-day', 'Scheduled' => 'Scheduled'],
            'defaultService' => 'Same-day',
        ];
    }
}
