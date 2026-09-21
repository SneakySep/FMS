<div class="p-6 space-y-8 bg-slate-50 text-slate-800 min-h-screen">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                <span class="p-2 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                Prescriptive Analytics Engine
            </h1>
            <p class="text-sm text-slate-500 mt-1">AI-driven lead prioritization, smart follow-up alerts, and dynamic rate optimization.</p>
        </div>
        <button id="btn-refresh-prescriptive" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition-all duration-200 shadow-sm shadow-indigo-200 active:scale-95">
            <svg id="refresh-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh Analytics
        </button>
    </div>

    <!-- Alert Banner: Follow-up Alerts -->
    <div id="alerts-container" class="space-y-3 hidden">
        <!-- Dynamic Alerts inserted via JS -->
    </div>

    <!-- Top Grid: Lead Prioritization & Next Best Action -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Lead Prioritization Table (Spans 2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Prioritized High-Value Leads
                </h2>
                <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200 font-medium">ML Ranked</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3">Company</th>
                            <th class="py-3 px-3">Est. Amount</th>
                            <th class="py-3 px-3">Win Prob.</th>
                            <th class="py-3 px-3 text-center">Score</th>
                            <th class="py-3 px-3 text-right">Urgency</th>
                        </tr>
                    </thead>
                    <tbody id="leads-table-body" class="divide-y divide-slate-100">
                        <!-- Dynamic Rows -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Next Best Action Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        Next Best Action
                    </h2>
                </div>
                <div id="next-action-container" class="space-y-4">
                    <!-- Dynamic Next Action Content -->
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-400">
                AI Recommendation based on lead score & response latency.
            </div>
        </div>
    </div>

    <!-- Bottom Grid: Dynamic Pricing & Gemini Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Dynamic Pricing Suggestions -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Dynamic Pricing Suggestions
            </h2>
            <div id="pricing-suggestions-list" class="space-y-3">
                <!-- Dynamic Items -->
            </div>
        </div>

        <!-- Gemini AI Won/Lost Performance Strategy -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm border-l-4 border-l-indigo-600">
            <h2 class="text-base font-semibold text-slate-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Gemini Performance Analysis
            </h2>
            
            <div id="won-lost-summary-box" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-700 mb-4">
                Loading AI synthesis...
            </div>

            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Actionable Recommendations</h3>
            <ul id="won-lost-recommendations-list" class="space-y-2 text-sm text-slate-700">
                <!-- Dynamic Bullet Points -->
            </ul>
        </div>
    </div>
</div>

<!-- Load Separate JS File -->
<script src="../../../../assets/js/admin/prescriptive_analytics.js"></script>