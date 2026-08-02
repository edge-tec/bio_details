<?php
/**
 * Testimonials Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$testimonials = [];
try {
    $testimonials = $db->fetchAll("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC");
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Client <span class="gradient-text">Testimonials</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Testimonials</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($testimonials as $index => $testimonial): ?>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 2) * 100; ?>">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($testimonial['photo'])): ?>
                            <img src="<?php echo e(uploadUrl($testimonial['photo'])); ?>" alt="<?php echo e($testimonial['name']); ?>" class="testimonial-avatar">
                        <?php else: ?>
                            <div class="testimonial-avatar d-flex align-items-center justify-content-center" style="background: rgba(var(--primary-rgb), 0.1); font-size: 1.5rem; color: var(--primary);">
                                <?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="testimonial-author"><?php echo e($testimonial['name']); ?></div>
                            <div class="testimonial-role">
                                <?php echo e($testimonial['position']); ?><?php echo $testimonial['company'] ? ', ' . e($testimonial['company']) : ''; ?>
                            </div>
                        </div>
                    </div>
                    <?php echo starRating((int)$testimonial['rating']); ?>
                    <p class="testimonial-text"><?php echo e($testimonial['review']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($testimonials)): ?>
        <div class="text-center py-5">
            <i class="bi bi-chat-quote" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Testimonials will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
