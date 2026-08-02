<?php
/**
 * Skills Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

// Get skills grouped by category
$skills = [];
try {
    $skills = $db->fetchAll("SELECT * FROM skills WHERE is_active = 1 ORDER BY category ASC, sort_order ASC");
} catch (Exception $e) {}

// Group by category
$skillsByCategory = [];
foreach ($skills as $skill) {
    $skillsByCategory[$skill['category']][] = $skill;
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Skills</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Skills</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">Expertise</span>
            <h2 class="section-title">Technical <span class="highlight">Skills</span></h2>
            <p class="section-desc">Technologies and tools I work with to build amazing products.</p>
        </div>
        
        <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
        <div class="mb-5" data-aos="fade-up">
            <h4 class="mb-4">
                <span class="gradient-text"><?php echo e($category); ?></span>
            </h4>
            <div class="row g-4">
                <?php foreach ($categorySkills as $skill): ?>
                <div class="col-lg-6">
                    <div class="skill-item">
                        <div class="skill-header">
                            <span class="skill-name">
                                <?php if (!empty($skill['icon'])): ?>
                                    <i class="<?php echo e($skill['icon']); ?>"></i>
                                <?php endif; ?>
                                <?php echo e($skill['name']); ?>
                            </span>
                            <span class="skill-percentage"><?php echo (int)$skill['percentage']; ?>%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-bar-fill" data-percentage="<?php echo (int)$skill['percentage']; ?>"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($skillsByCategory)): ?>
        <div class="text-center py-5">
            <i class="bi bi-gear" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Skills will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
