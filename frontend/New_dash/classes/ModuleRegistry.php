<?php

namespace App\NewDash;

/* ==========================================================================
    MODULE REGISTRY  —  one table that drives the nav rail AND the page files
    --------------------------------------------------------------------------
    Previously components/sidebar.php hardcoded five sections pointing at
    dashboard.php#anchors, while dashboard.php held the sections themselves, so
    adding or renaming a module meant editing both and hoping they agreed. The
    registry is now the only place a nav entry exists:

      key => [class, section, label, icon, url, badge?]

    sidebar.php reads navSections() from here; a page file reads moduleClass()
    to know which class to instantiate. Module keys are also the values of
    $activePage, so the highlighted rail item can never disagree with the page
    that rendered.
    ========================================================================== */

class ModuleRegistry
{
    /** @return array<string, array> */
    public static function all(): array
    {
        return [
            'dashboard' => [
                'class'   => OverviewModule::class,
                'section' => 'OVERVIEW',
                'label'   => 'Dashboard',
                'icon'    => 'fa-border-all',
                'url'     => 'dashboard.php',
            ],
            'book' => [
                'class'   => BookingModule::class,
                'section' => 'COURIER',
                'label'   => 'Book Delivery',
                'icon'    => 'fa-bolt',
                'url'     => 'book.php',
            ],
            'deliveries' => [
                'class'   => DeliveriesModule::class,
                'section' => 'COURIER',
                'label'   => 'Active Deliveries',
                'icon'    => 'fa-list-check',
                'url'     => 'deliveries.php',
                'badge'   => (string) DemoData::activeCount(),
            ],
            'motorcycles' => [
                'class'   => FleetModule::class,
                'section' => 'FLEET',
                'label'   => 'Motorcycles',
                'icon'    => 'fa-motorcycle',
                'url'     => 'fleet.php?vehicle=motorcycle',
            ],
            'vans' => [
                'class'   => FleetModule::class,
                'section' => 'FLEET',
                'label'   => 'Vans',
                'icon'    => 'fa-truck',
                'url'     => 'fleet.php?vehicle=van',
            ],
            'live' => [
                'class'   => TrackingModule::class,
                'section' => 'TRACKING',
                'label'   => 'Live Map',
                'icon'    => 'fa-location-crosshairs',
                'url'     => 'tracking.php',
            ],
            'ratings' => [
                'class'   => RatingsModule::class,
                'section' => 'FEEDBACK',
                'label'   => 'Ratings & Reviews',
                'icon'    => 'fa-star',
                'url'     => 'ratings.php',
            ],
            'settings' => [
                'class'   => SettingsModule::class,
                'section' => 'FEEDBACK',
                'label'   => 'Settings',
                'icon'    => 'fa-gear',
                'url'     => 'settings.php',
            ],
        ];
    }

    /**
     * The navigation tree in the shape SidebarService::buildNavigation()
     * returns - SECTION TITLE => [ key => ['label','icon','url','badge'?] ] -
     * so components/sidebar.php needs no other change.
     */
    public static function navSections(string $currentScript = ''): array
    {
        $sections = [];
        foreach (self::all() as $key => $entry) {
            $item = [
                'label' => $entry['label'],
                'icon'  => $entry['icon'],
                'url'   => $entry['url'],
            ];
            if (isset($entry['badge'])) $item['badge'] = $entry['badge'];
            $sections[$entry['section']][$key] = $item;
        }
        return $sections;
    }

    /** Class name for a key, or null when the key is unknown. */
    public static function moduleClass(string $key): ?string
    {
        return self::all()[$key]['class'] ?? null;
    }

    public static function entry(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /** The key matching a script basename ('deliveries.php' => 'deliveries'). */
    public static function keyForScript(string $script): ?string
    {
        $script = basename($script);
        foreach (self::all() as $key => $entry) {
            if (basename((string) parse_url($entry['url'], PHP_URL_PATH)) === $script) {
                return $key;
            }
        }
        return null;
    }

    /**
     * The one line every thin page file calls: resolve this script's module
     * from the registry, build it from $_GET, and pour it into PageLayout.
     * Unknown scripts fall back to the dashboard so a hand-typed URL can
     * never dead-end. fleet.php?vehicle=van needs no special handling here -
     * FleetModule::key() already reports which sidebar row to light up.
     */
    public static function renderPage(string $script, array $query = []): void
    {
        $key   = self::keyForScript($script) ?? 'dashboard';
        $class = self::all()[$key]['class'];

        (new PageLayout())->render(new $class($query));
    }
}
