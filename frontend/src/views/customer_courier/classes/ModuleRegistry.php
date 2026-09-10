<?php

namespace App\NewDash;

use App\Services\PortalRegistry;

/* CUSTOMER_SEGMENT_INDIVIDUAL is a global-namespace constant defined by
   src/helpers/portal_access.php (via auth_flow.php). Inside a namespace an
   unqualified CONSTANT does NOT fall back to global scope the way a function or
   class name does, so it is imported explicitly with `use const` below rather
   than sprinkled with leading backslashes. PortalRegistry requires
   portal_access.php itself, which is what makes the single require_once enough. */
use const CUSTOMER_SEGMENT_INDIVIDUAL;

require_once __DIR__ . '/../../../../src/services/PortalRegistry.php';

/* ==========================================================================
    MODULE REGISTRY  -  module CLASSES only; the nav tree comes from PortalRegistry
    --------------------------------------------------------------------------
    This class used to hold both the nav rail (label / icon / url / badge) and
    the script -> module class mapping. The nav half duplicated what the B2B
    portal kept in SidebarService, and the two drifted: a courier page could be
    renamed or relabelled without the B2B table ever hearing about it, and
    vice versa.

    Nav data now lives in ONE place - App\Services\PortalRegistry, keyed by
    customer segment - which both sidebars read. What stays here is genuinely
    courier-specific and cannot live in a shared table:

      key => class          a plain script has no module class, so the B2B
                           portal has nothing to contribute here
      deliveries badge     DemoData::activeCount() is demo data owned by this
                           module pipeline; a src/services class must not reach
                           into a view folder to fetch it

    navSections() is the seam between the two: it asks PortalRegistry for the
    tree and overlays the live count. components/sidebar.php still calls exactly
    this method, so the renderer needed no change.
    ========================================================================== */

class ModuleRegistry
{
    /**
     * The only courier-specific fact per nav key: which class builds the page.
     * Labels, icons, urls and section grouping come from PortalRegistry, so a
     * key missing from this map is a bug that surfaces as a null class rather
     * than as a silently wrong rail.
     */
    public static function classes(): array
    {
        return [
            'dashboard'   => OverviewModule::class,
            'book'        => BookingModule::class,
            'deliveries'  => DeliveriesModule::class,
            'motorcycles' => FleetModule::class,
            'vans'        => FleetModule::class,
            'live'        => TrackingModule::class,
            'ratings'     => RatingsModule::class,
            'settings'    => SettingsModule::class,
        ];
    }

    /** Class name for a key, or null when the key is unknown. */
    public static function moduleClass(string $key): ?string
    {
        return self::classes()[$key] ?? null;
    }

    /** The key matching a script basename ('deliveries.php' => 'deliveries'). */
    public static function keyForScript(string $script): ?string
    {
        $script = basename($script);
        $entries = PortalRegistry::entriesFor(CUSTOMER_SEGMENT_INDIVIDUAL);

        foreach (self::classes() as $key => $class) {
            $url = $entries[$key]['url'] ?? '';
            if (basename((string) parse_url($url, PHP_URL_PATH)) === $script) {
                return $key;
            }
        }
        return null;
    }

    /**
     * The navigation tree in the shape SidebarService::buildNavigation() and
     * src/includes/sidebar.php consume - SECTION TITLE => [ key =>
     * ['label','icon','url','badge'?] ] - so components/sidebar.php needed no
     * change when the labels moved out to PortalRegistry.
     *
     * The only thing added here is the live Active Deliveries count, which is
     * demo data owned by this module pipeline and therefore cannot live in a
     * shared table under src/services.
     */
    public static function navSections(string $currentScript = ''): array
    {
        /* withBadge() returns a copy, so the cached table in PortalRegistry is
           never mutated with this request's count. */
        return PortalRegistry::withBadge(
            CUSTOMER_SEGMENT_INDIVIDUAL,
            'deliveries',
            (string) DemoData::activeCount()
        );
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
        $class = self::moduleClass($key);

        if ($class === null) {
            /* PortalRegistry knows a page this map does not, or a key was
               renamed on one side only. Failing loudly beats a blank rail. */
            http_response_code(500);
            exit('Module class is not registered for portal page: ' . $key);
        }

        (new PageLayout())->render(new $class($query));
    }
}

