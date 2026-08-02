<?php
/**
 * Blog Listing Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

// Filters
$categorySlug = $_GET['category'] ?? '';
$tagFilter = $_GET['tag'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$currentPage = max(1, (int)($_GET['p'] ?? 1));

// Build query
$where = "b.status = 'published'";
$params = [];

if ($categorySlug) {
    $where .= " AND bc.slug = ?";
    $params[] = $categorySlug;
}
if ($tagFilter) {
    $where .= " AND b.tags LIKE ?";
    $params[] = '%' . $tagFilter . '%';
}
if ($searchQuery) {
    $where .= " AND (b.title LIKE ? OR b.content LIKE ? OR b.tags LIKE ?)";
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

// Count
$totalPosts = 0;
$posts = [];
$categories = [];
$popularPosts = [];

try {
    $totalPosts = (int)$db->fetchColumn(
        "SELECT COUNT(*) FROM blog b LEFT JOIN blog_categories bc ON b.category_id = bc.id WHERE {$where}", $params
    );
    
    $pagination = paginate($totalPosts, BLOG_PER_PAGE, $currentPage);
    
    $posts = $db->fetchAll(
        "SELECT b.*, bc.name as category_name, bc.slug as category_slug 
         FROM blog b 
         LEFT JOIN blog_categories bc ON b.category_id = bc.id 
         WHERE {$where} 
         ORDER BY b.published_at DESC 
         LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
        $params
    );
    
    $categories = $db->fetchAll("SELECT bc.*, COUNT(b.id) as post_count FROM blog_categories bc LEFT JOIN blog b ON bc.id = b.category_id AND b.status = 'published' GROUP BY bc.id ORDER BY bc.sort_order ASC");
    $popularPosts = $db->fetchAll("SELECT id, title, slug, views, published_at FROM blog WHERE status = 'published' ORDER BY views DESC LIMIT 5");
} catch (Exception $e) {
    $pagination = paginate(0, BLOG_PER_PAGE);
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Blog</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Blog</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Blog Posts -->
            <div class="col-lg-8">
                <!-- Search -->
                <form method="get" action="<?php echo url('blog'); ?>" class="mb-4" data-aos="fade-up">
                    <input type="hidden" name="page" value="blog">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search articles..." value="<?php echo e($searchQuery); ?>">
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
                            </div>
                            <div class="card-body">
                                <?php if (!empty($post['category_name'])): ?>
                                    <a href="<?php echo url('blog/category/' . $post['category_slug']); ?>" class="category-badge"><?php echo e($post['category_name']); ?></a>
                                <?php endif; ?>
                                <h5 class="card-title">
                                    <a href="<?php echo url('blog/' . $post['slug']); ?>"><?php echo e($post['title']); ?></a>
                                </h5>
                                <div class="card-meta">
                                    <span><i class="bi bi-calendar3"></i> <?php echo formatDate($post['published_at']); ?></span>
                                    <span><i class="bi bi-clock"></i> <?php echo (int)$post['reading_time']; ?> min</span>
                                    <span><i class="bi bi-eye"></i> <?php echo formatNumber((int)$post['views']); ?></span>
                                </div>
                                <p class="card-text"><?php echo e(truncate($post['excerpt'] ?? strip_tags($post['content']), 120)); ?></p>
                                <a href="<?php echo url('blog/' . $post['slug']); ?>" class="btn btn-sm btn-outline-primary">Read More <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4">
                    <?php echo renderPagination($pagination, url('blog')); ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-text" style="font-size: 4rem; color: var(--text-muted);"></i>
                    <p class="text-secondary mt-3">No blog posts found.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                <div class="glass-card mb-4" data-aos="fade-left">
                    <h5 class="mb-3"><i class="bi bi-folder me-2"></i>Categories</h5>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($categories as $cat): ?>
                        <li class="mb-2">
                            <a href="<?php echo url('blog/category/' . $cat['slug']); ?>" class="d-flex justify-content-between align-items-center" style="color: var(--text-secondary);">
                                <?php echo e($cat['name']); ?>
                                <span class="badge bg-primary rounded-pill"><?php echo (int)$cat['post_count']; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Popular Posts -->
                <?php if (!empty($popularPosts)): ?>
                <div class="glass-card" data-aos="fade-left" data-aos-delay="100">
                    <h5 class="mb-3"><i class="bi bi-fire me-2"></i>Popular Posts</h5>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($popularPosts as $pp): ?>
                        <li class="mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                            <a href="<?php echo url('blog/' . $pp['slug']); ?>" style="color: var(--text-primary); font-weight: 600; font-size: var(--font-size-sm);">
                                <?php echo e($pp['title']); ?>
                            </a>
                            <div style="font-size: var(--font-size-xs); color: var(--text-muted); margin-top: 0.25rem;">
                                <i class="bi bi-eye me-1"></i><?php echo formatNumber((int)$pp['views']); ?> views
                                <span class="ms-2"><i class="bi bi-calendar3 me-1"></i><?php echo formatDate($pp['published_at']); ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
