<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- LEFT SIDE: 4 KPI CARDS (2x2 Grid) -->
    <div class="lg:col-span-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
        
        <!-- 1. Total Leads vs Last Month -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Leads</span>
                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900"><?php echo $kpis['total_leads_current_month'] ?? 0; ?></h3>
            <p class="text-[11px] text-slate-500 mt-1">Last Month: <span class="text-slate-700 font-medium"><?php echo $kpis['total_leads_last_month'] ?? 0; ?></span></p>
        </div>

        <!-- 2. Active Leads -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active Leads</span>
                <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900"><?php echo $kpis['active_leads'] ?? 0; ?></h3>
            <p class="text-[11px] text-amber-600 mt-1 font-medium">Currently active</p>
        </div>

        <!-- 3. Opportunities (Qualifying Status) -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Qualifying Opps</span>
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-filter-circle-dollar"></i>
                </div>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900"><?php echo $kpis['qualifying_opportunities'] ?? 0; ?></h3>
            <p class="text-[11px] text-indigo-600 mt-1 font-medium">Status: Qualifying</p>
        </div>

        <!-- 4. Total Closed-Won vs Total Closed-Lost -->
        <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Won / Lost</span>
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-lg font-extrabold text-emerald-600"><?php echo $kpis['total_closed_won'] ?? 0; ?> <span class="text-[10px] font-normal text-slate-400">Won</span></h3>
                <span class="text-slate-300">/</span>
                <h3 class="text-lg font-extrabold text-rose-600"><?php echo $kpis['total_closed_lost'] ?? 0; ?> <span class="text-[10px] font-normal text-slate-400">Lost</span></h3>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Closed Totals</p>
        </div>

    </div>

    <!-- RIGHT SIDE: APEXCHARTS CURVED LINE GRAPH -->
    <div class="lg:col-span-2 bg-white border border-slate-200 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-center mb-2">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Opportunities & Conversion Trend</h4>
                <p class="text-xs text-slate-500">Buwanang takbo ng mga kwalipikadong oportunidad</p>
            </div>
            <span class="bg-blue-50 text-blue-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">ApexCharts</span>
        </div>
        
        <!-- Container para sa ApexCharts Curve Line -->
        <div id="opportunitiesApexChart" class="w-full h-60"></div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const options = {
        series: [{
            name: 'Qualifying Opportunities',
            data: [12, 19, 15, 25, 22, 30, 35, 40, 38, 45, 50, <?php echo $kpis['qualifying_opportunities'] ?? 55; ?>]
        }],
        chart: {
            type: 'line',
            height: '240px',
            toolbar: {
                show: false
            },
            fontFamily: 'inherit'
        },
        stroke: {
            curve: 'smooth', // Ito ang nagbibigay ng pormang curve line graph
            width: 3
        },
        colors: ['#2563eb'], // Brand blue color
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.0,
                stops: [0, 90, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            xaxis: {
                lines: { show: false }
            },
            yaxis: {
                lines: { show: true }
            }
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            labels: {
                style: {
                    colors: '#64748b',
                    fontSize: '10px'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#64748b',
                    fontSize: '10px'
                }
            }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return val + " Opportunities";
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#opportunitiesApexChart"), options);
    chart.render();
});
</script>