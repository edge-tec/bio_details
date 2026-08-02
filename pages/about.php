<?php
/**
 * About Me Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$profile = $profile ?? [];
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">About <span class="gradient-text">Me</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Photo -->
            <div class="col-lg-5" data-aos="fade-right">
                <div class="text-center">
                    <?php if (!empty($profile['photo'])): ?>
                        <img src="<?php echo e(uploadUrl($profile['photo'])); ?>" alt="<?php echo e($profile['full_name'] ?? ''); ?>" class="about-photo">
                    <?php else: ?>
                        <div class="about-photo d-flex align-items-center justify-content-center mx-auto" style="background: var(--gradient-primary); height: 400px; width: 400px; font-size: 10rem; color: #fff;">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bio -->
            <div class="col-lg-7" data-aos="fade-left">
                <span class="section-subtitle d-inline-block mb-3">Who Am I</span>
                <h2 class="mb-4"><?php echo e($profile['full_name'] ?? 'About Me'); ?></h2>
                <div class="mb-4" style="white-space: pre-line; color: var(--text-secondary); line-height: 1.9;">
                    <?php echo nl2br(e($profile['bio_full'] ?? 'No biography available yet.')); ?>
                </div>
                
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo url('contact'); ?>" class="btn btn-primary"><i class="bi bi-envelope"></i> Contact Me</a>
                    <a href="<?php echo url('resume'); ?>" class="btn btn-outline-primary"><i class="bi bi-download"></i> Download Resume</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Personal Information -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">Details</span>
            <h2 class="section-title">Personal <span class="highlight">Information</span></h2>
        </div>
        
        <div class="info-grid" data-aos="fade-up">
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-person"></i></div>
                <div><div class="info-label">Full Name</div><div class="info-value"><?php echo e($profile['full_name'] ?? 'N/A'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-cake2"></i></div>
                <div><div class="info-label">Birthday</div><div class="info-value"><?php echo formatDate($profile['birthday'] ?? '', 'F d, Y'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-flag"></i></div>
                <div><div class="info-label">Nationality</div><div class="info-value"><?php echo e($profile['nationality'] ?? 'N/A'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                <div><div class="info-label">Location</div><div class="info-value"><?php echo e($profile['location'] ?? 'N/A'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-translate"></i></div>
                <div><div class="info-label">Languages</div><div class="info-value"><?php echo e($profile['languages'] ?? 'N/A'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-envelope"></i></div>
                <div><div class="info-label">Email</div><div class="info-value"><?php echo e($profile['email'] ?? 'N/A'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-telephone"></i></div>
                <div><div class="info-label">Phone</div><div class="info-value"><?php echo e($profile['phone'] ?? 'N/A'); ?></div></div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="bi bi-globe"></i></div>
                <div><div class="info-label">Website</div><div class="info-value"><?php echo e($profile['website'] ?? 'N/A'); ?></div></div>
            </div>
        </div>
        
        <!-- Social Links -->
        <?php if (!empty($socialLinks)): ?>
        <div class="text-center mt-4" data-aos="fade-up">
            <h5 class="mb-3">Connect With Me</h5>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <?php foreach ($socialLinks as $social): ?>
                <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" title="<?php echo e($social['platform']); ?>" style="--social-color: <?php echo e($social['color'] ?? '#2563EB'); ?>">
                    <i class="<?php echo e($social['icon']); ?>"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Mission, Vision, Goals -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="mvg-card">
                    <div class="mvg-icon"><i class="bi bi-rocket-takeoff"></i></div>
                    <h4>Mission</h4>
                    <p class="text-secondary mb-0"><?php echo e($profile['mission'] ?? 'To deliver innovative web solutions that empower businesses.'); ?></p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="mvg-card">
                    <div class="mvg-icon"><i class="bi bi-eye"></i></div>
                    <h4>Vision</h4>
                    <p class="text-secondary mb-0"><?php echo e($profile['vision'] ?? 'To become a leading web technology expert.'); ?></p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="mvg-card">
                    <div class="mvg-icon"><i class="bi bi-bullseye"></i></div>
                    <h4>Goals</h4>
                    <p class="text-secondary mb-0"><?php echo e($profile['goals'] ?? 'Continue mastering modern web technologies.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
