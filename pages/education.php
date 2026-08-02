<?php
/**
 * Education Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$education = [];
try {
    $education = $db->fetchAll("SELECT * FROM education WHERE is_active = 1 ORDER BY sort_order ASC, passing_year DESC");
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Education</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Education</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="timeline">
                    <?php foreach ($education as $index => $edu): ?>
                    <div class="timeline-item" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <span class="timeline-date">
                            <?php echo e($edu['start_year'] ?? ''); ?><?php echo $edu['passing_year'] ? ' — ' . e($edu['passing_year']) : ''; ?>
                        </span>
                        <h4 class="timeline-title"><?php echo e($edu['degree']); ?></h4>
                        <?php if (!empty($edu['field_of_study'])): ?>
                            <p class="timeline-subtitle text-primary"><?php echo e($edu['field_of_study']); ?></p>
                        <?php endif; ?>
                        <p class="timeline-subtitle">
                            <i class="bi bi-building me-1"></i><?php echo e($edu['institute']); ?>
                        </p>
                        <div class="timeline-content">
                            <?php if (!empty($edu['grade'])): ?>
                            <p><strong>Grade:</strong> <?php echo e($edu['grade']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($edu['description'])): ?>
                            <p><?php echo e($edu['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($education)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-mortarboard" style="font-size: 4rem; color: var(--text-muted);"></i>
                    <p class="text-secondary mt-3">Education entries will be displayed here once added.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
