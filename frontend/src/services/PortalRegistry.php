<?php

namespace App\Services;

/* The segment values are global constants from src/helpers/auth_flow.php.
   Inside a namespace an unqualified CONSTANT does not fall back to the global
   scope the way function and class names do, so they are aliased in explicitly;
   without these two lines every reference below resolves to
   App\Services\CUSTOMER_SEGMENT_BUSINESS and is a fatal "undefined constant". */
use const CUSTOMER_SEGMENT_BUSINESS;
use const CUSTOMER_SEGMENT_INDIVIDUAL;

require_once __DIR__ . '/../helpers/portal_access.php';

/* ==========================================================================
    PORTAL REGISTRY  —  one navigation table for BOTH customer portals
    --------------------------------------------------------------------------
    The two customer surfaces used to keep their nav in two different places:
    the B2B rail was a literal inside SidebarService::buildNavigation(), and the
    courier rail lived in views/customer_courier/classes/ModuleRegistry.php.
    Adding a page meant editing whichever file owned that portal, and nothing
    kept the two in step.

    This class is now the single source of navigation truth, keyed by customer
    segment. Both sidebars read from it:

      src/includes/sidebar.php                    <- SidebarService (B2B)
      views/customer_courier/components/sidebar.php <- ModuleRegistry (courier)

    SHAPE - deliberately identical to what src/includes/sidebar.php already
    consumes, so neither renderer needed a new template:

      'SECTION TITLE' => [
          'key' => ['label' => , 'icon' => , 'url' => ,
                    'badge' => ?, 'badgeColor' => ?, 'submenu' => ?],
      ]

    A `submenu` entry is a group header that expands in place (the B2B "Shipment
    Hub"); its children carry label/url and no icon. Keys are what $activePage is
    compared against, so a key must equal the page's own $activePage value or the
    rail silently stops highlighting.

    WHAT IS *NOT* IN HERE: the courier module *classes* (OverviewModule, ...).
    Those map a script file to a PHP class and belong to the courier page
    pipeline; ModuleRegistry keeps them and delegates only the nav tree to this
    class. B2B pages have no module classes at all - they are plain scripts - so
    a shared class registry would have been a lie about the B2B side.
    ========================================================================== */

