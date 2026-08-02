<?php
/**
 * Education Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $db->delete('education', 'id = ?', [$id]);
    Session::flash('success', 'Education entry deleted.');
    redirect(SITE_URL . 'admin/education');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $data = [
            'institute'      => Validator::sanitize($_POST['institute'] ?? ''),
            'degree'         => Validator::sanitize($_POST['degree'] ?? ''),
            'field_of_study' => Validator::sanitize($_POST['field_of_study'] ?? ''),
            'start_year'     => $_POST['start_year'] ? (int)$_POST['start_year'] : null,
            'passing_year'   => $_POST['passing_year'] ? (int)$_POST['passing_year'] : null,
            'grade'          => Validator::sanitize($_POST['grade'] ?? ''),
            'description'    => Validator::sanitize($_POST['description'] ?? ''),
            'sort_order'     => (int)($_POST['sort_order'] ?? 0),
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($id > 0) {
            $db->update('education', $data, 'id = ?', [$id]);
            Session::flash('success', 'Education entry updated.');
        } else {
            $db->insert('education', $data);
            Session::flash('success', 'Education entry added.');
        }
        redirect(SITE_URL . 'admin/education');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM education WHERE id = ?", [$id]);
}

$educationList = $db->fetchAll("SELECT * FROM education ORDER BY sort_order ASC, passing_year DESC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Education Manager</h3>
    <p class="text-secondary">Manage degrees, institutes, and academic records.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Education' : 'Add Education'; ?></h5>
            <form method="post">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Institute Name *</label>
                    <input type="text" name="institute" class="form-control" required value="<?php echo e($editItem['institute'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Degree *</label>
                    <input type="text" name="degree" class="form-control" required value="<?php echo e($editItem['degree'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Field of Study</label>
                    <input type="text" name="field_of_study" class="form-control" value="<?php echo e($editItem['field_of_study'] ?? ''); ?>">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Year</label>
                        <input type="number" name="start_year" class="form-control" placeholder="2014" value="<?php echo e($editItem['start_year'] ?? ''); ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Passing Year</label>
                        <input type="number" name="passing_year" class="form-control" placeholder="2018" value="<?php echo e($editItem['passing_year'] ?? ''); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Grade / GPA</label>
                    <input type="text" name="grade" class="form-control" placeholder="3.75 / 4.00" value="<?php echo e($editItem['grade'] ?? ''); ?>">
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

                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Entry</button>
                <?php if ($editItem): ?>
                    <a href="<?php echo SITE_URL; ?>admin/education" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Education Records</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Degree & Institute</th>
                                <th>Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($educationList as $edu): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($edu['degree']); ?></div>
                                        <div class="small text-muted"><?php echo e($edu['institute']); ?></div>
                                    </td>
                                    <td><span class="small"><?php echo e($edu['start_year']); ?> - <?php echo e($edu['passing_year']); ?></span></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/education?action=edit&id=<?php echo $edu['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/education?action=delete&id=<?php echo $edu['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this education entry?"><i class="bi bi-trash"></i></a>
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
