<?php
$pageTitle = 'SwiftFreight - Analytics Control';
$activePage = 'analytics';
$chatMessage = trim(<<<'MSG'
Mabuhay! Need help interpreting analytics data? Chat with us here.
MSG);
include 'includes/head.php';
include 'includes/sidebar.php';
?>


    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Analytics Suite</h2>
            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors"><i class="fa-solid fa-bell text-xs"></i><span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span></button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">Help Desk <i class="fa-solid fa-headset text-xs"></i></button>
                <button onclick="alert('Downloading analytics report (SwiftFreight_Analytics_Q3.pdf)...')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-slate-200"><i class="fa-solid fa-file-export text-xs"></i> Report</button>
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            <!-- KPI CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">Total Shipments Volume</span>
                    <strong id="analyticsShipmentVolume" class="text-4xl font-black text-slate-900 block">24</strong>
                    <span class="text-[10px] font-semibold text-emerald-500">▲ +18% vs last quarter</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">On-Time Delivery Rate</span>
                    <strong id="analyticsOnTime" class="text-4xl font-black text-slate-900 block">94.2%</strong>
                    <span class="text-[10px] font-semibold text-emerald-500">▲ +2.1% vs last quarter</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">Invoiced Revenue</span>
                    <strong id="analyticsRevenue" class="text-4xl font-black text-slate-900 block">₱1.2M</strong>
                    <span class="text-[10px] font-semibold text-emerald-500">▲ +5.4% vs last quarter</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 reveal">
                    <span class="text-xs font-semibold text-slate-500 block">Customer Satisfaction</span>
                    <strong id="analyticsCsat" class="text-4xl font-black text-slate-900 block">4.8</strong>
                    <span class="text-[10px] font-semibold text-emerald-500">▲ +0.2 vs last quarter</span>
                </div>
            </div>

            <!-- CHARTS ROW -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 reveal">
                    <h3 class="text-base font-extrabold text-slate-900">Shipment Volume by Status</h3>
                    <div class="h-64"><canvas id="analyticsStatusChart"></canvas></div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 reveal">
                    <h3 class="text-base font-extrabold text-slate-900">Monthly Shipment Trends</h3>
                    <div class="h-64"><canvas id="analyticsTrendChart"></canvas></div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/chat-widget.php'; ?>

    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const store = getStore();

            const statusCounts = { 'In Transit': 0, 'Customs': 0, 'Delivered': 0, 'Delayed': 0 };
            store.shipments.forEach(s => { if (statusCounts[s.status] !== undefined) statusCounts[s.status]++; });
            const sum = Object.values(statusCounts).reduce((a, b) => a + b, 0) || 1;

            document.getElementById('analyticsShipmentVolume').innerText = sum;
            document.getElementById('analyticsOnTime').innerText = store.sla.overall + '%';

            if (window.Chart) {
                const statusChart = new Chart(document.getElementById('analyticsStatusChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['In Transit', 'Customs', 'Delivered', 'Delayed'],
                        datasets: [{
                            data: [statusCounts['In Transit'], statusCounts['Customs'], statusCounts['Delivered'], statusCounts['Delayed']],
                            backgroundColor: ['#0066ff', '#f59e0b', '#10b981', '#ef4444'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11 } } } } }
                });

                const trendChart = new Chart(document.getElementById('analyticsTrendChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [{
                            label: 'Shipments',
                            data: [12, 15, 14, 18, 20, 22, sum + 17],
                            borderColor: '#0066ff',
                            backgroundColor: 'rgba(0,102,255,0.1)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#0066ff',
                            pointRadius: 4
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
                });
            }
        });
    </script>
</body>
</html>