class PortalRegistry
{
    /** Segment => section tree. */
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        /* Support badges are live on the B2B side and static on the courier
           side today; call-sites pass the count through badge() rather than
           having this table reach for the API, which would make a nav lookup do
           network I/O. */
        return self::$cache = [
            CUSTOMER_SEGMENT_BUSINESS => self::business(),
            CUSTOMER_SEGMENT_INDIVIDUAL => self::individual(),
        ];
    }

    /** Sections for one segment; unknown segments fall back to business. */
    public static function sectionsFor(string $segment): array
    {
        $all = self::all();
        return $all[$segment] ?? $all[CUSTOMER_SEGMENT_BUSINESS];
    }

    /** The badge under the brand name in the rail. */
    public static function portalLabel(string $segment): string
    {
        return $segment === CUSTOMER_SEGMENT_INDIVIDUAL
            ? 'COURIER PORTAL'
            : 'CUSTOMER PORTAL';
    }

    /**
     * Flattened key => item map for a segment (section titles removed). Lets
     * callers resolve a nav key or a script name without walking sections, and
     * is what makes ModuleRegistry able to drop its own duplicate copy of the
     * labels, icons and urls.
     */
    public static function entriesFor(string $segment): array
    {
        $flat = [];
        foreach (self::sectionsFor($segment) as $items) {
            foreach ($items as $key => $item) {
                $flat[$key] = $item;
                foreach ($item['submenu'] ?? [] as $subKey => $sub) {
                    $flat[$subKey] = $sub;
                }
            }
        }
        return $flat;
    }

    /**
     * B2B / business. Moved verbatim from SidebarService::buildNavigation()
     * (the branch that used to run for every role other than admin/sales), so
     * labels, icons, keys, the Shipment Hub submenu, the Support badge and the
     * relative urls are unchanged - a business customer sees exactly the rail
     * they saw before.
     */
    private static function business(): array
    {
        return [
            'OVERVIEW' => [
                'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-border-all', 'url' => 'dashboard.php'],
            ],
            'FREIGHT' => [
                'freight_group' => [
                    'label' => 'Shipment Hub',
                    'icon'  => 'fa-box-archive',
                    'submenu' => [
                        'shipments'      => ['label' => 'Shipments',      'url' => 'shipments.php'],
                        'tracking'       => ['label' => 'Live Tracking',  'url' => 'tracking.php'],
                        'sla-monitoring' => ['label' => 'SLA Monitoring', 'url' => 'sla-monitoring.php'],
                    ],
                ],
            ],
            'RECORDS' => [
                'documents' => ['label' => 'Documents',          'icon' => 'fa-file-lines',            'url' => 'documents.php'],
                'invoices'  => ['label' => 'Invoices & Billing', 'icon' => 'fa-file-invoice-dollar',   'url' => 'invoices.php'],
                'analytics' => ['label' => 'BI Analytics',       'icon' => 'fa-chart-column',          'url' => 'analytics.php'],
            ],
            'SUPPORT' => [
                /* badge '2' is preserved verbatim from the old SidebarService
                   literal. It is not a live count: tickets.php renders static
                   demo HTML and portal.py exposes no ticket endpoint, so there
                   is nothing to count yet. When that endpoint exists, replace
                   this with PortalRegistry::withBadge(segment, 'tickets', $n). */
                'tickets'  => self::badge(
                    ['label' => 'Support Tickets', 'icon' => 'fa-comments', 'url' => 'tickets.php', 'badge' => '2'],
                    'bg-amber-500/20 text-amber-400'
                ),
                'settings' => ['label' => 'Settings', 'icon' => 'fa-gear', 'url' => 'settings.php'],
            ],
        ];
    }

    /**
     * C2B / individual (courier). Mirrors the tree previously hardcoded in
     * views/customer_courier/classes/ModuleRegistry.php.
     *
     * `deliveries` has no badge here on purpose: the count comes from DemoData,
     * which is courier-module code, and a nav table in src/services must not
     * reach into a view folder. ModuleRegistry::navSections() overlays the live
     * count onto whatever this returns.
     */
    private static function individual(): array
    {
        return [
            'OVERVIEW' => [
                'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-border-all', 'url' => 'dashboard.php'],
            ],
            'COURIER' => [
                'book'       => ['label' => 'Book Delivery',     'icon' => 'fa-bolt',       'url' => 'book.php'],
                'deliveries' => ['label' => 'Active Deliveries', 'icon' => 'fa-list-check', 'url' => 'deliveries.php'],
            ],
            'FLEET' => [
                'motorcycles' => ['label' => 'Motorcycles', 'icon' => 'fa-motorcycle', 'url' => 'fleet.php?vehicle=motorcycle'],
                'vans'        => ['label' => 'Vans',        'icon' => 'fa-truck',      'url' => 'fleet.php?vehicle=van'],
            ],
            'TRACKING' => [
                'live' => ['label' => 'Live Map', 'icon' => 'fa-location-crosshairs', 'url' => 'tracking.php'],
            ],
            'FEEDBACK' => [
                'ratings'  => ['label' => 'Ratings & Reviews', 'icon' => 'fa-star', 'url' => 'ratings.php'],
                'settings' => ['label' => 'Settings',          'icon' => 'fa-gear', 'url' => 'settings.php'],
            ],
        ];
    }

    /**
     * A nav entry whose badge is filled in by the caller. Rendering an absent
     * badge is safe: src/includes/sidebar.php guards it with isset(), so leaving
     * the key out is what "no count yet" looks like.
     */
    private static function badge(array $item, string $color = ''): array
    {
        if ($color !== '') {
            $item['badgeColor'] = $color;
        }
        return $item;
    }

    /**
     * Fill a named badge from live data. Returns a copy - the tables above are
     * cached, and mutating them would leak one request's count into the next.
     *
     * @param string $segment Which portal's tree to read.
     * @param string $key     Nav key, e.g. 'tickets' or 'deliveries'.
     * @param string|int $value The count. Empty string hides the badge.
     */
    public static function withBadge(string $segment, string $key, string|int $value): array
    {
        $sections = self::sectionsFor($segment);

        foreach ($sections as $title => $items) {
            if (!isset($items[$key])) {
                continue;
            }
            if ($value === '' || $value === null) {
                unset($sections[$title][$key]['badge']);
            } else {
                $sections[$title][$key]['badge'] = (string) $value;
            }
            return $sections;
        }

        return $sections;
    }
}

