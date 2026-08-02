<?php
/**
 * SEO Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$pageSlug = $_GET['slug'] ?? 'home';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $targetSlug = $_POST['page_slug'] ?? 'home';
        $data = [
            'meta_title'       => Validator::sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => Validator::sanitize($_POST['meta_description'] ?? ''),
            'meta_keywords'    => Validator::sanitize($_POST['meta_keywords'] ?? ''),
            'og_title'         => Validator::sanitize($_POST['og_title'] ?? ''),
            'og_description'   => Validator::sanitize($_POST['og_description'] ?? ''),
            'canonical_url'    => Validator::sanitizeUrl($_POST['canonical_url'] ?? ''),
            'robots'           => Validator::sanitize($_POST['robots'] ?? 'index, follow'),
        ];

        // Handle OG Image
        if (!empty($_FILES['og_image']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['og_image'], 'seo');
            if ($uploaded) {
                $data['og_image'] = uploadUrl($uploaded);
            }
        }

        if ($db->exists('seo', 'page_slug = ?', [$targetSlug])) {
            $db->update('seo', $data, 'page_slug = ?', [$targetSlug]);
        } else {
            $data['page_slug'] = $targetSlug;
            $db->insert('seo', $data);
        }

        Session::flash('success', 'SEO settings saved for page: ' . e($targetSlug));
        redirect(SITE_URL . 'admin/seo?slug=' . $targetSlug);
    }
}

$seoItem = $db->fetch("SELECT * FROM seo WHERE page_slug = ?", [$pageSlug]) ?: [];
$availablePages = ['home', 'about', 'skills', 'experience', 'education', 'portfolio', 'services', 'blog', 'contact', 'gallery', 'testimonials'];
?>

<div class="mb-4">
    <h3 class="fw-bold">SEO Manager</h3>
    <p class="text-secondary">Manage meta titles, meta descriptions, keywords, Open Graph, and search crawler rules per page.</p>
</div>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="admin-card p-3">
            <h6 class="fw-bold mb-3 px-2">Select Page</h6>
            <div class="list-group list-group-flush">
                <?php foreach ($availablePages as $p): ?>
                    <a href="<?php echo SITE_URL; ?>admin/seo?slug=<?php echo $p; ?>" class="list-group-item list-group-item-action border-0 rounded mb-1 <?php echo $pageSlug === $p ? 'active' : ''; ?>">
                        <i class="bi bi-file-earmark-text me-2"></i><?php echo ucfirst($p); ?> Page
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3">SEO Settings for "<?php echo ucfirst($pageSlug); ?>" Page</h5>
            <form method="post" enctype="multipart/form-data">
                <?php echo Session::csrfField(); ?>
                <input type="hidden" name="page_slug" value="<?php echo e($pageSlug); ?>">

                <div class="mb-3">
                    <label class="form-label">Meta Title (50-60 characters)</label>
                    <input type="text" name="meta_title" class="form-control" value="<?php echo e($seoItem['meta_title'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Meta Description (150-160 characters)</label>
                    <textarea name="meta_description" class="form-control" rows="3"><?php echo e($seoItem['meta_description'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Meta Keywords (comma-separated)</label>
                    <input type="text" name="meta_keywords" class="form-control" value="<?php echo e($seoItem['meta_keywords'] ?? ''); ?>">
                </div>

                <h6 class="fw-bold border-bottom pb-2 mt-4">Open Graph (Social Sharing)</h6>

                <div class="mb-3">
                    <label class="form-label">OG Title</label>
                    <input type="text" name="og_title" class="form-control" value="<?php echo e($seoItem['og_title'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">OG Description</label>
                    <textarea name="og_description" class="form-control" rows="2"><?php echo e($seoItem['og_description'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">OG Image (1200x630 recommended)</label>
                    <input type="file" name="og_image" class="form-control" accept="image/*">
                    <?php if (!empty($seoItem['og_image'])): ?>
                        <div class="mt-2"><img src="<?php echo e($seoItem['og_image']); ?>" style="height: 50px; border-radius: 6px;"></div>
                    <?php endif; ?>
                </div>

                <h6 class="fw-bold border-bottom pb-2 mt-4">Advanced Crawler Rules</h6>

                <div class="mb-3">
                    <label class="form-label">Canonical URL</label>
                    <input type="url" name="canonical_url" class="form-control" placeholder="https://example.com/page" value="<?php echo e($seoItem['canonical_url'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Robots Tag</label>
                    <select name="robots" class="form-select">
                        <option value="index, follow" <?php echo (isset($seoItem['robots']) && $seoItem['robots'] === 'index, follow') ? 'selected' : ''; ?>>index, follow (Default)</option>
                        <option value="noindex, follow" <?php echo (isset($seoItem['robots']) && $seoItem['robots'] === 'noindex, follow') ? 'selected' : ''; ?>>noindex, follow</option>
                        <option value="noindex, nofollow" <?php echo (isset($seoItem['robots']) && $seoItem['robots'] === 'noindex, nofollow') ? 'selected' : ''; ?>>noindex, nofollow</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-lg mt-3"><i class="bi bi-save me-1"></i> Save SEO Settings</button>
            </form>
        </div>
    </div>
</div>
