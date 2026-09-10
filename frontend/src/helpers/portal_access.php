<?php
/**
 * Customer portal access guard.
 *
 * The product has two customer-facing surfaces that must not be reachable by
 * the wrong account:
 *
 *   src/views/customer/         B2B / business   - contracted freight accounts
 *   src/views/customer_courier/ C2B / individual - walk-in courier customers
 *
 * Role and segment are separate axes (see the note in auth_flow.php): `role`
 * decides whether you may see a customer portal at all, `customer_type` decides
 * which of the two you are shown.
 *
 * The segment constants and normalize_customer_segment() are defined in
 * auth_flow.php, the lower-level file, so requiring it here does not create a
 * cycle - portal_access.php depends on auth_flow.php and never the reverse.
 *
 * SECURITY NOTE: a folder name in a URL is not access control. Everything below
 * is server-side; the frontend layout is a consequence of the guard, never the
 * thing enforcing it.
 */

require_once __DIR__ . '/auth_flow.php';

/**
 * The URL prefix that leads from the *currently executing* page back to the
 * frontend/ root, with a trailing slash - e.g. '../../../' for a page in
 * src/views/customer/.
 *
 * Computed rather than hardcoded, because the two ways this project is served
 * disagree about where the app root sits:
 *
 *   Apache/XAMPP       DOCUMENT_ROOT = E:/Xampp/Files/htdocs, so the page URL is
 *                      /CRM/customer_relationship/frontend/src/views/customer/x.php
 *   php -S -t frontend the page URL is /src/views/customer/x.php
 *
 * Both share the same `src/views/<folder>` tail, so counting segments after that
 * marker yields the right number of `../` under either server. Root-relative
 * URLs (/assets/, /src/) are NOT used: under Apache they resolve against htdocs/,
 * several levels above the app, and 404. (src/includes/header.php already carries
 * that pre-existing bug; fixing it is out of scope for the merge.)
 */
function portal_base_prefix(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $pos = strrpos($dir, '/src/views/');

    if ($pos !== false) {
        /* Tail is the view folder, e.g. "customer": one segment below
           frontend/src, so two more levels up reach frontend/. */
        $tail   = trim(substr($dir, $pos + strlen('/src/views')), '/');
        $levels = ($tail === '' ? 0 : count(explode('/', $tail))) + 2;
    } else {
        /* Outside src/views/ (or behind a rewrite): fall back to the number of
           path segments actually present, which is the same rule. */
        $levels = count(array_filter(explode('/', $dir), fn ($s) => $s !== ''));
    }

    return $cached = ($levels > 0 ? str_repeat('../', $levels) : './');
}

/**
 * Read a normalised customer segment out of a JWT payload.
 *
 * Deliberately does NOT verify the signature: role_from_jwt() in auth_flow.php
 * already makes the same trade-off, and the token is only ever read after the
 * backend accepted it at login. This is a convenience decoder, not a trust
 * boundary.
 *
 * IMPORTANT: because the value is unsigned it must never be the *only* thing
 * standing between a customer and another customer's data. When the backend
 * settles on one of the shapes below, portal.py has to enforce the same segment
 * on every endpoint - a redirect in PHP is navigation, not isolation.
 */
function jwt_customer_segment(string $token): ?string
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    $payload = json_decode(
        base64_decode(strtr($parts[1], '-_', '+/')),
        true
    );
    if (!is_array($payload)) {
        return null;
    }

    /* The claim name is not decided yet; accept the likely candidates so this
       keeps working whichever one lands. */
    foreach (['customer_type', 'portal_type', 'segment'] as $key) {
        if (isset($payload[$key])) {
            $normalized = normalize_customer_segment($payload[$key]);
            if ($normalized !== null) {
                return $normalized;
            }
        }
    }

    return null;
}

