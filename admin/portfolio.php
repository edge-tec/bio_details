<?php
/**
 * Portfolio & Project Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $item = $db->fetch("SELECT image FROM portfolio WHERE id = ?", [$id]);
    if ($item && !empty($item['image'])) {
        (new FileUpload())->delete($item['image']);
    }
    $db->delete('portfolio', 'id = ?', [$id]);
    Session::flash('success', 'Project deleted.');
    redirect(SITE_URL . 'admin/portfolio');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $title = Validator::sanitize($_POST['title'] ?? '');
        $slug = Validator::slugify($_POST['slug'] ?: $title);
        $slug = (new SEO())->uniqueSlug($slug, 'portfolio', $id ?: null);

        $data = [
            'title'             => $title,
            'slug'              => $slug,
            'short_description' => Validator::sanitize($_POST['short_description'] ?? ''),
            'description'       => Validator::sanitize($_POST['description'] ?? ''),
            'technologies'      => Validator::sanitize($_POST['technologies'] ?? ''),
            'category'          => Validator::sanitize($_POST['category'] ?? 'Web Development'),
            'live_url'          => Validator::sanitizeUrl($_POST['live_url'] ?? ''),
            'github_url'        => Validator::sanitizeUrl($_POST['github_url'] ?? ''),
            'is_featured'       => isset($_POST['is_featured']) ? 1 : 0,
            'sort_order'        => (int)($_POST['sort_order'] ?? 0),
            'is_active'         => isset($_POST['is_active']) ? 1 : 0,
        ];

        // Handle Image Upload
        if (!empty($_FILES['image']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['image'], 'portfolio');
            if ($uploaded) {
                if ($id > 0) {
                    $old = $db->fetch("SELECT image FROM portfolio WHERE id = ?", [$id]);
                    if ($old && !empty($old['image'])) {
                        $uploader->delete($old['image']);
                    }
                }
                $data['image'] = $uploaded;
            } else {
                Session::flash('error', $uploader->getFirstError());
            }
        }

        if ($id > 0) {
            $db->update('portfolio', $data, 'id = ?', [$id]);
            Session::flash('success', 'Project updated.');
        } else {
            $db->insert('portfolio', $data);
            Session::flash('success', 'Project added.');
        }
        redirect(SITE_URL . 'admin/portfolio');
    }
}

$editItem = null;
if ($id > 0 && $action === 'edit') {
    $editItem = $db->fetch("SELECT * FROM portfolio WHERE id = ?", [$id]);
}

$projects = $db->fetchAll("SELECT * FROM portfolio ORDER BY sort_order ASC, created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Portfolio & Projects Manager</h3>
        <p class="text-secondary mb-0">Manage projects, tech stack, live demos, and GitHub links.</p>
    </div>
    <?php if ($action !== 'new' && !$editItem): ?>
        <a href="<?php echo SITE_URL; ?>admin/portfolio?action=new" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Project</a>
    <?php endif; ?>
</div>

<?php if ($action === 'new' || $editItem): ?>
<div class="admin-card p-4 mb-4">
    <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Edit Project' : 'New Project'; ?></h5>
    <form method="post" enctype="multipart/form-data">
        <?php echo Session::csrfField(); ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Project Title *</label>
                <input type="text" name="title" class="form-control" data-slug-target required value="<?php echo e($editItem['title'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">URL Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?php echo e($editItem['slug'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" placeholder="Web Development" value="<?php echo e($editItem['category'] ?? 'Web Development'); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Live Demo URL</label>
                <input type="url" name="live_url" class="form-control" value="<?php echo e($editItem['live_url'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">GitHub Repository URL</label>
                <input type="url" name="github_url" class="form-control" value="<?php echo e($editItem['github_url'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Technologies (comma-separated)</label>
                <input type="text" name="technologies" class="form-control" placeholder="PHP, MySQL, Bootstrap" value="<?php echo e($editItem['technologies'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Project Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if (!empty($editItem['image'])): ?>
                    <div class="mt-2"><img src="<?php echo uploadUrl($editItem['image']); ?>" style="height: 50px; border-radius: 6px;"></div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <label class="form-label">Short Description</label>
                <textarea name="short_description" class="form-control" rows="2"><?php echo e($editItem['short_description'] ?? ''); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Full Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo e($editItem['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="<?php echo (int)($editItem['sort_order'] ?? 0); ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check me-4 mb-2">
                    <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" <?php echo (!empty($editItem['is_featured'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_featured">Featured on Homepage</label>
                </div>
                <div class="form-check mb-2">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (!isset($editItem) || $editItem['is_active']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Project</button>
                <a href="<?php echo SITE_URL; ?>admin/portfolio" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="fw-bold mb-0">Projects List</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Technologies</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($p['image'])): ?>
                                        <img src="<?php echo uploadUrl($p['image']); ?>" style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 35px; background: var(--bg-tertiary); border-radius: 4px;" class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?php echo e($p['title']); ?></div>
                                        <div class="small text-muted"><?php echo e($p['slug']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e($p['category']); ?></span></td>
                            <td><span class="small"><?php echo e(truncate($p['technologies'], 30)); ?></span></td>
                            <td>
                                <?php if ($p['is_featured']): ?>
                                    <span class="badge bg-warning-subtle text-warning">Featured</span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>admin/portfolio?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                <a href="<?php echo SITE_URL; ?>admin/portfolio?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this project?"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
