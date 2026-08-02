<?php
/**
 * Blog Listing Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

// Input sanitization & filter extraction
$categorySlug = trim($_GET['category'] ?? '');
$tagFilter    = trim($_GET['tag'] ?? '');
$searchQuery  = trim($_GET['search'] ?? '');
$currentPage  = max(1, (int)($_GET['p'] ?? 1));

// Base WHERE clause with prepared statement placeholders
$where = "b.status = 'published'";
$params = [];

if (!empty($categorySlug)) {
    $where .= " AND bc.slug = ?";
    $params[] = $categorySlug;
}
if (!empty($tagFilter)) {
    $where .= " AND b.tags LIKE ?";
    $params[] = '%' . $tagFilter . '%';
}
if (!empty($searchQuery)) {
    $where .= " AND (b.title LIKE ? OR b.content LIKE ? OR b.tags LIKE ?)";
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

// Data structures
$totalPosts   = 0;
$posts        = [];
$categories   = [];
$popularPosts = [];

try {
    // 1. Total post count for pagination
    $totalPosts = (int)$db->fetchColumn(
        "SELECT COUNT(*) FROM blog b LEFT JOIN blog_categories bc ON b.category_id = bc.id WHERE {$where}",
        $params
    );
    
    // Calculate pagination offsets safely
    $pagination = paginate($totalPosts, BLOG_PER_PAGE, $currentPage);
    
    // 2. Fetch paginated blog posts with category details via single JOIN
    $posts = $db->fetchAll(
        "SELECT b.*, bc.name as category_name, bc.slug as category_slug 
         FROM blog b 
         LEFT JOIN blog_categories bc ON b.category_id = bc.id 
         WHERE {$where} 
         ORDER BY b.published_at DESC 
         LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
        $params
    ) ?: [];
    
    // 3. Fetch active categories with post counts
    $categories = $db->fetchAll(
        "SELECT bc.*, COUNT(b.id) as post_count 
         FROM blog_categories bc 
         LEFT JOIN blog b ON bc.id = b.category_id AND b.status = 'published' 
         GROUP BY bc.id 
         ORDER BY bc.sort_order ASC"
    ) ?: [];

    // 4. Fetch popular posts for sidebar
    $popularPosts = $db->fetchAll(
        "SELECT id, title, slug, views, published_at 
         FROM blog 
         WHERE status = 'published' 
         ORDER BY views DESC 
         LIMIT 5"
    ) ?: [];

} catch (Exception $e) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log('Blog Page Error: ' . $e->getMessage());
    }
    $pagination = paginate(0, BLOG_PER_PAGE);
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Medical <span class="gradient-text">Articles & Blog</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Blog</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Main Blog Section -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Blog Posts Column -->
            <div class="col-lg-8">
                <!-- Search Form -->
                <form method="get" action="<?php echo url('blog'); ?>" class="mb-4" data-aos="fade-up">
                    <input type="hidden" name="page" value="blog">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search medical articles & health tips..." value="<?php echo e($searchQuery); ?>">
                    </div>
                </form>
                
                <?php if (!empty($posts)): ?>
                <div class="row g-4">
                    <?php foreach ($posts as $index => $post): ?>
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 2) * 100; ?>">
                        <div class="blog-card">
                            <div class="card-image">
                                <?php if (!empty($post['featured_image'])): ?>
                                    <img src="<?php echo e(uploadUrl($post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 16/9; background: var(--bg-tertiary);">
                                        <i class="bi bi-journal-text" style="font-size: 3rem; color: var(--text-muted);"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($post['category_name'])): ?>
                                    <span class="category-badge"><?php echo e($post['category_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-content">
                                <div class="meta-info">
                                    <span><i class="bi bi-calendar3"></i> <?php echo formatDate($post['published_at']); ?></span>
                                    <span><i class="bi bi-eye"></i> <?php echo formatNumber((int)($post['views'] ?? 0)); ?></span>
                                </div>
                                <h3 class="blog-title">
                                    <a href="<?php echo url('blog/' . $post['slug']); ?>"><?php echo e($post['title']); ?></a>
                                </h3>
                                <p class="blog-excerpt"><?php echo e(truncate($post['excerpt'] ?: $post['content'], 110)); ?></p>
                                <a href="<?php echo url('blog/' . $post['slug']); ?>" class="read-more">
                                    Read Full Article <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="mt-5" data-aos="fade-up">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo url('blog?p=' . $pagination['prev_page'] . ($categorySlug ? '&category='.$categorySlug : '')); ?>"><i class="bi bi-chevron-left"></i></a></li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo url('blog?p=' . $i . ($categorySlug ? '&category='.$categorySlug : '')); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo url('blog?p=' . $pagination['next_page'] . ($categorySlug ? '&category='.$categorySlug : '')); ?>"><i class="bi bi-chevron-right"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <div class="text-center py-5" data-aos="fade-up">
                    <i class="bi bi-journal-x" style="font-size: 3.5rem; color: var(--text-muted);"></i>
                    <h3 class="mt-3">No Articles Found</h3>
                    <p class="text-muted">No blog articles matched your criteria. Try adjusting your search query.</p>
                    <a href="<?php echo url('blog'); ?>" class="btn btn-primary mt-2">View All Articles</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                <div class="card mb-4" data-aos="fade-up">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><i class="bi bi-folder me-2"></i>Categories</h4>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($categories as $cat): ?>
                            <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <a href="<?php echo url('blog?category=' . $cat['slug']); ?>" class="text-decoration-none text-body">
                                    <?php echo e($cat['name']); ?>

                                </a>
                                <span class="badge bg-primary rounded-pill"><?php echo (int)$cat['post_count']; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Popular Posts -->
                <?php if (!empty($popularPosts)): ?>
                <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <h4 class="card-title mb-3"><i class="bi bi-fire me-2"></i>Popular Articles</h4>
                        <div class="popular-posts">
                            <?php foreach ($popularPosts as $pop): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1" style="font-size: var(--font-size-sm);">
                                        <a href="<?php echo url('blog/' . $pop['slug']); ?>" class="text-decoration-none text-body fw-semibold">
                                            <?php echo e(truncate($pop['title'], 55)); ?>

                                        </a>
                                    </h6>
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo formatDate($pop['published_at']); ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
