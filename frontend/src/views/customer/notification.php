<?php
$page_title = "Notification · Priority Handling Logistics";

include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../helpers/api_helper.php';
include_once __DIR__ . '/../../includes/sidebar.php';

// --- Fetch live notifications from the backend API, with demo fallback ---
$notif_res  = make_api_request('/api/v1/portal/notifications', 'GET');
$notif_data = $notif_res['data']['data'] ?? $notif_res['data'] ?? null;

if (!empty($notif_data) && is_array($notif_data)) {
    $notifications = $notif_data;
} else {
    // Demo fallback when API is unreachable
    $notifications = [
        ['id' => 1, 'type' => 'urgent',   'title' => 'SLA Breach - WB12345',       'message' => 'Delivery Exceeded SLA window by 3h 40m. Escalated to Ops.',     'time' => '2h ago',  'action' => 'View Details', 'link' => 'sla-monitoring.php'],
        ['id' => 2, 'type' => 'warning',  'title' => 'Document Pending - WB208812', 'message' => 'Commercial Invoice awaiting your review and approval.',          'time' => '1d ago',  'action' => 'Review Doc',   'link' => 'documents.php'],
        ['id' => 3, 'type' => 'success',  'title' => 'POD Confirmed - WB208835',   'message' => 'Proof of Delivery uploaded for Cebu-Manila shipment.',           'time' => '2d ago',  'action' => 'View POD',     'link' => 'documents.php'],
        ['id' => 4, 'type' => 'info',     'title' => 'Inquiry Resolved - INQ-1245','message' => 'Billing clarification closed by your account manager.',          'time' => '3d ago',  'action' => 'View',         'link' => 'tickets.php'],
        ['id' => 5, 'type' => 'warning',  'title' => 'Shipment Delay - WB-1245',   'message' => 'New ETA +2h due to traffic advisory on route.',                  'time' => '3d ago',  'action' => 'Track',        'link' => 'tracking.php'],
    ];
}

// Count alert types for the badge
$alert_count = count($notifications);

// Presentation map per notification type: dot colour, badge class and label.
$notif_styles = [
    'urgent'  => ['dot' => 'bg-rose-500',   'badge' => 'crm-badge-red',   'label' => 'Urgent'],
    'warning' => ['dot' => 'bg-amber-500',  'badge' => 'crm-badge-amber', 'label' => 'Warning'],
    'success' => ['dot' => 'bg-emerald-500','badge' => 'crm-badge-green', 'label' => 'Confirmed'],
    'info'    => ['dot' => 'bg-sky-500',    'badge' => 'crm-badge-blue',  'label' => 'Resolved'],
];
?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 flex flex-col min-w-0">

    <?php
    // Shared top bar. The bell is intentionally omitted: this page IS the
    // notification centre, and there is no search target to wire up.
    $pageTitle    = 'Notifications';
    $pageSubtitle = 'Alerts for your accounts · ' . $alert_count . ' total';
    $headerSearch = false;
    include_once __DIR__ . '/../../components/customer_header.php';
    ?>

    <!-- NOTIFICATION CONTENT BODY -->
    <div class="p-6 lg:p-8 2xl:px-10 space-y-6 w-full">

        <section class="crm-card crm-fade-up">
            <div class="crm-panel-head">
                <div>
                    <h2 class="crm-panel-title">Recent Alerts</h2>
                    <span class="crm-panel-sub">Newest first · click a row to open the full details</span>
                </div>
                <button type="button" onclick="markAllRead()" class="crm-btn crm-btn-subtle !h-8 !px-3 !text-xs">
                    <i class="fa-solid fa-check-double text-xs"></i>
                    Mark all as read
                </button>
            </div>

            <?php if (!empty($notifications)): ?>
                <div class="divide-y" style="border-color: var(--line);">
                    <?php foreach ($notifications as $notif):
                        $type   = $notif['type'] ?? 'info';
                        $style  = $notif_styles[$type] ?? $notif_styles['info'];
                        $title  = htmlspecialchars($notif['title'] ?? 'Notification', ENT_QUOTES, 'UTF-8');
                        $msg    = htmlspecialchars($notif['message'] ?? '', ENT_QUOTES, 'UTF-8');
                        $time   = htmlspecialchars($notif['time'] ?? '', ENT_QUOTES, 'UTF-8');
                        $action = htmlspecialchars($notif['action'] ?? 'View', ENT_QUOTES, 'UTF-8');
                        $link   = htmlspecialchars($notif['link'] ?? '#', ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="flex items-start gap-3.5 px-5 py-4 transition-colors hover:bg-navy-50 dark:hover:bg-navy-850 cursor-pointer" data-title="<?= $title ?>" onclick="viewNotification(this)">
                            <span class="w-2 h-2 rounded-full <?= $style['dot'] ?> mt-2 shrink-0"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h3 class="text-sm font-bold truncate" style="color: var(--fg-heading);"><?= $title ?></h3>
                                    <span class="crm-badge <?= $style['badge'] ?> shrink-0"><?= $style['label'] ?></span>
                                </div>
                                <p class="text-xs leading-relaxed mt-1" style="color: var(--fg-body);"><?= $msg ?></p>
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs flex items-center gap-1.5" style="color: var(--fg-muted);">
                                        <i class="fa-regular fa-clock text-[10px]"></i> <?= $time ?>
                                    </span>
                                    <a href="<?= $link ?>" onclick="event.stopPropagation()" class="text-xs font-semibold text-brand-blue hover:underline">
                                        <?= $action ?> &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="crm-empty">
                    <span class="crm-empty-ico"><i class="fa-regular fa-bell-slash text-lg"></i></span>
                    <p class="crm-empty-title">No notifications yet</p>
                    <p class="crm-empty-sub">Shipment, SLA and billing alerts will appear here as they happen.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- FOOTER COPYRIGHT -->
        <p class="text-center text-xs pt-4" style="color: var(--fg-muted); border-top: 1px solid var(--line);">&copy; 2026 CargoNet Systems. Global Logistics Solutions.</p>

    </div>
</main>

<!-- PAGE SPECIFIC SCRIPT -->
<script>
  function viewNotification(row) {
    var title = row && row.dataset ? row.dataset.title : 'Notification';
    alert(`📬 Notification: ${title}\n\nIn production, this would open the full notification details.`);
  }

  function markAllRead() {
    if (confirm('Mark all notifications as read?')) {
      alert('✅ All notifications marked as read.');
    }
  }
</script>

<!-- FOOTER INCLUDE -->
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