/**
 * Which customer portal does the signed-in user belong to?
 *
 * Resolution order, cheapest and most authoritative first:
 *   1. $_SESSION['customer_type']   - set at login, see auth_flow.php
 *   2. a claim on the access token  - only if the backend starts emitting one
 *   3. 'business'                   - the safe default (justified below)
 *
 * ---------------------------------------------------------------------------
 * BACKEND DECISION NOT YET MADE - read this before changing the default.
 *
 * PHP cannot know a customer's segment until the backend says so, and two
 * plausible shapes exist. This function is written so either drops in without
 * touching the guards:
 *
 * (A) One shared `customers` table, new column.
 *     Add a segment column and return it from GET /api/v1/portal/profile.
 *     login.php and otp_verification.php already fetch a profile at sign-in, so
 *     storing $_SESSION['customer_type'] from it is a two-line change and step 1
 *     starts working on its own.
 *     WARNING: customers.tier already exists and means volume/contract tier,
 *     NOT portal segment - do not reuse it.
 *     WARNING: backend-api's data_loader.py already uses the NAME customer_type
 *     for 'new_customer' / 'active_customer', which is a lifecycle state. That is
 *     a different field wearing the same name; if that loader ever writes the same
 *     column the segment is silently clobbered. Prefer a distinct column name such
 *     as `portal_type` if the lifecycle field lands in the same table.
 *
 * (B) Separate Supabase projects per segment.
 *     Then the segment is derivable from *which* auth project issued the token,
 *     and the backend can stamp it into the JWT. Step 2 already reads such a
 *     claim, so this option needs no session plumbing at all.
 *
 * Until one is chosen, unknown falls back to BUSINESS rather than individual:
 * the B2B portal is the live, real-data surface, while customer_courier is still
 * demo data. Mis-routing a courier user costs them a wrong-looking dashboard;
 * mis-routing a business customer onto the courier portal would show them fake
 * shipments as if they were their own.
 * ---------------------------------------------------------------------------
 */
function resolve_customer_segment(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $fromSession = normalize_customer_segment($_SESSION['customer_type'] ?? null);
    if ($fromSession !== null) {
        return $fromSession;
    }

    $fromToken = jwt_customer_segment((string) ($_SESSION['access_token'] ?? ''));
    if ($fromToken !== null) {
        return $fromToken;
    }

    return CUSTOMER_SEGMENT_BUSINESS;
}

/**
 * Guard a customer-portal page. Call as the first statement of every page under
 * src/views/customer/ and src/views/customer_courier/.
 *
 *   require_once __DIR__ . '/../../helpers/portal_access.php';
 *   require_customer_portal(CUSTOMER_SEGMENT_BUSINESS);
 *
 * @param string|null $segment The segment this page belongs to. Pass null only
 *                             for a page that legitimately serves both portals.
 */
function require_customer_portal(?string $segment = null): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $base  = portal_base_prefix();
    $token = (string) ($_SESSION['access_token'] ?? '');
    $role  = strtolower((string) ($_SESSION['role'] ?? ''));

    /* 1. Anonymous -> login. `logged_out` is deliberately not set, so the user
          gets a clean form instead of a "you are signed out" screen they never
          asked for. */
    if ($token === '') {
        header('Location: ' . $base . 'login.php');
        exit();
    }

    /* 2. Staff do not belong in either customer portal. Send them to their own
          dashboard rather than a 403, so a mistyped URL self-corrects. An empty
          role falls through to the segment check, which is safe: resolve_
          customer_segment() defaults to business and the B2B pages then load
          their data through the API, which authenticates the token for real. */
    if ($role !== '' && $role !== 'customer') {
        $target = dashboard_for_role($role);
        if ($target !== null) {
            /* dashboard_for_role() returns paths relative to frontend/, which is
               exactly what $base points at. */
            header('Location: ' . $base . $target);
            exit();
        }
        http_response_code(403);
        exit('Your account is not authorised for the customer portal.');
    }

    /* 3. Segment mismatch -> the customer's own dashboard. Once the courier
          portal leaves demo data, a cross-portal visit stops being cosmetic:
          both surfaces show customer records, so this is the check that keeps
          one account's data out of the other's UI. */
    if ($segment !== null) {
        $wanted = normalize_customer_segment($segment);
        if ($wanted === null) {
            /* A typo in a call site must never widen access. */
            http_response_code(500);
            exit('Unknown customer portal segment.');
        }

        $actual = resolve_customer_segment();
        if ($actual !== $wanted) {
            $home = dashboard_for_role('customer', $actual);
            header('Location: ' . $base . ($home ?? 'src/views/customer/dashboard.php'));
            exit();
        }
    }
}

