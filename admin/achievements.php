<?php
/**
 * Achievements Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $db->delete('achievements', 'id = ?', [$id]);
    Session::flash('success', 'Achievement deleted.');
    redirect(SITE_URL . 'admin/achievements');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $data = [
            'title'            => Validator::sanitize($_POST['title'] ?? ''),
            'description'      => Validator::sanitize($_POST['description'] ?? ''),
            'icon'             => Validator::sanitize($_POST['icon'] ?? 'bi-trophy'),
            'achievement_date' => $_POST['achievement_date'] ?: null,
            'sort_order'       => (int)($_POST['sort_order'] ?? 0),
            'is_active'        => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($id > 0) {
            $db->update('achievements', $data, 'id = ?', [$id]);
            Session::flash('success', 'Achievement updated.');
        } else {
            $db->insert('achievements', $data);
            Session::flash('success', 'Achievement added.');
        }
        redirect(SITE_URL . 'admin/achievements');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM achievements WHERE id = ?", [$id]);
}

$achievements = $db->fetchAll("SELECT * FROM achievements ORDER BY sort_order ASC, achievement_date DESC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Achievements Manager</h3>
    <p class="text-secondary">Manage awards, recognitions, and achievements.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Achievement' : 'Add Achievement'; ?></h5>
            <form method="post">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Achievement Title *</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo e($editItem['title'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon Class (Bootstrap Icons)</label>
                    <input type="text" name="icon" class="form-control" placeholder="bi-trophy" value="<?php echo e($editItem['icon'] ?? 'bi-trophy'); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Achievement Date</label>
                    <input type="date" name="achievement_date" class="form-control" value="<?php echo e($editItem['achievement_date'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($editItem['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo (int)($editItem['sort_order'] ?? 0); ?>">
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($editItem) || $editItem['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active / Visible</label>
                </div>

                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Achievement</button>
                <?php if ($editItem): ?>
                    <a href="<?php echo SITE_URL; ?>admin/achievements" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Achievements List</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Achievement</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($achievements as $ach): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><i class="<?php echo e($ach['icon']); ?> text-warning me-2"></i><?php echo e($ach['title']); ?></div>
                                        <div class="small text-muted"><?php echo e(truncate($ach['description'], 60)); ?></div>
                                    </td>
                                    <td><span class="small"><?php echo formatDate($ach['achievement_date']); ?></span></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/achievements?action=edit&id=<?php echo $ach['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/achievements?action=delete&id=<?php echo $ach['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete achievement?"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
