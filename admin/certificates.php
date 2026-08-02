<?php
/**
 * Certificates Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $item = $db->fetch("SELECT image FROM certificates WHERE id = ?", [$id]);
    if ($item && !empty($item['image'])) {
        (new FileUpload())->delete($item['image']);
    }
    $db->delete('certificates', 'id = ?', [$id]);
    Session::flash('success', 'Certificate deleted.');
    redirect(SITE_URL . 'admin/certificates');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $data = [
            'title'          => Validator::sanitize($_POST['title'] ?? ''),
            'issuer'         => Validator::sanitize($_POST['issuer'] ?? ''),
            'issue_date'     => $_POST['issue_date'] ?: null,
            'expiry_date'    => $_POST['expiry_date'] ?: null,
            'credential_id'  => Validator::sanitize($_POST['credential_id'] ?? ''),
            'credential_url' => Validator::sanitizeUrl($_POST['credential_url'] ?? ''),
            'description'    => Validator::sanitize($_POST['description'] ?? ''),
            'sort_order'     => (int)($_POST['sort_order'] ?? 0),
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['image']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['image'], 'certificates');
            if ($uploaded) {
                if ($id > 0) {
                    $old = $db->fetch("SELECT image FROM certificates WHERE id = ?", [$id]);
                    if ($old && !empty($old['image'])) {
                        $uploader->delete($old['image']);
                    }
                }
                $data['image'] = $uploaded;
            }
        }

        if ($id > 0) {
            $db->update('certificates', $data, 'id = ?', [$id]);
            Session::flash('success', 'Certificate updated.');
        } else {
            $db->insert('certificates', $data);
            Session::flash('success', 'Certificate added.');
        }
        redirect(SITE_URL . 'admin/certificates');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM certificates WHERE id = ?", [$id]);
}

$certificates = $db->fetchAll("SELECT * FROM certificates ORDER BY sort_order ASC, issue_date DESC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Certificates Manager</h3>
    <p class="text-secondary">Manage professional certifications, issuers, and credential links.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Certificate' : 'Add Certificate'; ?></h5>
            <form method="post" enctype="multipart/form-data">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Certificate Title *</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo e($editItem['title'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Issuer Organization *</label>
                    <input type="text" name="issuer" class="form-control" required placeholder="e.g. Zend, Google" value="<?php echo e($editItem['issuer'] ?? ''); ?>">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" value="<?php echo e($editItem['issue_date'] ?? ''); ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?php echo e($editItem['expiry_date'] ?? ''); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Credential ID</label>
                    <input type="text" name="credential_id" class="form-control" value="<?php echo e($editItem['credential_id'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Credential Verification URL</label>
                    <input type="url" name="credential_url" class="form-control" value="<?php echo e($editItem['credential_url'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Certificate Image / Badge</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if (!empty($editItem['image'])): ?>
                        <div class="mt-2"><img src="<?php echo uploadUrl($editItem['image']); ?>" style="height: 50px; border-radius: 6px;"></div>
                    <?php endif; ?>
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

                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Certificate</button>
                <?php if ($editItem): ?>
                    <a href="<?php echo SITE_URL; ?>admin/certificates" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Certificates List</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Certificate</th>
                                <th>Issuer</th>
                                <th>Issue Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificates as $c): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($c['title']); ?></div>
                                        <div class="small text-muted"><?php echo e($c['credential_id']); ?></div>
                                    </td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e($c['issuer']); ?></span></td>
                                    <td><span class="small"><?php echo formatDate($c['issue_date']); ?></span></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/certificates?action=edit&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/certificates?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete certificate?"><i class="bi bi-trash"></i></a>
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
