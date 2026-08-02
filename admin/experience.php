<?php
/**
 * Experience Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $db->delete('experience', 'id = ?', [$id]);
    Session::flash('success', 'Experience entry deleted.');
    redirect(SITE_URL . 'admin/experience');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $responsibilities = array_filter(array_map('trim', explode("\n", $_POST['responsibilities'] ?? '')));
        
        $data = [
            'company'          => Validator::sanitize($_POST['company'] ?? ''),
            'position'         => Validator::sanitize($_POST['position'] ?? ''),
            'start_date'       => $_POST['start_date'] ?: date('Y-m-d'),
            'end_date'         => isset($_POST['is_current']) ? null : ($_POST['end_date'] ?: null),
            'is_current'       => isset($_POST['is_current']) ? 1 : 0,
            'description'      => Validator::sanitize($_POST['description'] ?? ''),
            'responsibilities' => json_encode(array_values($responsibilities)),
            'technologies'     => Validator::sanitize($_POST['technologies'] ?? ''),
            'company_url'      => Validator::sanitizeUrl($_POST['company_url'] ?? ''),
            'sort_order'       => (int)($_POST['sort_order'] ?? 0),
            'is_active'        => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($id > 0) {
            $db->update('experience', $data, 'id = ?', [$id]);
            Session::flash('success', 'Experience entry updated.');
        } else {
            $db->insert('experience', $data);
            Session::flash('success', 'Experience entry added.');
        }
        redirect(SITE_URL . 'admin/experience');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM experience WHERE id = ?", [$id]);
}

$experiences = $db->fetchAll("SELECT * FROM experience ORDER BY sort_order ASC, start_date DESC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Experience Manager</h3>
    <p class="text-secondary">Manage work history, positions, and company details.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Entry' : 'Add New Entry'; ?></h5>
            <form method="post">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="company" class="form-control" required value="<?php echo e($editItem['company'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Position / Role *</label>
                    <input type="text" name="position" class="form-control" required value="<?php echo e($editItem['position'] ?? ''); ?>">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Start Date *</label>
                        <input type="date" name="start_date" class="form-control" required value="<?php echo e($editItem['start_date'] ?? ''); ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo e($editItem['end_date'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_current" class="form-check-input" id="is_current" <?php echo (!empty($editItem['is_current'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_current">Currently Working Here</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Company Website URL</label>
                    <input type="url" name="company_url" class="form-control" value="<?php echo e($editItem['company_url'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Overview / Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($editItem['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Responsibilities (One per line)</label>
                    <?php
                        $respText = '';
                        if (!empty($editItem['responsibilities'])) {
                            $arr = json_decode($editItem['responsibilities'], true) ?: [];
                            $respText = implode("\n", $arr);
                        }
                    ?>
                    <textarea name="responsibilities" class="form-control" rows="4"><?php echo e($respText); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Technologies Used (comma-separated)</label>
                    <input type="text" name="technologies" class="form-control" placeholder="PHP, MySQL, Bootstrap" value="<?php echo e($editItem['technologies'] ?? ''); ?>">
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
                    <a href="<?php echo SITE_URL; ?>admin/experience" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Experience Entries</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Role & Company</th>
                                <th>Period</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($experiences as $exp): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($exp['position']); ?></div>
                                        <div class="small text-muted"><?php echo e($exp['company']); ?></div>
                                    </td>
                                    <td>
                                        <span class="small"><?php echo formatDate($exp['start_date'], 'M Y'); ?> - <?php echo $exp['is_current'] ? 'Present' : formatDate($exp['end_date'], 'M Y'); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/experience?action=edit&id=<?php echo $exp['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/experience?action=delete&id=<?php echo $exp['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this experience?"><i class="bi bi-trash"></i></a>
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
