<?php
/**
 * Certificates Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$certificates = [];
try {
    $certificates = $db->fetchAll("SELECT * FROM certificates WHERE is_active = 1 ORDER BY sort_order ASC, issue_date DESC");
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Certificates</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Certificates</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($certificates as $index => $cert): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                <div class="certificate-card">
                    <?php if (!empty($cert['image'])): ?>
                    <div class="card-image" style="aspect-ratio: 16/10; overflow: hidden;">
                        <img src="<?php echo e(uploadUrl($cert['image'])); ?>" alt="<?php echo e($cert['title']); ?>" loading="lazy" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="certificate-issuer"><?php echo e($cert['issuer']); ?></span>
                        <h5 class="mt-2 mb-2"><?php echo e($cert['title']); ?></h5>
                        <?php if (!empty($cert['issue_date'])): ?>
                            <small class="text-muted d-block mb-2"><i class="bi bi-calendar3 me-1"></i>Issued: <?php echo formatDate($cert['issue_date']); ?></small>
                        <?php endif; ?>
                        <?php if (!empty($cert['credential_id'])): ?>
                            <small class="text-muted d-block mb-2"><i class="bi bi-key me-1"></i>ID: <?php echo e($cert['credential_id']); ?></small>
                        <?php endif; ?>
                        <?php if (!empty($cert['description'])): ?>
                            <p class="text-secondary mt-2 mb-2" style="font-size: var(--font-size-sm);"><?php echo e(truncate($cert['description'], 150)); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($cert['credential_url'])): ?>
                            <a href="<?php echo e($cert['credential_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bi bi-box-arrow-up-right"></i> View Credential
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($certificates)): ?>
        <div class="text-center py-5">
            <i class="bi bi-award" style="font-size: 4rem; color: var(--text-muted);"></i>
            <p class="text-secondary mt-3">Certificates will be displayed here once added.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
