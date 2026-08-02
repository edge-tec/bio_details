<?php
/**
 * Contact Messages Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $db->delete('contact_messages', 'id = ?', [$id]);
    Session::flash('success', 'Message deleted.');
    redirect(SITE_URL . 'admin/messages');
}

if ($action === 'read' && $id > 0) {
    $db->update('contact_messages', ['is_read' => 1], 'id = ?', [$id]);
}

$viewMessage = null;
if ($id > 0 && ($action === 'view' || $action === 'read')) {
    $db->update('contact_messages', ['is_read' => 1], 'id = ?', [$id]);
    $viewMessage = $db->fetch("SELECT * FROM contact_messages WHERE id = ?", [$id]);
}

$messages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Contact Messages Manager</h3>
    <p class="text-secondary">View and respond to messages submitted via the contact form.</p>
</div>

<?php if ($viewMessage): ?>
<div class="admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-1"><?php echo e($viewMessage['subject'] ?: 'No Subject'); ?></h5>
            <div class="small text-muted">
                From: <strong><?php echo e($viewMessage['name']); ?></strong> (&lt;<?php echo e($viewMessage['email']); ?>&gt;)
                <?php if ($viewMessage['phone']): ?> | Phone: <?php echo e($viewMessage['phone']); ?><?php endif; ?>
            </div>
        </div>
        <div class="text-end">
            <span class="small text-muted d-block"><?php echo formatDate($viewMessage['created_at'], 'M d, Y h:i A'); ?></span>
            <span class="small text-muted">IP: <?php echo e($viewMessage['ip_address']); ?></span>
        </div>
    </div>
    <div class="p-3 bg-tertiary rounded mb-3" style="white-space: pre-wrap; font-size: var(--font-size-base);">
        <?php echo e($viewMessage['message']); ?>
    </div>
    <div class="d-flex gap-2">
        <a href="mailto:<?php echo e($viewMessage['email']); ?>?subject=Re: <?php echo e(urlencode($viewMessage['subject'] ?: 'Inquiry')); ?>" class="btn btn-primary"><i class="bi bi-reply me-1"></i> Reply via Email</a>
        <a href="<?php echo SITE_URL; ?>admin/messages?action=delete&id=<?php echo $viewMessage['id']; ?>" class="btn btn-outline-danger" data-confirm="Delete message?"><i class="bi bi-trash me-1"></i> Delete</a>
        <a href="<?php echo SITE_URL; ?>admin/messages" class="btn btn-outline-secondary">Close View</a>
    </div>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="fw-bold mb-0">Inbox (<?php echo count($messages); ?>)</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Subject / Snippet</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?php echo !$msg['is_read'] ? 'fw-bold table-light' : ''; ?>">
                            <td>
                                <div><?php echo e($msg['name']); ?></div>
                                <div class="small text-muted font-normal"><?php echo e($msg['email']); ?></div>
                            </td>
                            <td>
                                <div><?php echo e(truncate($msg['subject'] ?: $msg['message'], 40)); ?></div>
                                <div class="small text-muted font-normal"><?php echo e(truncate($msg['message'], 60)); ?></div>
                            </td>
                            <td><span class="small font-normal"><?php echo timeAgo($msg['created_at']); ?></span></td>
                            <td>
                                <?php if ($msg['is_read']): ?>
                                    <span class="badge bg-secondary-subtle text-secondary font-normal">Read</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning font-normal">Unread</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>admin/messages?action=view&id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-eye"></i> View</a>
                                <a href="<?php echo SITE_URL; ?>admin/messages?action=delete&id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete message?"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
