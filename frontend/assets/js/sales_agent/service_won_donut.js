// Unique name to avoid a global `const` redeclaration against the other
// analytics scripts loaded on the same page.
const DONUT_API_URL = (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL)
    ? window.APP_CONFIG.API_BASE_URL
    : 'http://127.0.0.1:8000';

document.addEventListener("DOMContentLoaded", function () {
    async function fetchServiceWonDistribution() {
        try {
            const response = await fetch(`${DONUT_API_URL}/api/v1/analytics/service-won-distribution`);
            const data = await response.json();

            if (data.status === 'success') {
                // Update Gemini AI Suggestion Box
                const suggestionEl = document.getElementById('ai-service-donut-suggestion');
                if (suggestionEl && data.ai_suggestion) {
                    suggestionEl.innerText = data.ai_suggestion;
                }

                // Render Donut Chart
                renderDonutChart(data.labels, data.series);
            }
        } catch (error) {
            console.error("Error fetching Service Distribution:", error);
            const suggestionEl = document.getElementById('ai-service-donut-suggestion');
            if (suggestionEl) {
                suggestionEl.innerText = "Unable to fetch service distribution data.";
            }
        }
    }

    let donutChartInstance = null;

    function renderDonutChart(labels, series) {
        const chartElement = document.querySelector("#serviceWonDonutChart");
        if (!chartElement) return;

        // Sequential navy ramp from the theme tokens - replaces the five
        // hand-picked blues this card used to invent for itself.
        const P = (window.crmPalette || function () { return window.CRM_COLORS; })();

        if (donutChartInstance) {
            donutChartInstance.destroy();
        }

        const options = {
            series: series.length > 0 ? series : [1],
            labels: labels.length > 0 ? labels : ['No Data'],
            chart: {
                type: 'donut',
                height: 210,
                fontFamily: 'Inter, sans-serif'
            },
            colors: P.chart,
            stroke: {
                show: true,
                colors: [P.surface],
                width: 2
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Won',
                                fontSize: '12px',
                                fontWeight: 600,
                                color: P.muted,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + " deals";
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(1) + "%";
                },
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 'bold'
                },
                dropShadow: { enabled: false }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: 600,
                labels: { colors: P.muted },
                markers: { width: 8, height: 8, radius: 12 },
                itemMargin: { horizontal: 6, vertical: 2 }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " deals";
                    }
                }
            }
        };

        donutChartInstance = new ApexCharts(chartElement, options);
        donutChartInstance.render();
    }

    fetchServiceWonDistribution();
});