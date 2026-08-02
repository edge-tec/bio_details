<?php
/**
 * Activity & Error Logs Viewer
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    if (Session::validateCsrfToken($_GET['_token'] ?? '')) {
        $db->query("DELETE FROM activity_logs");
        Session::flash('success', 'Activity logs cleared.');
        redirect(SITE_URL . 'admin/logs');
    }
}

$logs = $db->fetchAll("SELECT l.*, u.name as user_name FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 100");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Activity Logs Viewer</h3>
        <p class="text-secondary mb-0">Audit security events, logins, and system operations.</p>
    </div>
    <?php if (!empty($logs)): ?>
        <a href="<?php echo SITE_URL; ?>admin/logs?action=clear&_token=<?php echo Session::generateCsrfToken(); ?>" class="btn btn-outline-danger" data-confirm="Clear all activity logs?">
            <i class="bi bi-trash me-1"></i> Clear Logs
        </a>
    <?php endif; ?>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="fw-bold mb-0">Recent Activity Logs (Last 100)</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><span class="small"><?php echo formatDate($log['created_at'], 'Y-m-d H:i:s'); ?></span></td>
                            <td><span class="fw-semibold small"><?php echo e($log['user_name'] ?: 'System'); ?></span></td>
                            <td><span class="badge bg-primary-subtle text-primary"><?php echo e($log['action']); ?></span></td>
                            <td><span class="small text-secondary"><?php echo e($log['details']); ?></span></td>
                            <td><span class="small font-monospace"><?php echo e($log['ip_address']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No activity logs recorded.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
