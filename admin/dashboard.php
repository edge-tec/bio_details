<?php
/**
 * Admin Dashboard Page
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

// Gather stats overview
$totalProjects = $db->count('portfolio');
$totalPosts = $db->count('blog');
$totalMessages = $db->count('contact_messages');
$unreadMessages = $db->count('contact_messages', 'is_read = 0');
$totalSubscribers = $db->count('newsletter_subscribers');
$recentMessages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recentLogs = $db->fetchAll("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Dashboard</h3>
        <p class="text-secondary mb-0">Overview of your biography website metrics and activities.</p>
    </div>
    <a href="<?php echo SITE_URL; ?>admin/blog?action=new" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Blog Post
    </a>
</div>

<!-- Quick Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold">TOTAL PROJECTS</span>
                <h2 class="fw-bold mb-0 mt-1"><?php echo $totalProjects; ?></h2>
            </div>
            <div class="stat-card-icon bg-primary-subtle text-primary">
                <i class="bi bi-collection"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold">BLOG POSTS</span>
                <h2 class="fw-bold mb-0 mt-1"><?php echo $totalPosts; ?></h2>
            </div>
            <div class="stat-card-icon bg-info-subtle text-info">
                <i class="bi bi-journal-text"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold">UNREAD MESSAGES</span>
                <h2 class="fw-bold mb-0 mt-1"><?php echo $unreadMessages; ?></h2>
            </div>
            <div class="stat-card-icon bg-warning-subtle text-warning">
                <i class="bi bi-envelope-exclamation"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold">SUBSCRIBERS</span>
                <h2 class="fw-bold mb-0 mt-1"><?php echo $totalSubscribers; ?></h2>
            </div>
            <div class="stat-card-icon bg-success-subtle text-success">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Messages -->
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0"><i class="bi bi-envelope me-2"></i>Recent Messages</h5>
                <a href="<?php echo SITE_URL; ?>admin/messages" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="admin-card-body p-0">
                <?php if (!empty($recentMessages)): ?>
                    <div class="table-responsive">
                        <table class="table admin-table align-middle">
                            <thead>
                                <tr>
                                    <th>Sender</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMessages as $msg): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?php echo e($msg['name']); ?></div>
                                            <div class="small text-muted"><?php echo e($msg['email']); ?></div>
                                        </td>
                                        <td><?php echo e(truncate($msg['subject'] ?: $msg['message'], 30)); ?></td>
                                        <td class="small text-muted"><?php echo timeAgo($msg['created_at']); ?></td>
                                        <td>
                                            <?php if ($msg['is_read']): ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Read</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning">Unread</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">No messages received yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activity Log preview -->
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2"></i>Recent Activity</h5>
                <a href="<?php echo SITE_URL; ?>admin/logs" class="btn btn-sm btn-outline-primary">View Logs</a>
            </div>
            <div class="admin-card-body p-3">
                <?php if (!empty($recentLogs)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentLogs as $log): ?>
                            <li class="list-group-item bg-transparent px-0 py-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold small"><?php echo e(ucfirst(str_replace('_', ' ', $log['action']))); ?></span>
                                    <span class="text-muted x-small"><?php echo timeAgo($log['created_at']); ?></span>
                                </div>
                                <div class="text-secondary small"><?php echo e($log['details']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center text-muted py-3">No activity logged.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
