/**
 * FMS theme controller (graphite dark mode).
 *
 * Loaded in <head> immediately after the ApexCharts CDN so it can also theme
 * charts. The initial `dark` class is applied by the anti-flash inline script
 * in src/components/head.php and src/includes/header.php; this file owns the
 * toggle button, localStorage persistence, OS-preference sync, and ApexCharts
 * theming (via a transparent wrapper around the ApexCharts constructor).
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'fms-theme';
    var mq = window.matchMedia('(prefers-color-scheme: dark)');

    function isDark() {
        return document.documentElement.classList.contains('dark');
    }

    // Resolve the effective theme: explicit choice wins, otherwise OS.
    function preferredIsDark() {
        var saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'dark') return true;
        if (saved === 'light') return false;
        return mq.matches;
    }

    function emit() {
        document.dispatchEvent(new CustomEvent('fms:themechange', { detail: { dark: isDark() } }));
    }

    // theme: 'dark' | 'light' | 'system'
    function setTheme(theme) {
        if (theme === 'system') {
            localStorage.removeItem(STORAGE_KEY);
        } else {
            localStorage.setItem(STORAGE_KEY, theme);
        }
        document.documentElement.classList.toggle('dark', preferredIsDark());
        syncButtons();
        emit();
    }

    function toggle() {
        setTheme(isDark() ? 'light' : 'dark');
    }

    function syncButtons() {
        var dark = isDark();
        document.querySelectorAll('[data-theme-toggle]').forEach(function (btn) {
            btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
            btn.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');
            var icon = btn.querySelector('i');
            if (icon) icon.className = dark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        });
    }

    // Follow the OS only while the user hasn't made an explicit choice.
    mq.addEventListener('change', function () {
        if (!localStorage.getItem(STORAGE_KEY)) {
            document.documentElement.classList.toggle('dark', mq.matches);
            syncButtons();
            emit();
        }
    });

    document.addEventListener('DOMContentLoaded', syncButtons);

    window.FMSTheme = {
        isDark: isDark,
        toggle: toggle,
        setTheme: setTheme,
        syncButtons: syncButtons
    };

    /* ---- ApexCharts graphite theming ----------------------------------- */

    function palette() {
        var dark = isDark();
        return {
            fore: dark ? '#A2A6AD' : '#334155',
            label: dark ? '#A2A6AD' : '#64748b',
            axis: dark ? '#2B2E34' : '#E2E8F0',
            grid: dark ? '#2B2E34' : '#F1F5F9',
            tooltip: dark ? 'dark' : 'light',
            ring: dark ? '#17181B' : '#ffffff'
        };
    }

    // ApexCharts accepts a single axis object or an array of them.
    function themeAxis(axis, t) {
        if (!axis) return axis;
        if (Array.isArray(axis)) {
            return axis.map(function (a) { return themeAxis(a, t); });
        }
        var out = Object.assign({}, axis);
        out.labels = Object.assign({}, axis.labels, {
            style: Object.assign({}, axis.labels && axis.labels.style, { colors: t.label })
        });
        out.axisBorder = Object.assign({}, axis.axisBorder, { color: t.axis });
        return out;
    }

    // Merge graphite-aware colors into a chart's options (used at construction
    // and again on theme change).
    function themed(options) {
        var t = palette();
        var out = Object.assign({}, options);
        out.chart = Object.assign({}, options.chart, { foreColor: t.fore });
        out.grid = Object.assign({}, options.grid, { borderColor: t.grid });
        out.tooltip = Object.assign({}, options.tooltip, { theme: t.tooltip });
        if (options.xaxis) out.xaxis = themeAxis(options.xaxis, t);
        if (options.yaxis) out.yaxis = themeAxis(options.yaxis, t);
        if (options.legend) {
            out.legend = Object.assign({}, options.legend, {
                labels: Object.assign({}, options.legend.labels, { colors: t.label })
            });
        }
        // Donut charts draw their ring with a single white stroke color; swap
        // it for the card surface so the ring blends into graphite.
        if (options.stroke && Array.isArray(options.stroke.colors) &&
            options.stroke.colors.length === 1 &&
            String(options.stroke.colors[0]).toLowerCase() === '#ffffff') {
            out.stroke = Object.assign({}, options.stroke, { colors: [t.ring] });
        }
        return out;
    }

    if (typeof window.ApexCharts === 'function' && !window.ApexCharts.__fmsThemed) {
        var Original = window.ApexCharts;
        var instances = [];

        function ThemedApexCharts(el, options) {
            var instance = new Original(el, themed(options || {}));
            instances.push(instance);
            return instance;
        }
        ThemedApexCharts.prototype = Original.prototype;
        Object.keys(Original).forEach(function (key) {
            ThemedApexCharts[key] = Original[key];
        });
        ThemedApexCharts.__fmsThemed = true;
        window.ApexCharts = ThemedApexCharts;

        document.addEventListener('fms:themechange', function () {
            var t = palette();
            instances.forEach(function (inst) {
                try {
                    inst.updateOptions({
                        chart: { foreColor: t.fore },
                        grid: { borderColor: t.grid },
                        tooltip: { theme: t.tooltip },
                        xaxis: { labels: { style: { colors: t.label } }, axisBorder: { color: t.axis } },
                        yaxis: { labels: { style: { colors: t.label } } },
                        legend: { labels: { colors: t.label } }
                    });
                } catch (e) {
                    /* chart was destroyed */
                }
            });
        });
    }
})();
