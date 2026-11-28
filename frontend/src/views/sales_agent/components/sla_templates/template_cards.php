<?php
$sla_templates = $sla_templates ?? [];

/**
 * Mode-based design tokens para sa SLA template card.
 * Isang lugar lang ang color logic (kapareho ng badge helper pattern).
 */
if (!function_exists('getSlaTemplateModeStyle')) {
    function getSlaTemplateModeStyle($mode)
    {
        switch (strtolower(trim((string) $mode))) {
            case 'land':
                return [
                    'icon' => 'fa-truck-fast',
                    'chip' => 'bg-indigo-50 text-indigo-600',
                    'pill' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                    'bar'  => 'from-indigo-500 to-indigo-400',
                ];
            case 'sea':
                return [
                    'icon' => 'fa-ship',
                    'chip' => 'bg-blue-50 text-blue-600',
                    'pill' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'bar'  => 'from-blue-500 to-cyan-400',
                ];
            case 'air':
                return [
                    'icon' => 'fa-plane',
                    'chip' => 'bg-purple-50 text-purple-600',
                    'pill' => 'bg-purple-100 text-purple-700 border-purple-200',
                    'bar'  => 'from-purple-500 to-fuchsia-400',
                ];
            default:
                return [
                    'icon' => 'fa-file-shield',
                    'chip' => 'bg-slate-50 text-slate-600',
                    'pill' => 'bg-slate-100 text-slate-600 border-slate-200',
                    'bar'  => 'from-slate-400 to-slate-300',
                ];
        }
    }
}

/**
 * Mode badge styles (pinapanatili para sa compatibility).
 */
if (!function_exists('getSlaTemplateModeBadge')) {
    function getSlaTemplateModeBadge($mode)
    {
        $style = getSlaTemplateModeStyle($mode);
        return $style['pill'];
    }
}

/**
 * Tone ng on-time target bar batay sa threshold.
 */
if (!function_exists('getSlaTemplateOntimeTone')) {
    function getSlaTemplateOntimeTone($pct)
    {
        $pct = (float) $pct;
        if ($pct >= 95) {
            return ['fill' => 'bg-emerald-500', 'text' => 'text-emerald-600'];
        }
        if ($pct >= 90) {
            return ['fill' => 'bg-amber-500', 'text' => 'text-amber-600'];
        }
        return ['fill' => 'bg-rose-500', 'text' => 'text-rose-600'];
    }
}

/**
 * Helper: i-format ang numeric value (alisin ang trailing .0) para
 * maging "95" o "98.5" sa halip na "95.0".
 */
if (!function_exists('slaTemplateNumber')) {
    function slaTemplateNumber($value)
    {
        return rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.');
    }
}
?>

<?php if (empty($sla_templates)): ?>
    <!-- EMPTY STATE -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10 text-center">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-file-shield text-lg"></i>
        </div>
        <h4 class="text-sm font-bold text-slate-800">Walang SLA Template na nahanap</h4>
        <p class="text-xs text-slate-500 mt-1">Wala pang reusable na service terms na naka-save. Gumawa ng template para magamit sa mga bagong contract.</p>
    </div>
<?php else: ?>
    <!-- SLA TEMPLATE CARD GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($sla_templates as $template): ?>
            <?php
            $name   = $template['name'] ?? 'Untitled Template';
            $mode   = trim((string) ($template['mode'] ?? ''));
            $active = !empty($template['is_active']);

            // Mode design tokens (muted kapag inactive)
            $style = getSlaTemplateModeStyle($mode);
            if (!$active) {
                $style['chip'] = 'bg-slate-100 text-slate-400';
                $style['pill'] = 'bg-slate-100 text-slate-500 border-slate-200';
                $style['bar']  = 'from-slate-300 to-slate-200';
            }

            // On-time target (headline metric)
            $ontime      = (float) ($template['ontime_target_pct'] ?? 0);
            $ontime_tone = getSlaTemplateOntimeTone($ontime);
            $ontime_w    = max(0, min(100, $ontime));

            // Ibang terms (on-time target ay nasa progress bar na)
            $terms = [
                ['label' => 'Transit',           'icon' => 'fa-road',               'value' => (int) ($template['transit_days'] ?? 0) . ' days'],
                ['label' => 'Pickup window',     'icon' => 'fa-clock',              'value' => (int) ($template['pickup_window_hours'] ?? 0) . ' hrs'],
                ['label' => 'Tracking interval', 'icon' => 'fa-satellite-dish',     'value' => (int) ($template['tracking_interval_hours'] ?? 0) . ' hrs'],
                ['label' => 'Claims response',   'icon' => 'fa-headset',            'value' => (int) ($template['claims_response_hours'] ?? 0) . ' hrs'],
                ['label' => 'Late credit / day', 'icon' => 'fa-percent',            'value' => slaTemplateNumber($template['late_credit_pct_per_day'] ?? 0) . '%'],
                ['label' => 'Credit cap',        'icon' => 'fa-hand-holding-dollar', 'value' => slaTemplateNumber($template['credit_cap_pct'] ?? 0) . '%'],
                ['label' => 'Liability limit',   'icon' => 'fa-shield-halved',      'value' => slaTemplateNumber($template['liability_limit_pct'] ?? 0) . '%'],
            ];
            ?>
            <!-- TEMPLATE CARD -->
            <div class="group bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden <?php echo $active ? '' : 'opacity-80'; ?> hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

                <!-- TOP ACCENT BAR -->
                <div class="h-1.5 w-full bg-gradient-to-r <?php echo $style['bar']; ?>"></div>

                <div class="p-5">

                    <!-- CARD HEADER: ICON CHIP + NAME + BADGES -->
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl <?php echo $style['chip']; ?> flex items-center justify-center text-sm shrink-0">
                            <i class="fa-solid <?php echo $style['icon']; ?>"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-extrabold text-slate-900 leading-snug"><?php echo htmlspecialchars($name); ?></h3>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                <?php if ($mode !== ''): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-[11px] font-semibold capitalize <?php echo $style['pill']; ?>">
                                        <?php echo htmlspecialchars($mode); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!$active): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg border text-[11px] font-semibold <?php echo getSlaTemplateBadge(false); ?>">
                                        Inactive
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ON-TIME TARGET PROGRESS -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">On-time target</span>
                            <span class="text-xs font-extrabold <?php echo $ontime_tone['text']; ?>"><?php echo slaTemplateNumber($ontime); ?>%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full <?php echo $ontime_tone['fill']; ?>" style="width: <?php echo $ontime_w; ?>%;"></div>
                        </div>
                    </div>

                    <!-- TERM ROWS -->
                    <dl class="mt-4 divide-y divide-slate-100">
                        <?php foreach ($terms as $term): ?>
                            <div class="flex items-center justify-between gap-3 py-2.5 px-1 rounded-lg hover:bg-slate-50/70 transition-colors">
                                <dt class="flex items-center gap-2 text-xs text-slate-500 min-w-0">
                                    <i class="fa-solid <?php echo $term['icon']; ?> text-[10px] text-slate-400 w-3 text-center shrink-0"></i>
                                    <span class="truncate"><?php echo htmlspecialchars($term['label']); ?></span>
                                </dt>
                                <dd class="shrink-0">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-50 border border-slate-100 text-xs font-semibold text-slate-800">
                                        <?php echo htmlspecialchars($term['value']); ?>
                                    </span>
                                </dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
