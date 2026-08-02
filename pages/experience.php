<?php
/**
 * Experience Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$experiences = [];
try {
    $experiences = $db->fetchAll("SELECT * FROM experience WHERE is_active = 1 ORDER BY sort_order ASC, start_date DESC");
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Work <span class="gradient-text">Experience</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Experience</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="timeline">
                    <?php foreach ($experiences as $index => $exp): ?>
                    <div class="timeline-item <?php echo $exp['is_current'] ? 'current' : ''; ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <span class="timeline-date">
                            <?php echo formatDate($exp['start_date'], 'M Y'); ?> — <?php echo $exp['is_current'] ? 'Present' : formatDate($exp['end_date'], 'M Y'); ?>
                        </span>
                        <h4 class="timeline-title"><?php echo e($exp['position']); ?></h4>
                        <p class="timeline-subtitle">
                            <?php if (!empty($exp['company_url'])): ?>
                                <a href="<?php echo e($exp['company_url']); ?>" target="_blank"><?php echo e($exp['company']); ?></a>
                            <?php else: ?>
                                <?php echo e($exp['company']); ?>
                            <?php endif; ?>
                        </p>
                        <div class="timeline-content">
                            <p><?php echo e($exp['description']); ?></p>
                            
                            <?php
                            $responsibilities = json_decode($exp['responsibilities'] ?? '[]', true);
                            if (!empty($responsibilities)):
                            ?>
                            <ul class="list-unstyled mb-3">
                                <?php foreach ($responsibilities as $resp): ?>
                                <li class="mb-1" style="color: var(--text-secondary); font-size: var(--font-size-sm);">
                                    <i class="bi bi-check-circle text-success me-2"></i><?php echo e($resp); ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                            
                            <?php if (!empty($exp['technologies'])): ?>
                            <div class="tech-tags">
                                <?php foreach (explode(',', $exp['technologies']) as $tech): ?>
                                    <?php if (trim($tech)): ?>
                                        <span class="tech-tag"><?php echo e(trim($tech)); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($experiences)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-briefcase" style="font-size: 4rem; color: var(--text-muted);"></i>
                    <p class="text-secondary mt-3">Experience entries will be displayed here once added.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
