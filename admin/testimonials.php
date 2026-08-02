<?php
/**
 * Testimonials Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $item = $db->fetch("SELECT photo FROM testimonials WHERE id = ?", [$id]);
    if ($item && !empty($item['photo'])) {
        (new FileUpload())->delete($item['photo']);
    }
    $db->delete('testimonials', 'id = ?', [$id]);
    Session::flash('success', 'Testimonial deleted.');
    redirect(SITE_URL . 'admin/testimonials');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $data = [
            'name'       => Validator::sanitize($_POST['name'] ?? ''),
            'review'     => Validator::sanitize($_POST['review'] ?? ''),
            'rating'     => min(5, max(1, (int)($_POST['rating'] ?? 5))),
            'company'    => Validator::sanitize($_POST['company'] ?? ''),
            'position'   => Validator::sanitize($_POST['position'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];

        // Handle photo upload
        if (!empty($_FILES['photo']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['photo'], 'testimonials');
            if ($uploaded) {
                if ($id > 0) {
                    $old = $db->fetch("SELECT photo FROM testimonials WHERE id = ?", [$id]);
                    if ($old && !empty($old['photo'])) {
                        $uploader->delete($old['photo']);
                    }
                }
                $data['photo'] = $uploaded;
            }
        }

        if ($id > 0) {
            $db->update('testimonials', $data, 'id = ?', [$id]);
            Session::flash('success', 'Testimonial updated.');
        } else {
            $db->insert('testimonials', $data);
            Session::flash('success', 'Testimonial added.');
        }
        redirect(SITE_URL . 'admin/testimonials');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM testimonials WHERE id = ?", [$id]);
}

$testimonials = $db->fetchAll("SELECT * FROM testimonials ORDER BY sort_order ASC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Testimonials Manager</h3>
    <p class="text-secondary">Manage client reviews, ratings, and client photos.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Testimonial' : 'Add Testimonial'; ?></h5>
            <form method="post" enctype="multipart/form-data">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Client Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo e($editItem['name'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control" value="<?php echo e($editItem['company'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Position / Role</label>
                    <input type="text" name="position" class="form-control" value="<?php echo e($editItem['position'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating (1 to 5 Stars)</label>
                    <select name="rating" class="form-select">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($editItem['rating']) && $editItem['rating'] == $i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?> Stars
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Client Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <?php if (!empty($editItem['photo'])): ?>
                        <div class="mt-2"><img src="<?php echo uploadUrl($editItem['photo']); ?>" style="height: 45px; border-radius: 50%;"></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Review Text *</label>
                    <textarea name="review" class="form-control" rows="4" required><?php echo e($editItem['review'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo (int)($editItem['sort_order'] ?? 0); ?>">
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($editItem) || $editItem['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active / Visible</label>
                </div>

                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Testimonial</button>
                <?php if ($editItem): ?>
                    <a href="<?php echo SITE_URL; ?>admin/testimonials" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Client Reviews</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testimonials as $t): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($t['name']); ?></div>
                                        <div class="small text-muted"><?php echo e($t['position']); ?>, <?php echo e($t['company']); ?></div>
                                    </td>
                                    <td><span class="text-warning"><?php echo str_repeat('★', (int)$t['rating']); ?></span></td>
                                    <td>
                                        <?php if ($t['is_active']): ?>
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/testimonials?action=edit&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/testimonials?action=delete&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete testimonial?"><i class="bi bi-trash"></i></a>
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
