<?php
/**
 * Services Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$services = [];
try {
    $services = $db->fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Services</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Services</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">What I Offer</span>
            <h2 class="section-title">Professional <span class="highlight">Services</span></h2>
            <p class="section-desc">Comprehensive web development and digital marketing solutions.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($services as $index => $service): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo e($service['icon']); ?>"></i>
                    </div>
                    <h5 class="mb-3"><?php echo e($service['title']); ?></h5>
                    <p class="text-secondary mb-3"><?php echo e($service['description']); ?></p>
                    
                    <?php
                    $features = json_decode($service['features'] ?? '[]', true);
                    if (!empty($features)):
                    ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($features as $feature): ?>
                        <li class="mb-2" style="font-size: var(--font-size-sm); color: var(--text-secondary);">
                            <i class="bi bi-check2-circle text-success me-2"></i><?php echo e($feature); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($services)): ?>
        <div class="text-center py-5">
            <i class="bi bi-tools" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Services will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="section section-alt">
    <div class="container text-center" data-aos="fade-up">
        <div class="glass-card p-5 mx-auto" style="max-width: 700px;">
            <h3 class="mb-3">Need a Custom <span class="gradient-text">Solution</span>?</h3>
            <p class="text-secondary mb-4">Let's discuss your project requirements and find the perfect solution for your needs.</p>
            <a href="<?php echo url('contact'); ?>" class="btn btn-primary btn-lg"><i class="bi bi-chat-dots"></i> Let's Talk</a>
        </div>
    </div>
</section>
