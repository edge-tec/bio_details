<?php
/**
 * Blog Single Post Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$slug = $_GET['slug'] ?? '';
$post = null;
$relatedPosts = [];
$comments = [];
$profile = $profile ?? [];

if ($slug) {
    try {
        $post = $db->fetch(
            "SELECT b.*, bc.name as category_name, bc.slug as category_slug, u.name as author_name 
             FROM blog b 
             LEFT JOIN blog_categories bc ON b.category_id = bc.id 
             LEFT JOIN users u ON b.author_id = u.id 
             WHERE b.slug = ? AND b.status = 'published'",
            [$slug]
        );
        
        if ($post) {
            // Increment views
            $db->query("UPDATE blog SET views = views + 1 WHERE id = ?", [$post['id']]);
            
            // Get comments
            $comments = $db->fetchAll(
                "SELECT * FROM blog_comments WHERE blog_id = ? AND status = 'approved' ORDER BY created_at ASC",
                [$post['id']]
            );
            
            // Get related posts
            $relatedPosts = $db->fetchAll(
                "SELECT id, title, slug, featured_image, published_at, reading_time FROM blog WHERE status = 'published' AND id != ? AND category_id = ? ORDER BY published_at DESC LIMIT 3",
                [$post['id'], $post['category_id']]
            );
            
            // Update page title
            $pageTitle = ($post['meta_title'] ?? $post['title']) . ' | ' . SITE_NAME;
        }
    } catch (Exception $e) {}
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'comment' && $post) {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        if (!isRateLimited('comment', 3, 300)) {
            $validator = new Validator($_POST);
            $validator->required('comment_name', 'Name')
                      ->required('comment_email', 'Email')
                      ->email('comment_email', 'Email')
                      ->required('comment_text', 'Comment')
                      ->minLength('comment_text', 5, 'Comment');
            
            if ($validator->passes()) {
                $db->insert('blog_comments', [
                    'blog_id'    => $post['id'],
                    'name'       => Validator::sanitize($_POST['comment_name']),
                    'email'      => Validator::sanitizeEmail($_POST['comment_email']),
                    'comment'    => Validator::sanitize($_POST['comment_text']),
                    'ip_address' => getClientIp(),
                ]);
                Session::flash('success', 'Your comment has been submitted for review.');
                redirect(url('blog/' . $slug));
            } else {
                Session::flash('error', $validator->getFirstError());
            }
        } else {
            Session::flash('error', 'Too many comments. Please wait a few minutes.');
        }
    }
}

if (!$post) {
    http_response_code(404);
}
?>

<?php if ($post): ?>

<!-- Article Schema -->
<?php echo $seo->renderArticleSchema($post, $profile); ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up" style="font-size: var(--font-size-3xl);"><?php echo e($post['title']); ?></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('blog'); ?>">Blog</a></li>
                <li class="breadcrumb-item active"><?php echo e(truncate($post['title'], 40)); ?></li>
            </ol>
        </nav>
        <div class="d-flex justify-content-center gap-3 flex-wrap mt-3" style="font-size: var(--font-size-sm); color: var(--text-muted);">
            <span><i class="bi bi-person me-1"></i><?php echo e($post['author_name'] ?? 'Admin'); ?></span>
            <span><i class="bi bi-calendar3 me-1"></i><?php echo formatDate($post['published_at']); ?></span>
            <span><i class="bi bi-clock me-1"></i><?php echo (int)$post['reading_time']; ?> min read</span>
            <span><i class="bi bi-eye me-1"></i><?php echo formatNumber((int)$post['views']); ?> views</span>
            <?php if (!empty($post['category_name'])): ?>
            <span><a href="<?php echo url('blog/category/' . $post['category_slug']); ?>" class="category-badge"><?php echo e($post['category_name']); ?></a></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section" data-blog-id="<?php echo (int)$post['id']; ?>">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Featured Image -->
                <?php if (!empty($post['featured_image'])): ?>
                <div class="mb-4" data-aos="fade-up">
                    <img src="<?php echo e(uploadUrl($post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>" class="w-100" style="border-radius: var(--radius-lg);">
                </div>
                <?php endif; ?>
                
                <!-- Content -->
                <article class="blog-single-content" data-aos="fade-up">
                    <?php echo $post['content']; ?>
                </article>
                
                <!-- Tags -->
                <?php if (!empty($post['tags'])): ?>
                <div class="mt-4 pt-4" style="border-top: 1px solid var(--border-color);" data-aos="fade-up">
                    <h6 class="mb-2"><i class="bi bi-tags me-2"></i>Tags</h6>
                    <div class="tech-tags">
                        <?php foreach (explode(',', $post['tags']) as $tag): ?>
                            <?php if (trim($tag)): ?>
                                <a href="<?php echo url('blog/tag/' . slugify(trim($tag))); ?>" class="tech-tag"><?php echo e(trim($tag)); ?></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Share Buttons -->
                <div class="mt-4 pt-4" style="border-top: 1px solid var(--border-color);" data-aos="fade-up">
                    <h6 class="mb-2"><i class="bi bi-share me-2"></i>Share This Post</h6>
                    <div class="share-buttons">
                        <button class="share-btn facebook" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></button>
                        <button class="share-btn twitter" aria-label="Share on Twitter"><i class="bi bi-twitter-x"></i></button>
                        <button class="share-btn linkedin" aria-label="Share on LinkedIn"><i class="bi bi-linkedin"></i></button>
                        <button class="share-btn whatsapp" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></button>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <div class="mt-4"><?php echo renderFlashMessages(); ?></div>
                
                <!-- Comments -->
                <div class="mt-5" data-aos="fade-up">
                    <h4 class="mb-4"><i class="bi bi-chat-dots me-2"></i>Comments (<?php echo count($comments); ?>)</h4>
                    
                    <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="comment-author"><?php echo e($comment['name']); ?></span>
                            <span class="comment-date"><?php echo timeAgo($comment['created_at']); ?></span>
                        </div>
                        <p class="mt-2 mb-0" style="color: var(--text-secondary); font-size: var(--font-size-sm);"><?php echo nl2br(e($comment['comment'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Comment Form -->
                    <?php if (getSetting('comments_enabled', '1') === '1'): ?>
                    <div class="glass-card mt-4">
                        <h5 class="mb-3">Leave a Comment</h5>
                        <form method="post" class="contact-form">
                            <?php echo Session::csrfField(); ?>
                            <input type="hidden" name="action" value="comment">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="comment_name" class="form-control" placeholder="Your Name *" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="comment_email" class="form-control" placeholder="Your Email *" required>
                                </div>
                                <div class="col-12">
                                    <textarea name="comment_text" class="form-control" rows="4" placeholder="Your Comment *" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Submit Comment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="mt-5" data-aos="fade-up">
                    <h4 class="mb-4"><i class="bi bi-journal-text me-2"></i>Related Posts</h4>
                    <div class="row g-3">
                        <?php foreach ($relatedPosts as $rp): ?>
                        <div class="col-md-4">
                            <div class="blog-card">
                                <div class="card-image">
                                    <?php if (!empty($rp['featured_image'])): ?>
                                        <img src="<?php echo e(uploadUrl($rp['featured_image'])); ?>" alt="<?php echo e($rp['title']); ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 16/9; background: var(--bg-tertiary);">
                                            <i class="bi bi-journal-text" style="font-size: 2rem; color: var(--text-muted);"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><a href="<?php echo url('blog/' . $rp['slug']); ?>"><?php echo e($rp['title']); ?></a></h6>
                                    <small class="text-muted"><?php echo formatDate($rp['published_at']); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<section class="error-page">
    <div class="text-center">
        <div class="error-code">404</div>
        <h2 class="mb-3">Post Not Found</h2>
        <p class="text-secondary mb-4">The blog post you're looking for doesn't exist or has been removed.</p>
        <a href="<?php echo url('blog'); ?>" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Back to Blog</a>
    </div>
</section>
<?php endif; ?>
