<?php
/**
 * Gallery Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$galleryItems = [];
$galleryCategories = [];
try {
    $galleryItems = $db->fetchAll("SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC");
    $galleryCategories = array_unique(array_column($galleryItems, 'category'));
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Photo <span class="gradient-text">Gallery</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (!empty($galleryCategories)): ?>
        <div class="filter-buttons mb-4" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($galleryCategories as $cat): ?>
                <button class="filter-btn" data-filter="<?php echo e($cat); ?>"><?php echo e($cat); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="gallery-grid">
            <?php foreach ($galleryItems as $index => $item): ?>
            <div class="gallery-item" data-category="<?php echo e($item['category']); ?>" data-aos="zoom-in" data-aos-delay="<?php echo ($index % 4) * 50; ?>">
                <img src="<?php echo e(uploadUrl($item['image'])); ?>" alt="<?php echo e($item['alt_text'] ?? $item['caption'] ?? 'Gallery Image'); ?>" loading="lazy">
                <div class="gallery-overlay">
                    <span class="gallery-caption"><?php echo e($item['caption'] ?? ''); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($galleryItems)): ?>
        <div class="text-center py-5">
            <i class="bi bi-images" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Gallery images will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
