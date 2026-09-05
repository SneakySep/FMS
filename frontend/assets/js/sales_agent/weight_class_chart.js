// Unique name to avoid a global `const` redeclaration against the other
// analytics scripts loaded on the same page.
const WEIGHT_CLASS_API_URL = (window.APP_CONFIG && window.APP_CONFIG.API_BASE_URL)
    ? window.APP_CONFIG.API_BASE_URL
    : 'http://127.0.0.1:8000';

document.addEventListener("DOMContentLoaded", function () {
    async function fetchWeightClassAnalytics() {
        try {
            const response = await fetch(`${WEIGHT_CLASS_API_URL}/api/v1/analytics/weight-class-win-loss`);
            const data = await response.json();

            if (data.status === 'success') {
                renderWeightClassChart(data.categories, data.series);
            }
        } catch (error) {
            console.error("Error fetching Weight Class analytics:", error);
        }
    }

    let weightChartInstance = null;

    function renderWeightClassChart(categories, series) {
        const chartElement = document.querySelector("#weightClassBarChart");
        if (!chartElement) return;

        // Transport modes stay distinguishable but come off one navy ramp:
        // Air = sky-brand, Sea = brand navy, Land = navy-200.
        const P = (window.crmPalette || function () { return window.CRM_COLORS; })();

        if (weightChartInstance) {
            weightChartInstance.destroy();
        }

        const options = {
            series: series,
            chart: {
                type: 'bar',
                height: 250,
                stacked: true,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: [P.chart[2], P.chart[0], P.chart[4]],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '35%',
                    borderRadius: 6
                }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '12px', fontWeight: 'bold' }
            },
            grid: {
                borderColor: P.grid,
                strokeDashArray: 3
            },
            xaxis: {
                categories: categories,
                labels: { style: { colors: P.muted, fontWeight: 600, fontSize: '12px' } }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: 600
            },
            tooltip: {
                shared: false,
                intersect: true,
                custom: function({ seriesIndex, dataPointIndex, w }) {
                    const sName = w.globals.seriesNames[seriesIndex];
                    const val = w.globals.series[seriesIndex][dataPointIndex];
                    const details = w.config.series[seriesIndex].details[dataPointIndex];
                    
                    let weightsHtml = details.length > 0 
                        ? details.map((w, i) => `<div class="text-[10px] text-slate-500">${i + 1}. ${w}</div>`).join('') 
                        : '<div class="text-[10px] text-slate-400">No weight recorded</div>';

                    return `
                        <div class="p-2.5 bg-white border border-slate-200 rounded-xl shadow-md">
                            <div class="font-bold text-xs text-slate-800">${sName}</div>
                            <div class="text-xs text-indigo-600 font-semibold mb-1">${val} deal(s)</div>
                            <div class="border-t border-slate-100 pt-1">
                                <div class="text-[10px] font-bold text-slate-600 uppercase">Cargo Weights:</div>
                                ${weightsHtml}
                            </div>
                        </div>
                    `;
                }
            }
        };

        weightChartInstance = new ApexCharts(chartElement, options);
        weightChartInstance.render();
    }

    fetchWeightClassAnalytics();
});