<?php
/**
 * Portfolio Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$portfolioItems = [];
$categories = [];
try {
    $portfolioItems = $db->fetchAll("SELECT * FROM portfolio WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC");
    // Get unique categories
    $categories = array_unique(array_column($portfolioItems, 'category'));
} catch (Exception $e) {}

$searchQuery = $_GET['search'] ?? '';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Portfolio</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Portfolio</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Search -->
        <div class="row justify-content-center mb-4" data-aos="fade-up">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="form-control" placeholder="Search projects..." data-search=".portfolio-item" value="<?php echo e($searchQuery); ?>">
                </div>
            </div>
        </div>
        
        <!-- Filter Buttons -->
        <?php if (!empty($categories)): ?>
        <div class="filter-buttons" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn" data-filter="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Portfolio Grid -->
        <div class="row g-4">
            <?php foreach ($portfolioItems as $index => $project): ?>
            <div class="col-lg-4 col-md-6 portfolio-item" data-category="<?php echo e($project['category']); ?>" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                <div class="portfolio-card">
                    <div class="card-image">
                        <?php if (!empty($project['image'])): ?>
                            <img src="<?php echo e(uploadUrl($project['image'])); ?>" alt="<?php echo e($project['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 16/10; background: var(--bg-tertiary);">
                                <i class="bi bi-code-slash" style="font-size: 3rem; color: var(--text-muted);"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-overlay">
                            <?php if (!empty($project['live_url'])): ?>
                                <a href="<?php echo e($project['live_url']); ?>" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-up-right"></i> Live Demo</a>
                            <?php endif; ?>
                            <?php if (!empty($project['github_url'])): ?>
                                <a href="<?php echo e($project['github_url']); ?>" target="_blank" class="btn btn-sm btn-glass"><i class="bi bi-github"></i> Source</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="category-badge"><?php echo e($project['category']); ?></span>
                        <h5 class="card-title"><?php echo e($project['title']); ?></h5>
                        <p class="card-text"><?php echo e(truncate($project['short_description'] ?? $project['description'] ?? '', 120)); ?></p>
                        <div class="tech-tags">
                            <?php foreach (explode(',', $project['technologies'] ?? '') as $tech): ?>
                                <?php if (trim($tech)): ?>
                                    <span class="tech-tag"><?php echo e(trim($tech)); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($portfolioItems)): ?>
        <div class="text-center py-5">
            <i class="bi bi-collection" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Portfolio projects will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
