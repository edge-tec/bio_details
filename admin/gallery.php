<?php
/**
 * Gallery Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $item = $db->fetch("SELECT image FROM gallery WHERE id = ?", [$id]);
    if ($item && !empty($item['image'])) {
        (new FileUpload())->delete($item['image']);
    }
    $db->delete('gallery', 'id = ?', [$id]);
    Session::flash('success', 'Image deleted.');
    redirect(SITE_URL . 'admin/gallery');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        if (!empty($_FILES['image']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['image'], 'gallery');
            if ($uploaded) {
                $db->insert('gallery', [
                    'image'      => $uploaded,
                    'caption'    => Validator::sanitize($_POST['caption'] ?? ''),
                    'alt_text'   => Validator::sanitize($_POST['alt_text'] ?? ''),
                    'category'   => Validator::sanitize($_POST['category'] ?? 'General'),
                    'sort_order' => (int)($_POST['sort_order'] ?? 0),
                    'is_active'  => 1,
                ]);
                Session::flash('success', 'Image uploaded.');
            } else {
                Session::flash('error', $uploader->getFirstError());
            }
        }
        redirect(SITE_URL . 'admin/gallery');
    }
}

$images = $db->fetchAll("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");
?>

<div class="mb-4">
    <h3 class="fw-bold">Gallery Manager</h3>
    <p class="text-secondary">Upload and manage photo gallery images.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3">Upload New Image</h5>
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <?php echo Session::csrfField(); ?>
                <div class="col-md-4">
                    <label class="form-label">Image File *</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Caption</label>
                    <input type="text" name="caption" class="form-control" placeholder="Photo caption">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="Events, Work, etc." value="General">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload me-1"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="admin-card p-4">
    <h5 class="fw-bold mb-3">Gallery Images</h5>
    <div class="row g-3">
        <?php foreach ($images as $img): ?>
            <div class="col-md-3 col-sm-6">
                <div class="border rounded p-2 text-center position-relative bg-card">
                    <img src="<?php echo uploadUrl($img['image']); ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 6px;">
                    <div class="mt-2 text-truncate small fw-semibold"><?php echo e($img['caption'] ?: 'No caption'); ?></div>
                    <div class="small text-muted mb-2"><?php echo e($img['category']); ?></div>
                    <a href="<?php echo SITE_URL; ?>admin/gallery?action=delete&id=<?php echo $img['id']; ?>" class="btn btn-sm btn-outline-danger w-100" data-confirm="Delete this image?">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
