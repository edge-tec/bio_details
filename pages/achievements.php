<?php
/**
 * Achievements Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$achievements = [];
try {
    $achievements = $db->fetchAll("SELECT * FROM achievements WHERE is_active = 1 ORDER BY sort_order ASC, achievement_date DESC");
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Achievements</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Achievements</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($achievements as $index => $achievement): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="<?php echo e($achievement['icon'] ?? 'bi-trophy'); ?>"></i>
                    </div>
                    <h5 class="mb-2"><?php echo e($achievement['title']); ?></h5>
                    <?php if (!empty($achievement['achievement_date'])): ?>
                        <small class="text-muted d-block mb-2"><i class="bi bi-calendar3 me-1"></i><?php echo formatDate($achievement['achievement_date']); ?></small>
                    <?php endif; ?>
                    <p class="text-secondary mb-0"><?php echo e($achievement['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($achievements)): ?>
        <div class="text-center py-5">
            <i class="bi bi-trophy" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Achievements will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
