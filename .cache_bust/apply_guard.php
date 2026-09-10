<?php
/*
 * One-shot migration: insert the segment guard into every customer portal page.
 *
 * The guard has to run before ANY output. Both portals emit their first bytes
 * from an include (header.php for B2B, PageLayout via bootstrap for courier),
 * so the anchor is that first include line, and the guard goes immediately
 * above it. Doing it this way also preserves each file's existing header comment.
 *
 * Safe to re-run: a file that already mentions require_customer_portal is skipped.
 */

$views = __DIR__ . '/../frontend/src/views';

$targets = [
    'business' => [
        'dir'    => $views . '/customer',
        'anchor' => "header.php",
        'const'  => 'CUSTOMER_SEGMENT_BUSINESS',
        'note'   => "// Segment guard: B2B portal only. Sends anonymous users to login,\n// staff to their own dashboard, and courier (individual) customers to their\n// own portal. See src/helpers/portal_access.php.",
    ],
    'individual' => [
        'dir'    => $views . '/customer_courier',
        'anchor' => "bootstrap.php",
        'const'  => 'CUSTOMER_SEGMENT_INDIVIDUAL',
        'note'   => "// Segment guard: courier (C2B) portal only. Sends anonymous users to login,\n// staff to their own dashboard, and business customers to the B2B portal.\n// See src/helpers/portal_access.php.",
    ],
];

foreach ($targets as $seg => $cfg) {
    foreach (glob($cfg['dir'] . '/*.php') as $file) {
        $src = file_get_contents($file);

        if (strpos($src, 'require_customer_portal') !== false) {
            echo "SKIP  (already guarded) " . basename($file) . "\n";
            continue;
        }

        /* Anchor on the first require/include line that pulls in the chrome. */
        /* [^'\";]* skips the `__DIR__ . ` concatenation; \r? is required because
           these files are CRLF and $ only matches before the \n. */
        if (!preg_match('/^([ \t]*)(?:require|include)(?:_once)?[ \t]+[^\'";]*[\'"][^\r\n]*?' . preg_quote($cfg['anchor'], '/') . '[^\r\n]*;[ \t]*\r?$/m', $src, $m)) {
            echo "MISS  (no anchor '" . $cfg['anchor'] . "') " . basename($file) . "\n";
            continue;
        }

        $indent   = $m[1];
        $block    = $indent . "require_once __DIR__ . '/../../helpers/portal_access.php';\n"
                  . $indent . $cfg['note'] . "\n"
                  . $indent . "require_customer_portal(" . $cfg['const'] . ");\n"
                  . $indent . "\n";

        /* Split on the raw line including its newline, preserving CRLF vs LF. */
        $needle   = $m[0];
        $pos      = strpos($src, $needle);
        $eol      = (strpos($src, "\r\n") !== false) ? "\r\n" : "\n";
        $insertAt = $pos;
        $blockOut = str_replace("\n", $eol, $block);

        $out = substr($src, 0, $insertAt) . $blockOut . substr($src, $insertAt);
        file_put_contents($file, $out);
        echo "GUARD $seg  " . basename($file) . "\n";
    }
}
