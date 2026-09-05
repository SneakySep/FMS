// Unique name: several sales_agent scripts load on bi_analytics.php and share
// one global lexical scope, so a plain `const API_URL` collides across files.
const WIN_LOSS_API_URL = (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL)
    ? window.APP_CONFIG.API_BASE_URL
    : 'http://127.0.0.1:8000';

document.addEventListener("DOMContentLoaded", function () {
    async function fetchWinLossAnalytics() {
        try {
            const response = await fetch(`${WIN_LOSS_API_URL}/api/v1/analytics/win-loss-service`);
            const data = await response.json();

            if (data.status === 'success') {
                // Update Gemini AI Suggestion Box
                const suggestionEl = document.getElementById('ai-win-loss-suggestion');
                if (suggestionEl && data.ai_suggestion) {
                    suggestionEl.innerText = data.ai_suggestion;
                }

                // Render Chart
                renderWinLossChart(data.categories, data.series);
            }
        } catch (error) {
            console.error("Error fetching Win/Loss Analytics:", error);
            const suggestionEl = document.getElementById('ai-win-loss-suggestion');
            if (suggestionEl) {
                suggestionEl.innerText = "Unable to fetch AI analytics at this time.";
            }
        }
    }

    let winLossChartInstance = null;

   function renderWinLossChart(categories, series) {
    const chartElement = document.querySelector("#winLossStackedBarChart");
    if (!chartElement) return;

    if (winLossChartInstance) {
        winLossChartInstance.destroy();
    }

    // Won/lost take the semantic green/red the rest of the portal already uses
    // for those states, instead of a blue-vs-red palette that clashed with it.
    const P = (window.crmPalette || function () { return window.CRM_COLORS; })();

    const options = {
        series: series,
        chart: {
            type: 'bar',
            height: 220,
            stacked: true,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: [P.stage.won, P.stage.lost],
        fill: {
            opacity: 0.95
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '30%', // Sakto ang kapal sa kalahating card width
                borderRadius: 8,
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'last'
            }
        },
        dataLabels: { enabled: false },
        grid: {
            borderColor: P.grid,
            strokeDashArray: 3,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        xaxis: {
            categories: categories,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: P.muted,
                    fontSize: '12px',
                    fontWeight: 600
                }
            }
        },
        yaxis: {
            forceNiceScale: true,
            min: 0,
            labels: {
                style: {
                    colors: P.muted,
                    fontSize: '12px',
                    fontWeight: 500
                },
                formatter: (val) => Math.floor(val)
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '12px',
            fontWeight: 600,
            offsetY: -5,
            labels: { colors: P.muted },
            markers: { width: 8, height: 8, radius: 12 }
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

    winLossChartInstance = new ApexCharts(chartElement, options);
    winLossChartInstance.render();
}

    fetchWinLossAnalytics();
});