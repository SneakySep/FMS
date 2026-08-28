<?php
$page_title = "Live Tracking · Priority Handling Logistics";
$activePage = 'tracking';
$extraHead = '
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    .track-card { background:#fff; border:1px solid #e2e8f0; border-radius:1rem; box-shadow:0 1px 3px 0 rgba(0,0,0,.04); }
    .track-scroll { scrollbar-width:thin; scrollbar-color:#cbd5e1 #f1f5f9; }
    .track-scroll::-webkit-scrollbar { width:5px; }
    .track-scroll::-webkit-scrollbar-track { background:#f1f5f9; }
    .track-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:99px; }
    .ms-item { position:relative; }
    .ms-dot { width:14px; height:14px; border-radius:9999px; flex:0 0 auto; box-shadow:0 0 0 4px #fff; }
    .ms-line { position:absolute; left:6.5px; top:22px; bottom:-10px; width:2px; background:#e2e8f0; }
    .live-pulse { position:absolute; width:18px; height:18px; border-radius:9999px; background:#0066ff; box-shadow:0 0 0 0 rgba(0,102,255,.55); animation:livePulse 1.8s infinite; }
    @keyframes livePulse { 0%{box-shadow:0 0 0 0 rgba(0,102,255,.55);} 70%{box-shadow:0 0 0 16px rgba(0,102,255,0);} 100%{box-shadow:0 0 0 0 rgba(0,102,255,0);} }
    .leaflet-popup-content-wrapper { border-radius:12px; }
    .fade-in { animation:fadeIn .4s ease-out both; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(8px);} to{opacity:1;transform:translateY(0);} }
  </style>
';
require_once '../../includes/header.php';
include_once '../../includes/sidebar.php';

/* Shipment data source (single source of truth; mirrors shipments.php demo set).
   Swap for make_api_request('/api/v1/portal/shipments','GET') in production. */
$shipments = [
  ['waybill'=>'PH-WB-208841','type'=>'40ft container · Reefer','route'=>'Manila → Cebu','origin'=>'Manila','destination'=>'Cebu','carrier'=>'Trans-Pacific Lines','status'=>'in-transit','progress'=>62,'eta'=>'2026-08-30 14:00:00','from'=>[14.5995,120.9842],'to'=>[10.3157,123.8854],'live'=>[12.4,122.3],'next'=>'Cebu Port Terminal','next_date'=>'Aug 30',
    'milestones'=>[['label'=>'Booked','loc'=>'Manila Hub','time'=>'Aug 22, 09:12','done'=>true],['label'=>'Picked Up','loc'=>'Manila, PH','time'=>'Aug 23, 11:40','done'=>true],['label'=>'Origin Hub','loc'=>'Manila Container Yard','time'=>'Aug 24, 06:05','done'=>true],['label'=>'In Transit','loc'=>'At sea · Vessel M/V Luzon','time'=>'Aug 25, 18:30','done'=>true],['label'=>'Customs Clearance','loc'=>'Cebu Customs','time'=>'Pending','done'=>false],['label'=>'Destination Hub','loc'=>'Cebu Port Terminal','time'=>'Pending','done'=>false],['label'=>'Delivered','loc'=>'Consignee · Cebu','time'=>'Pending','done'=>false]]],
  ['waybill'=>'PH-WB-208835','type'=>'20ft container · Dry van','route'=>'Cebu → Manila','origin'=>'Cebu','destination'=>'Manila','carrier'=>'2GO Freight','status'=>'customs','progress'=>78,'eta'=>'2026-08-29 09:00:00','from'=>[10.3157,123.8854],'to'=>[14.5995,120.9842],'live'=>[13.9,121.8],'next'=>'Manila Customs Zone','next_date'=>'Aug 29',
    'milestones'=>[['label'=>'Booked','loc'=>'Cebu Hub','time'=>'Aug 20, 10:00','done'=>true],['label'=>'Picked Up','loc'=>'Cebu, PH','time'=>'Aug 21, 14:20','done'=>true],['label'=>'Origin Hub','loc'=>'Cebu Container Yard','time'=>'Aug 21, 19:05','done'=>true],['label'=>'In Transit','loc'=>'At sea · Vessel M/V Visayas','time'=>'Aug 22, 08:00','done'=>true],['label'=>'Customs Clearance','loc'=>'Manila Customs','time'=>'Aug 28, 13:30','done'=>true],['label'=>'Destination Hub','loc'=>'Manila Port Terminal','time'=>'Pending','done'=>false],['label'=>'Delivered','loc'=>'Consignee · Manila','time'=>'Pending','done'=>false]]],
  ['waybill'=>'PH-WB-208790','type'=>'40ft container · Dry van','route'=>'Davao → Manila','origin'=>'Davao','destination'=>'Manila','carrier'=>'Sulpicio Lines','status'=>'delayed','progress'=>41,'eta'=>'2026-08-31 18:00:00','from'=>[7.1907,125.4553],'to'=>[14.5995,120.9842],'live'=>[10.2,124.0],'next'=>'Open-sea leg (weather delay)','next_date'=>'Aug 31',
    'milestones'=>[['label'=>'Booked','loc'=>'Davao Hub','time'=>'Aug 19, 08:00','done'=>true],['label'=>'Picked Up','loc'=>'Davao, PH','time'=>'Aug 20, 10:10','done'=>true],['label'=>'Origin Hub','loc'=>'Davao Container Yard','time'=>'Aug 20, 15:00','done'=>true],['label'=>'In Transit','loc'=>'At sea · Vessel M/V Mindanao','time'=>'Aug 21, 07:00','done'=>true],['label'=>'Customs Clearance','loc'=>'Manila Customs','time'=>'Pending','done'=>false],['label'=>'Destination Hub','loc'=>'Manila Port Terminal','time'=>'Pending','done'=>false],['label'=>'Delivered','loc'=>'Consignee · Manila','time'=>'Pending','done'=>false]]],
];
$total = count($shipments);
$inTransit = $delayed = 0;
foreach ($shipments as $s) { if ($s['status']==='in-transit') $inTransit++; if ($s['status']==='delayed') $delayed++; }
$slaOnTime = 94;
function trackBadge($status){ switch($status){
  case 'in-transit': return ['label'=>'In Transit','class'=>'bg-blue-50 text-blue-700 border-blue-200','dot'=>'bg-blue-500'];
  case 'customs':    return ['label'=>'Customs','class'=>'bg-amber-50 text-amber-700 border-amber-200','dot'=>'bg-amber-500'];
  case 'delayed':    return ['label'=>'Delayed','class'=>'bg-rose-50 text-rose-700 border-rose-200','dot'=>'bg-rose-500'];
  case 'delivered':  return ['label'=>'Delivered','class'=>'bg-emerald-50 text-emerald-700 border-emerald-200','dot'=>'bg-emerald-500'];
  default:           return ['label'=>ucfirst($status),'class'=>'bg-slate-50 text-slate-700 border-slate-200','dot'=>'bg-slate-400'];
}}
?>


    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col min-w-0">

        <!-- TOP HEADER BAR -->
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button onclick="toggleSidebar()" class="sm:hidden text-slate-600 hover:text-slate-900 p-1.5 shrink-0">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="min-w-0">
                    <h2 class="text-2xl font-black italic text-slate-900 tracking-tight">Live Tracking</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Real-time status, milestone timeline &amp; GPS route map</p>
                </div>
            </div>

            <div class="flex-1 max-w-md mx-auto order-3 sm:order-none w-full sm:w-auto">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="liveTrackInput" placeholder="Track a waybill, invoice, or document..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-blue focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative w-9 h-9 bg-slate-100 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-bell text-xs"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="toggleChat()" class="bg-blue-50 hover:bg-blue-100 text-brand-blue font-semibold text-xs px-4 py-2 rounded-xl transition-colors flex items-center gap-2 border border-blue-100">
                    Help Desk <i class="fa-solid fa-headset text-xs"></i>
                </button>
                <button onclick="alert('Opening Freight Booking Form...')" class="bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                    + Book Shipment
                </button>
            </div>
        </header>

        <!-- LIVE TRACKING CONTENT BODY -->
        <div class="p-6 lg:p-8 w-full space-y-6">

            <!-- KPI STRIP -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <?php
                $kpis = [
                  ['label'=>'Active Shipments','value'=>$total,'sub'=>'+'.($total-1).' vs last week','icon'=>'fa-boxes-stacked','tone'=>'blue','up'=>true],
                  ['label'=>'In Transit','value'=>$inTransit,'sub'=>'Vessels at sea','icon'=>'fa-ship','tone'=>'blue','up'=>true],
                  ['label'=>'Delayed','value'=>$delayed,'sub'=>'Action needed','icon'=>'fa-triangle-exclamation','tone'=>'rose','up'=>false],
                  ['label'=>'On-Time SLA','value'=>$slaOnTime.'%','sub'=>'+2% this month','icon'=>'fa-gauge-high','tone'=>'emerald','up'=>true],
                ];
                $toneMap = [
                  'blue'=>['bg'=>'bg-blue-50','tx'=>'text-brand-blue','ring'=>'ring-blue-100'],
                  'rose'=>['bg'=>'bg-rose-50','tx'=>'text-rose-600','ring'=>'ring-rose-100'],
                  'emerald'=>['bg'=>'bg-emerald-50','tx'=>'text-emerald-600','ring'=>'ring-emerald-100'],
                ];
                foreach ($kpis as $k): $t=$toneMap[$k['tone']];
                ?>
                <div class="track-card p-5 flex items-center gap-4 fade-in">
                    <div class="w-11 h-11 rounded-xl <?= $t['bg'] ?> <?= $t['tx'] ?> flex items-center justify-center ring-4 <?= $t['ring'] ?>">
                        <i class="fa-solid <?= $k['icon'] ?>"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider"><?= $k['label'] ?></p>
                        <p class="text-xl font-black text-slate-900 leading-tight"><?= $k['value'] ?></p>
                        <p class="text-[10px] font-medium <?= $k['up']?'text-emerald-600':'text-rose-500' ?> flex items-center gap-1">
                            <i class="fa-solid <?= $k['up']?'fa-arrow-trend-up':'fa-arrow-trend-down' ?> text-[9px]"></i><?= $k['sub'] ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- MAIN SPLIT GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" id="trackMainGrid">

                <!-- LEFT COLUMN (7 cols) -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Shipment Summary Card -->
                    <div class="track-card p-6 fade-in">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-extrabold text-slate-900">Shipment Summary</h3>
                                    <span id="summaryBadge" class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full border"></span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Waybill <span id="summaryWaybill" class="font-mono font-bold text-slate-600"></span> · <span id="summaryType"></span></p>
                                <p id="etaCountdown" class="mt-1.5 inline-flex items-center gap-1.5 text-[11px] font-bold text-brand-blue bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full"><i class="fa-solid fa-clock text-[10px]"></i> Calculating ETA…</p>
                            </div>
                            <select id="waybillSelect" onchange="switchTrackWaybill(this.value)" class="bg-slate-50 border border-slate-200 text-slate-900 font-mono text-xs font-bold px-3 py-2 rounded-xl focus:outline-none focus:border-brand-blue cursor-pointer shrink-0">
                                <?php foreach ($shipments as $s): ?><option value="<?= $s['waybill'] ?>"><?= $s['waybill'] ?> (<?= $s['route'] ?>)</option><?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 py-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Carrier</p>
                                <p id="summaryCarrier" class="text-xs font-bold text-slate-800 mt-0.5"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Route</p>
                                <p id="summaryRoute" class="text-xs font-bold text-slate-800 mt-0.5"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">ETA</p>
                                <p id="summaryEta" class="text-xs font-bold text-slate-800 mt-0.5"></p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <div class="flex justify-between items-center text-[11px] font-semibold text-slate-500 mb-1.5">
                                <span>Journey Progress</span>
                                <span id="progressLabel" class="text-brand-blue font-bold">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div id="progressFill" class="bg-gradient-to-r from-brand-blue to-blue-400 h-full rounded-full transition-all duration-700" style="width:0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Milestone Timeline Card -->
                    <div class="track-card p-6 fade-in">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Milestone Timeline</h3>
                                <p class="text-xs text-slate-400">Live tracking events</p>
                            </div>
                            <span id="routeMapBadge" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-brand-blue">● Vessel In Transit</span>
                        </div>
                        <div id="timelineContainer" class="relative pl-2 space-y-6"></div>
                    </div>

                </div>

                <!-- RIGHT COLUMN (5 cols) -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- Map Card -->
                    <div class="track-card p-6 fade-in space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-location-crosshairs text-brand-blue"></i> GPS Route Map</h3>
                                <p class="text-xs text-slate-400">Live vessel position</p>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-full flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-brand-blue animate-pulse"></span> Live</span>
                        </div>

                        <div class="relative h-[340px] rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-950">
                            <div id="trackingMap" class="w-full h-full z-0"></div>
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-600 flex justify-between items-center gap-3">
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Next Checkpoint</span>
                                <strong id="nextCheckpointText" class="text-slate-900 text-xs font-bold"></strong>
                            </div>
                            <button onclick="alert('Downloading Bill of Lading PDF...')" class="bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm shrink-0">
                                <i class="fa-solid fa-file-pdf text-red-500"></i> e-BOL PDF
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ACTIVE SHIPMENTS RAIL -->
            <div class="track-card p-6 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Active Shipments</h3>
                        <p class="text-xs text-slate-400">Switch tracking view</p>
                    </div>
                    <a href="/shipments" class="text-xs font-semibold text-brand-blue hover:text-brand-darkblue">View all &rarr;</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <?php foreach ($shipments as $s): $b = trackBadge($s['status']); ?>
                    <button onclick="switchTrackWaybill('<?= $s['waybill'] ?>')" data-waybill="<?= $s['waybill'] ?>" class="track-shipment group text-left p-4 rounded-xl bg-slate-50 hover:bg-brand-blue hover:text-white border border-slate-200 hover:border-brand-blue transition-all">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs font-bold truncate"><?= $s['waybill'] ?></span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold px-2 py-0.5 rounded-full border <?= $b['class'] ?> group-hover:bg-white/15 group-hover:text-white group-hover:border-white/30">
                                <span class="w-1.5 h-1.5 rounded-full <?= $b['dot'] ?>"></span><?= $b['label'] ?>
                            </span>
                        </div>
                        <p class="text-[11px] mt-1.5 opacity-70 truncate"><?= $s['route'] ?> · <?= $s['carrier'] ?></p>
                        <div class="mt-2 w-full bg-white/60 group-hover:bg-white/25 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-brand-blue group-hover:bg-white h-full rounded-full" style="width:<?= $s['progress'] ?>%"></div>
                        </div>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            </div>

        </div>
    </main>

    <?php include_once '../../components/chat_widget.php'; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/assets/js/customer/customer_dashboard.js"></script>
    <script>
    /* Single source of truth shared with PHP (mirrors $shipments). */
    window.__disableTrackAutoInit = true;
    var TRACK_DATA = <?= json_encode($shipments, JSON_UNESCAPED_SLASHES) ?>;

    (function () {
      'use strict';
      function byId(id) { return document.getElementById(id); }

      var dotColor = { 'in-transit':'bg-blue-500', 'customs':'bg-amber-500', 'delayed':'bg-rose-500', 'delivered':'bg-emerald-500' };
      var badgeClass = {
        'in-transit': 'bg-blue-50 text-blue-700 border-blue-200',
        'customs':    'bg-amber-50 text-amber-700 border-amber-200',
        'delayed':    'bg-rose-50 text-rose-700 border-rose-200',
        'delivered':  'bg-emerald-50 text-emerald-700 border-emerald-200'
      };
      var badgeLabel = { 'in-transit':'In Transit', 'customs':'Customs', 'delayed':'Delayed', 'delivered':'Delivered' };

      /* ---------- Map state ---------- */
      var map = null, routeLine = null, liveMarker = null, originMk = null, destMk = null;

      function initTrackMap() {
        var el = byId('trackingMap');
        if (!el || typeof L === 'undefined') return;
        map = L.map(el, { scrollWheelZoom: false }).setView([12.5, 122], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap' }).addTo(map);
        setTimeout(function () { map.invalidateSize(); }, 250);
      }

      function renderMap(s) {
        if (!map) return;
        if (routeLine) map.removeLayer(routeLine);
        if (liveMarker) map.removeLayer(liveMarker);
        if (originMk) map.removeLayer(originMk);
        if (destMk) map.removeLayer(destMk);
        var from = s.from, to = s.to, live = s.live;
        routeLine = L.polyline([from, live, to], { color:'#0066ff', weight:3, opacity:0.7, dashArray:'6 8' }).addTo(map);
        originMk = L.circleMarker(from, { radius:6, color:'#64748b', fillColor:'#94a3b8', fillOpacity:1 }).addTo(map).bindPopup(s.origin + ' (Origin)');
        destMk  = L.circleMarker(to,   { radius:6, color:'#059669', fillColor:'#10b981', fillOpacity:1 }).addTo(map).bindPopup(s.destination + ' (Destination)');
        liveMarker = L.circleMarker(live, { radius:8, color:'#fff', weight:3, fillColor:'#0066ff', fillOpacity:1 }).addTo(map).bindPopup('Live vessel · ' + s.waybill).openPopup();
        map.fitBounds(L.latLngBounds([from, to]), { padding:[40, 40] });
      }

      /* ---------- Timeline ---------- */
      function renderTimeline(s) {
        var c = byId('timelineContainer');
        if (!c) return;
        c.innerHTML = '';
        var n = s.milestones.length;
        s.milestones.forEach(function (m, i) {
          var dot = m.done ? (dotColor[s.status] || 'bg-brand-blue') : 'bg-slate-300';
          var line = (i < n - 1) ? '<span class="ms-line ' + (m.done ? 'bg-brand-blue/40' : 'bg-slate-200') + '"></span>' : '';
          var icon = m.done ? 'fa-check' : (i === n - 3 ? 'fa-ship' : 'fa-circle');
          var item = document.createElement('div');
          item.className = 'ms-item flex items-start gap-3';
          item.innerHTML =
            '<div class="ms-dot ' + dot + ' mt-1 flex items-center justify-center"><i class="fa-solid ' + icon + ' text-[7px] text-white"></i></div>' +
            line +
            '<div class="flex-1 min-w-0 pb-1">' +
              '<div class="flex items-center justify-between gap-2">' +
                '<p class="text-xs font-bold ' + (m.done ? 'text-slate-900' : 'text-slate-500') + '">' + m.label + '</p>' +
                '<span class="text-[10px] font-medium ' + (m.done ? 'text-slate-400' : 'text-brand-blue') + '">' + m.time + '</span>' +
              '</div>' +
              '<p class="text-[11px] text-slate-400 mt-0.5">' + m.loc + '</p>' +
            '</div>';
          c.appendChild(item);
        });
      }

      /* ---------- Summary + progress + checkpoint ---------- */
      function renderSummary(s) {
        var b = badgeClass[s.status] || 'bg-slate-50 text-slate-700 border-slate-200';
        var badge = byId('summaryBadge');
        badge.className = 'inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-0.5 rounded-full border ' + b;
        badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full ' + (dotColor[s.status] || 'bg-slate-400') + '"></span>' + (badgeLabel[s.status] || s.status);
        byId('summaryWaybill').textContent = s.waybill;
        byId('summaryType').textContent = s.type;
        byId('summaryCarrier').textContent = s.carrier;
        byId('summaryRoute').textContent = s.route;
        byId('summaryEta').textContent = s.eta.replace(/\s\d{2}:\d{2}:\d{2}/, '');
        var pct = s.progress;
        byId('progressLabel').textContent = pct + '%';
        var fill = byId('progressFill');
        fill.style.width = '0%';
        requestAnimationFrame(function () { fill.style.width = pct + '%'; });
        var badge2 = byId('routeMapBadge');
        if (badge2) badge2.innerHTML = '● ' + (badgeLabel[s.status] || 'In Transit');
        byId('nextCheckpointText').textContent = s.next + ' (' + s.next_date + ')';
      }

      /* ---------- Active-shipment rail highlight ---------- */
      function highlightRail(waybill) {
        document.querySelectorAll('.track-shipment').forEach(function (btn) {
          var sel = btn.getAttribute('data-waybill') === waybill;
          btn.classList.toggle('ring-2', sel);
          btn.classList.toggle('ring-brand-blue', sel);
        });
      }

      /* ---------- Master switch (overrides shared customer_dashboard.js) ---------- */
      window.switchTrackWaybill = function (value) {
        var s = TRACK_DATA.find(function (x) { return x.waybill === value; });
        if (!s) s = TRACK_DATA[0];
        var sel = byId('waybillSelect');
        if (sel) sel.value = s.waybill;
        renderSummary(s);
        renderTimeline(s);
        if (!map) initTrackMap();
        if (map) renderMap(s);
        highlightRail(s.waybill);
      };

      /* ---------- ETA live countdown ---------- */
      var etaTimer = null;
      function startCountdown() {
        if (etaTimer) clearInterval(etaTimer);
        function tick() {
          var sel = byId('waybillSelect'); if (!sel) return;
          var s = TRACK_DATA.find(function (x) { return x.waybill === sel.value; }); if (!s) return;
          var diff = new Date(s.eta).getTime() - Date.now();
          var chip = byId('etaCountdown'); if (!chip) return;
          if (diff <= 0) { chip.textContent = 'Delivered'; return; }
          var d = Math.floor(diff / 864e5);
          var h = Math.floor(diff % 864e5 / 36e5);
          var m = Math.floor(diff % 36e5 / 6e4);
          chip.textContent = 'Arrives in ' + d + 'd ' + h + 'h ' + m + 'm';
        }
        tick();
        etaTimer = setInterval(tick, 60000);
      }

      document.addEventListener('DOMContentLoaded', function () {
        initTrackMap();
        var first = (new URLSearchParams(location.search).get('waybill')) || TRACK_DATA[0].waybill;
        window.switchTrackWaybill(first);
        startCountdown();
      });
    })();
    </script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>
