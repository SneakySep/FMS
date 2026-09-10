<?php

namespace App\NewDash;

/**
 * SETTINGS  —  settings.php
 * ---------------------------------------------------------------------------
 * The one genuinely new screen in the refactor. Before, dark mode / accent /
 * density were only readable from the <head> bootstrap; there was no way to
 * change them from the delivery desk. This page exposes the three controls the
 * same way the customer settings page does - against the shared
 * 'crm_customer_prefs' localStorage record - using the window.crmSetDarkMode /
 * crmSetAccent / crmSetDensity helpers PageLayout's pre-paint script defines.
 *
 * Still zero backend: no session, no API call, no fetch(). Clearing demo data
 * clears only the two New_dash localStorage keys.
 */
class SettingsModule extends DashboardModule
{
    protected string $key      = 'settings';
    protected string $title    = 'Settings';
    protected string $subtitle = 'Saved to this device only - nothing is sent anywhere';
    protected string $template = 'settings';
    protected array  $css      = [];
    protected array  $js       = [];

    /** The page reads prefs from localStorage at runtime; no PHP seed needed. */
    public function needsRatingSeed(): bool
    {
        return false;
    }

    public function headerSearch(): array|bool|null
    {
        return false;
    }

    protected function prepare(): void
    {
        $this->vars = [
            /* Values are only the labels + the exact data-accent tokens the
               customer settings page writes and theme.css reads; the
               checked/selected state is applied client-side from localStorage
               so the markup never disagrees with the stored prefs. */
            'accents' => [
                'navy'    => ['label' => 'Navy',    'color' => '#1d2e6a'],
                'blue'    => ['label' => 'Brand blue', 'color' => '#0066ff'],
                'violet'  => ['label' => 'Violet',  'color' => '#7c3aed'],
                'emerald' => ['label' => 'Emerald', 'color' => '#059669'],
                'amber'   => ['label' => 'Amber',   'color' => '#d97706'],
                'rose'    => ['label' => 'Rose',    'color' => '#dc2626'],
            ],
            'storageKeys' => [
                ['key' => 'newdash_bookings',    'label' => 'Bookings created on this device'],
                ['key' => 'newdash_ratings',     'label' => 'Ratings submitted on this device'],
                ['key' => 'newdash_read_notifs', 'label' => 'Read notifications'],
                ['key' => 'crm_customer_prefs',  'label' => 'Theme, accent and density'],
            ],
        ];
    }
}
