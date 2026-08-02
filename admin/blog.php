<?php
/**
 * Blog Posts Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Categories CRUD
if (isset($_POST['category_action'])) {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $catName = Validator::sanitize($_POST['category_name'] ?? '');
        if ($catName) {
            $catSlug = (new SEO())->uniqueSlug(slugify($catName), 'blog_categories');
            $db->insert('blog_categories', ['name' => $catName, 'slug' => $catSlug]);
            Session::flash('success', 'Category added.');
        }
        redirect(SITE_URL . 'admin/blog');
    }
}

// Delete Post
if ($action === 'delete' && $id > 0) {
    $post = $db->fetch("SELECT featured_image FROM blog WHERE id = ?", [$id]);
    if ($post && !empty($post['featured_image'])) {
        (new FileUpload())->delete($post['featured_image']);
    }
    $db->delete('blog', 'id = ?', [$id]);
    Session::flash('success', 'Blog post deleted.');
    redirect(SITE_URL . 'admin/blog');
}

// Create/Update Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['category_action'])) {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $title = Validator::sanitize($_POST['title'] ?? '');
        $slug = slugify($_POST['slug'] ?: $title);
        $slug = (new SEO())->uniqueSlug($slug, 'blog', $id ?: null);
        $content = $_POST['content'] ?? '';

        $data = [
            'title'            => $title,
            'slug'             => $slug,
            'content'          => $content,
            'excerpt'          => Validator::sanitize($_POST['excerpt'] ?? ''),
            'category_id'      => $_POST['category_id'] ? (int)$_POST['category_id'] : null,
            'tags'             => Validator::sanitize($_POST['tags'] ?? ''),
            'meta_title'       => Validator::sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => Validator::sanitize($_POST['meta_description'] ?? ''),
            'meta_keywords'    => Validator::sanitize($_POST['meta_keywords'] ?? ''),
            'reading_time'     => readingTime($content),
            'status'           => $_POST['status'] ?? 'draft',
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
            'author_id'        => Session::get('user_id'),
        ];

        if ($data['status'] === 'published' && (empty($_POST['published_at']) || $id === 0)) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Image upload
        if (!empty($_FILES['featured_image']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['featured_image'], 'blog');
            if ($uploaded) {
                if ($id > 0) {
                    $old = $db->fetch("SELECT featured_image FROM blog WHERE id = ?", [$id]);
                    if ($old && !empty($old['featured_image'])) {
                        $uploader->delete($old['featured_image']);
                    }
                }
                $data['featured_image'] = $uploaded;
            } else {
                Session::flash('error', $uploader->getFirstError());
            }
        }

        if ($id > 0) {
            $db->update('blog', $data, 'id = ?', [$id]);
            Session::flash('success', 'Blog post updated.');
        } else {
            $db->insert('blog', $data);
            Session::flash('success', 'Blog post created.');
        }
        redirect(SITE_URL . 'admin/blog');
    }
}

$editPost = null;
if ($id > 0 && $action === 'edit') {
    $editPost = $db->fetch("SELECT * FROM blog WHERE id = ?", [$id]);
}

$categories = $db->fetchAll("SELECT * FROM blog_categories ORDER BY name ASC");
$posts = $db->fetchAll("SELECT b.*, bc.name as category_name FROM blog b LEFT JOIN blog_categories bc ON b.category_id = bc.id ORDER BY b.created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Blog Posts Manager</h3>
        <p class="text-secondary mb-0">Create, edit, publish, and optimize blog articles.</p>
    </div>
    <?php if ($action !== 'new' && !$editPost): ?>
        <a href="<?php echo SITE_URL; ?>admin/blog?action=new" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> New Article</a>
    <?php endif; ?>
</div>

<?php if ($action === 'new' || $editPost): ?>
<div class="admin-card p-4 mb-4">
    <h5 class="fw-bold mb-3"><?php echo $editPost ? 'Edit Article' : 'New Article'; ?></h5>
    <form method="post" enctype="multipart/form-data">
        <?php echo Session::csrfField(); ?>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Article Title *</label>
                <input type="text" name="title" class="form-control" data-slug-target required value="<?php echo e($editPost['title'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Uncategorized</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($editPost['category_id']) && $editPost['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo e($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-8">
                <label class="form-label">URL Slug</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?php echo e($editPost['slug'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="draft" <?php echo (isset($editPost['status']) && $editPost['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo (isset($editPost['status']) && $editPost['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="archived" <?php echo (isset($editPost['status']) && $editPost['status'] === 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Featured Image</label>
                <input type="file" name="featured_image" class="form-control" accept="image/*">
                <?php if (!empty($editPost['featured_image'])): ?>
                    <div class="mt-2"><img src="<?php echo uploadUrl($editPost['featured_image']); ?>" style="height: 50px; border-radius: 6px;"></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tags (comma-separated)</label>
                <input type="text" name="tags" class="form-control" placeholder="php, tutorial, web" value="<?php echo e($editPost['tags'] ?? ''); ?>">
            </div>

            <div class="col-12">
                <label class="form-label">Excerpt / Summary</label>
                <textarea name="excerpt" class="form-control" rows="2"><?php echo e($editPost['excerpt'] ?? ''); ?></textarea>
            </div>

            <div class="col-12">
                <label class="form-label">Full Content *</label>
                <textarea name="content" id="tinymce-editor" class="form-control" rows="10" required><?php echo e($editPost['content'] ?? ''); ?></textarea>
            </div>

            <!-- TinyMCE Rich Text Editor -->
            <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
            <script>
            tinymce.init({
                selector: '#tinymce-editor',
                height: 400,
                menubar: true,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | link image media | ' +
                    'forecolor backcolor | removeformat | code fullscreen | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 15px; line-height: 1.7; }',
                branding: false,
                promotion: false,
                skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
                content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
            });
            </script>

            <!-- SEO Settings -->
            <div class="col-12 mt-4">
                <h6 class="fw-bold border-bottom pb-2">SEO Settings</h6>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="<?php echo e($editPost['meta_title'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta Keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="<?php echo e($editPost['meta_keywords'] ?? ''); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="2"><?php echo e($editPost['meta_description'] ?? ''); ?></textarea>
            </div>

            <div class="col-12 mt-3">
                <div class="form-check me-4 d-inline-block">
                    <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" <?php echo (!empty($editPost['is_featured'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_featured">Featured Post</label>
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Article</button>
                <a href="<?php echo SITE_URL; ?>admin/blog" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="fw-bold mb-0">Articles List</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $p): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($p['title']); ?></div>
                                        <div class="small text-muted"><?php echo formatDate($p['published_at'] ?: $p['created_at']); ?></div>
                                    </td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e($p['category_name'] ?: 'Uncategorized'); ?></span></td>
                                    <td><span class="small"><?php echo (int)$p['views']; ?></span></td>
                                    <td>
                                        <?php if ($p['status'] === 'published'): ?>
                                            <span class="badge bg-success-subtle text-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning"><?php echo ucfirst($p['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>admin/blog?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="<?php echo SITE_URL; ?>admin/blog?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete article?"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Category -->
    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3">Add Blog Category</h5>
            <form method="post">
                <?php echo Session::csrfField(); ?>
                <input type="hidden" name="category_action" value="1">
                <div class="mb-3">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="category_name" class="form-control" required placeholder="e.g. Tutorials">
                </div>
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-plus-lg me-1"></i> Add Category</button>
            </form>
        </div>
    </div>
</div>
