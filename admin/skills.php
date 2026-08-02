<?php
/**
 * Skills Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Delete Skill
if ($action === 'delete' && $id > 0) {
    $db->delete('skills', 'id = ?', [$id]);
    Session::flash('success', 'Skill deleted successfully.');
    redirect(SITE_URL . 'admin/skills');
}

// Add or Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $data = [
            'name'       => Validator::sanitize($_POST['name'] ?? ''),
            'percentage' => min(100, max(0, (int)($_POST['percentage'] ?? 0))),
            'category'   => Validator::sanitize($_POST['category'] ?? 'General'),
            'icon'       => Validator::sanitize($_POST['icon'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($id > 0) {
            $db->update('skills', $data, 'id = ?', [$id]);
            Session::flash('success', 'Skill updated successfully.');
        } else {
            $db->insert('skills', $data);
            Session::flash('success', 'Skill added successfully.');
        }
        redirect(SITE_URL . 'admin/skills');
    }
}

$editSkill = null;
if ($id > 0 && $action === 'edit') {
    $editSkill = $db->fetch("SELECT * FROM skills WHERE id = ?", [$id]);
}

$skills = $db->fetchAll("SELECT * FROM skills ORDER BY category ASC, sort_order ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Skills Manager</h3>
        <p class="text-secondary mb-0">Manage technical skills and proficiency percentages.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Form Side -->
    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editSkill ? 'Edit Skill' : 'Add New Skill'; ?></h5>
            <form method="post">
                <?php echo Session::csrfField(); ?>
                
                <div class="mb-3">
                    <label class="form-label">Skill Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo e($editSkill['name'] ?? ''); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Proficiency Percentage (0-100) *</label>
                    <input type="number" name="percentage" class="form-control" min="0" max="100" required value="<?php echo (int)($editSkill['percentage'] ?? 80); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="Programming, DevOps, etc." value="<?php echo e($editSkill['category'] ?? 'Programming'); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Bootstrap Icon Class</label>
                    <input type="text" name="icon" class="form-control" placeholder="bi-filetype-php" value="<?php echo e($editSkill['icon'] ?? 'bi-gear'); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo (int)($editSkill['sort_order'] ?? 0); ?>">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($editSkill) || $editSkill['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active / Visible</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i> <?php echo $editSkill ? 'Update Skill' : 'Save Skill'; ?>
                </button>
                <?php if ($editSkill): ?>
                    <a href="<?php echo SITE_URL; ?>admin/skills" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Table Side -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">All Skills</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($skills as $s): ?>
                                <tr>
                                    <td>
                                        <i class="<?php echo e($s['icon']); ?> me-2 text-primary"></i>
                                        <strong><?php echo e($s['name']); ?></strong>
                                    </td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e($s['category']); ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: <?php echo (int)$s['percentage']; ?>%"></div>
                                            </div>
                                            <span class="small fw-bold"><?php echo (int)$s['percentage']; ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($s['is_active']): ?>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/skills?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/skills?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this skill?"><i class="bi bi-trash"></i></a>
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
