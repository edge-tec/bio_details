<?php
/**
 * Services Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $db->delete('services', 'id = ?', [$id]);
    Session::flash('success', 'Service deleted.');
    redirect(SITE_URL . 'admin/services');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $features = array_filter(array_map('trim', explode("\n", $_POST['features'] ?? '')));
        
        $data = [
            'title'       => Validator::sanitize($_POST['title'] ?? ''),
            'description' => Validator::sanitize($_POST['description'] ?? ''),
            'icon'        => Validator::sanitize($_POST['icon'] ?? 'bi-gear'),
            'features'    => json_encode(array_values($features)),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($id > 0) {
            $db->update('services', $data, 'id = ?', [$id]);
            Session::flash('success', 'Service updated.');
        } else {
            $db->insert('services', $data);
            Session::flash('success', 'Service added.');
        }
        redirect(SITE_URL . 'admin/services');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM services WHERE id = ?", [$id]);
}

$services = $db->fetchAll("SELECT * FROM services ORDER BY sort_order ASC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Services Manager</h3>
    <p class="text-secondary">Manage services offered, icons, and features.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Service' : 'Add Service'; ?></h5>
            <form method="post">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Service Title *</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo e($editItem['title'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon Class (Bootstrap Icons)</label>
                    <input type="text" name="icon" class="form-control" placeholder="bi-code-slash" value="<?php echo e($editItem['icon'] ?? 'bi-gear'); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($editItem['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Key Features (One per line)</label>
                    <?php
                        $featText = '';
                        if (!empty($editItem['features'])) {
                            $arr = json_decode($editItem['features'], true) ?: [];
                            $featText = implode("\n", $arr);
                        }
                    ?>
                    <textarea name="features" class="form-control" rows="4"><?php echo e($featText); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo (int)($editItem['sort_order'] ?? 0); ?>">
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($editItem) || $editItem['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active / Visible</label>
                </div>

                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Service</button>
                <?php if ($editItem): ?>
                    <a href="<?php echo SITE_URL; ?>admin/services" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Services Offered</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $srv): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><i class="<?php echo e($srv['icon']); ?> text-primary me-2"></i><?php echo e($srv['title']); ?></div>
                                        <div class="small text-muted"><?php echo e(truncate($srv['description'], 50)); ?></div>
                                    </td>
                                    <td>
                                        <?php if ($srv['is_active']): ?>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/services?action=edit&id=<?php echo $srv['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/services?action=delete&id=<?php echo $srv['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this service?"><i class="bi bi-trash"></i></a>
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
