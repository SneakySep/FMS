<?php
$page_title = "Notification · Rising Red Dragon";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';

// --- Fetch live notifications from the backend API, with demo fallback ---
$notif_res  = make_api_request('/api/v1/portal/notifications', 'GET');
$notif_data = $notif_res['data']['data'] ?? $notif_res['data'] ?? null;

if (!empty($notif_data) && is_array($notif_data)) {
    $notifications = $notif_data;
} else {
    // Demo fallback when API is unreachable
    $notifications = [
        ['id' => 1, 'type' => 'urgent',   'title' => 'SLA Breach - WB12345',       'message' => 'Delivery Exceeded SLA window by 3h 40m. Escalated to Ops.',     'time' => '2h ago',  'action' => 'View Details', 'link' => '/sla-monitoring'],
        ['id' => 2, 'type' => 'warning',  'title' => 'Document Pending - WB208812', 'message' => 'Commercial Invoice awaiting your review and approval.',          'time' => '1d ago',  'action' => 'Review Doc',   'link' => '/documents'],
        ['id' => 3, 'type' => 'success',  'title' => 'POD Confirmed - WB208835',   'message' => 'Proof of Delivery uploaded for Cebu-Manila shipment.',           'time' => '2d ago',  'action' => 'View POD',     'link' => '/documents'],
        ['id' => 4, 'type' => 'info',     'title' => 'Inquiry Resolved - INQ-1245','message' => 'Billing clarification closed by your account manager.',          'time' => '3d ago',  'action' => 'View',         'link' => '/tickets'],
        ['id' => 5, 'type' => 'warning',  'title' => 'Shipment Delay - WB-1245',   'message' => 'New ETA +2h due to traffic advisory on route.',                  'time' => '3d ago',  'action' => 'Track',        'link' => '/tracking'],
    ];
}

// Count alert types for the badge
$alert_count = count($notifications);
?>

<div class="app-container">

  <!-- SIDEBAR INCLUDE -->
  <?php include_once '../../includes/sidebar.php'; ?>

  <!-- MAIN CONTENT – NOTIFICATION DASHBOARD -->
  <main class="main-content mesh-bg relative overflow-y-auto">

    <!-- Mobile toggle button -->
    <button onclick="toggleSidebar()" class="mobile-toggle fixed top-4 left-4 z-30 p-2 rounded-lg bg-slate-800/80 backdrop-blur border border-slate-700 text-slate-300 hover:text-white transition" aria-label="Open sidebar">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>

    <div class="max-w-5xl mx-auto fade-in">

      <!-- PAGE HEADER -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-white tracking-tight">Notification</h1>
          <p class="text-sm text-slate-400 mt-0.5">Notification - Alerts for your accounts</p>
        </div>
        <!-- Session time + notification count -->
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2 text-sm">
            <span class="text-slate-400">🔔</span>
            <span class="text-slate-300"><?= $alert_count ?> alerts</span>
          </div>
          <div class="text-right">
            <p class="text-xs text-slate-500">Session Active</p>
            <p class="text-sm font-mono text-sky-400 font-semibold" id="sessionTime">5:38:45 PM</p>
          </div>
        </div>
      </div>

      <!-- NOTIFICATION LIST -->
      <div class="glass-card rounded-2xl p-5">

        <!-- Header with actions -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-700/50">
          <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Recent Alerts</h2>
          <button onclick="markAllRead()" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium">
            Mark all as read
          </button>
        </div>

        <!-- Notification items -->
        <div class="space-y-4">

          <?php
          $notif_styles = [
              'urgent'   => ['dot' => 'bg-rose-500',  'badge' => 'text-rose-400',  'label' => 'Urgent',     'panel_class' => 'notif-urgent'],
              'warning'  => ['dot' => 'bg-amber-500', 'badge' => 'text-amber-400', 'label' => 'Warning',    'panel_class' => 'notif-warning'],
              'success'  => ['dot' => 'bg-emerald-500','badge' => 'text-emerald-400','label' => 'Confirmed', 'panel_class' => 'notif-success'],
              'info'     => ['dot' => 'bg-sky-500',   'badge' => 'text-sky-400',   'label' => 'Resolved',   'panel_class' => 'notif-info'],
          ];
          foreach ($notifications as $notif):
              $type   = $notif['type'] ?? 'info';
              $style  = $notif_styles[$type] ?? $notif_styles['info'];
              $title  = htmlspecialchars($notif['title'] ?? 'Notification');
              $msg    = htmlspecialchars($notif['message'] ?? '');
              $time   = htmlspecialchars($notif['time'] ?? '');
              $action = htmlspecialchars($notif['action'] ?? 'View');
              $link   = $notif['link'] ?? '#';
          ?>
          <div class="<?= $style['panel_class'] ?> glass-panel rounded-xl p-4 hover:bg-white/5 transition cursor-pointer" onclick="viewNotification('<?= $title ?>')">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 rounded-full <?= $style['dot'] ?> mt-2 flex-shrink-0"></div>
              <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <h3 class="text-sm font-semibold text-white"><?= $title ?></h3>
                  <span class="text-xs <?= $style['badge'] ?> font-medium"><?= $style['label'] ?></span>
                </div>
                <p class="text-sm text-slate-300 mt-0.5">
                  <?= $msg ?>
                </p>
                <div class="flex items-center gap-4 mt-2">
                  <span class="text-xs text-slate-500">⏱️ <?= $time ?></span>
                  <a href="<?= $link ?>" class="text-xs text-sky-400 hover:text-sky-300 transition font-medium"><?= $action ?> →</a>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

      </div>
      </div>

      <!-- FOOTER COPYRIGHT -->
      <p class="text-center text-[10px] text-slate-500 mt-8 pt-4 border-t border-white/5">© 2026 CargoNet Systems. Global Logistics Solutions.</p>

    </div>
  </main>

</div>

<!-- PAGE SPECIFIC SCRIPT -->
<script>
  function viewNotification(title) {
    alert(`📬 Notification: ${title}\n\nIn production, this would open the full notification details.`);
  }

  function markAllRead() {
    if (confirm('Mark all notifications as read?')) {
      alert('✅ All notifications marked as read.');
    }
  }

  function updateSessionTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const el = document.getElementById('sessionTime');
    if (el) el.textContent = timeStr;
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    updateSessionTime();
    setInterval(updateSessionTime, 1000);
  });
</script>

<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>