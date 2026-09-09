<?php

namespace App\NewDash;

/* ==========================================================================
    DASHBOARD MODULE  —  base class every page in New_dash extends
    --------------------------------------------------------------------------
    A module is one self-contained slice of the dashboard: the thing a sidebar
    row points at. It declares its own identity (key / title / subtitle),
    which template renders it, and which CSS + JS assets it needs, and PageLayout
    assembles the rest of the chrome around it.

    That is the whole contract, which is why adding a module is a 3-file job:
      1. a class here in classes/modules/    (data it needs + the assets it owns)
      2. a template in views/                (markup, 1:1 with what it replaced)
      3. one entry in ModuleRegistry::all()  (nav label, icon, url, badge)

    The module never echoes anything itself and never touches $_GET directly;
    prepare() turns request params + DemoData into plain $vars for the template,
    so a template stays a dumb view and the data flow is readable top to bottom.
    ========================================================================== */

abstract class DashboardModule
{
    /** Stable identifier - also the value ModuleRegistry keys on. */
    protected string $key = '';

    /** Top-bar heading (components/top_header.php's $pageTitle). */
    protected string $title = 'Dashboard';

    /** Top-bar sub-heading. */
    protected string $subtitle = '';

    /** Template file under views/, without the .php extension. */
    protected string $template = '';

    /** Stylesheets under the New_dash root, in dependency order. */
    protected array $css = [];

    /** Scripts under the New_dash root, in dependency order. */
    protected array $js = [];

    /** Modules that render a waybill need the Leaflet CSS/JS pair. */
    protected bool $needsMap = false;

    /** Anything with a "Rate this delivery" button needs the star modal. */
    protected bool $needsRatingModal = false;

    /** Data handed to the template; populated by prepare(). */
    protected array $vars = [];

    /**
     * @param array $query  usually $_GET - filters like ?vehicle=van, ?wb=WB-90412.
     */
    public function __construct(protected array $query = [])
    {
        $this->prepare();
    }

    /** The module key, for PageLayout / sidebar active-state lookups. */
    public function key(): string
    {
        return $this->key;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function subtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * Gather everything the template needs. Overridden by each module; the
     * base is a no-op so a purely static page can exist without one.
     */
    protected function prepare(): void
    {
        $this->vars = [];
    }

    /** Read one query param with a whitelist guard - no unfiltered $_GET in views. */
    protected function queryParam(string $name, ?string $fallback = null, array $allowed = []): ?string
    {
        $raw = isset($this->query[$name]) ? trim((string) $this->query[$name]) : '';
        if ($raw === '') return $fallback;
        if ($allowed && !in_array($raw, $allowed, true)) return $fallback;
        return $raw;
    }

    /**
     * Render the module template into a string. Included with local scope so
     * the template reads $vars directly via extract(), matching the style of
     * the rest of the portal's includes.
     */
    public function render(): string
    {
        $file = __DIR__ . '/../views/' . $this->template . '.php';
        if (!is_file($file)) {
            return '<!-- missing view: ' . htmlspecialchars($this->template) . ' -->';
        }

        extract($this->vars, EXTR_SKIP);
        ob_start();
        include $file;

        return (string) ob_get_clean();
    }

    /** PageLayout calls this to collect the module's extra head stylesheets. */
    public function cssFiles(): array
    {
        return $this->css;
    }

    /** PageLayout calls this to collect the module's scripts (footer order). */
    public function jsFiles(): array
    {
        return $this->js;
    }

    public function needsMap(): bool
    {
        return $this->needsMap;
    }

    public function needsRatingModal(): bool
    {
        return $this->needsRatingModal;
    }

    /**
     * Whether PageLayout should inline DemoData::ratingSeed() as
     * <script id="ratingSeed">. True for anything that renders stars or the
     * rating KPI; false keeps the payload off pages such as settings.php.
     */
    public function needsRatingSeed(): bool
    {
        return true;
    }

    /**
     * The top-bar search box, in the shape components/top_header.php expects
     * (['placeholder'=>, 'id'=>, 'onkeyup'=>]). The default is a plain, inert
     * box so the chrome matches the live portals; DeliveriesModule overrides it
     * with the handler that filters its table, and a module that has nothing
     * worth searching returns false to drop the control entirely.
     */
    public function headerSearch(): array|bool|null
    {
        return ['placeholder' => 'Search the courier desk...', 'id' => 'globalSearch'];
    }

    /**
     * The top-bar hero action. Every page's hero action is the booking form,
     * so the default links at book.php (the original markup pointed at the
     * #book-delivery anchor that used to sit on the same page).
     */
    public function headerAction(): ?string
    {
        return '<a href="book.php" class="crm-btn crm-btn-primary !h-9 !px-3.5 !text-xs">'
            . '<i class="fa-solid fa-bolt text-[10px]"></i>'
            . '<span class="hidden sm:inline">Book Delivery</span></a>';
    }
}
