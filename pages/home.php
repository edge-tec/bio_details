<?php
/**
 * ============================================
 * Home Page
 * ============================================
 * 
 * Hero banner with typing animation, photo,
 * download/hire/contact buttons, stats counters,
 * and featured sections preview.
 * 
 * @package PersonalBiography
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

// Get profile data
$profile = $profile ?? [];
$typingTexts = $profile['typing_texts'] ?? '["Developer", "Designer", "Creator"]';

// Get featured portfolio items
$featuredPortfolio = [];
try {
    $featuredPortfolio = $db->fetchAll(
        "SELECT * FROM portfolio WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC LIMIT 6"
    );
} catch (Exception $e) {}

// Get featured blog posts
$featuredPosts = [];
try {
    $featuredPosts = $db->fetchAll(
        "SELECT b.*, bc.name as category_name FROM blog b LEFT JOIN blog_categories bc ON b.category_id = bc.id WHERE b.status = 'published' AND b.is_featured = 1 ORDER BY b.published_at DESC LIMIT 3"
    );
} catch (Exception $e) {}

// Get testimonials
$testimonials = [];
try {
    $testimonials = $db->fetchAll(
        "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 4"
    );
} catch (Exception $e) {}

// Get services
$services = [];
try {
    $services = $db->fetchAll(
        "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6"
    );
} catch (Exception $e) {}
?>

<!-- ============================================ -->
<!-- HERO SECTION                                 -->
<!-- ============================================ -->
<section class="hero-section" id="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 order-lg-1 order-2">
                <div class="hero-content" data-aos="fade-right">
                    <p class="hero-greeting">Hello, I'm <span class="wave">👋</span></p>
                    <h1 class="hero-name">
                        <?php echo e($profile['full_name'] ?? 'John Doe'); ?>
                    </h1>
                    <p class="hero-profession"><?php echo e($profile['profession'] ?? 'Full Stack Developer'); ?></p>
                    
                    <!-- Typing Animation -->
                    <div class="typing-wrapper">
                        I'm a <span id="typing-text" data-texts='<?php echo e($typingTexts); ?>'></span><span class="typing-cursor"></span>
                    </div>
                    
                    <p class="hero-bio">
                        <?php echo e($profile['bio_short'] ?? 'Passionate developer building modern web applications.'); ?>
                    </p>
                    
                    <!-- Hero Buttons -->
                    <div class="hero-buttons">
                        <a href="<?php echo url('resume'); ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-download"></i> Download Resume
                        </a>
                        <a href="<?php echo url('contact'); ?>" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-person-check"></i> Hire Me
                        </a>
                        <a href="<?php echo url('contact'); ?>" class="btn btn-glass btn-lg">
                            <i class="bi bi-chat-dots"></i> Contact Me
                        </a>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="hero-stats" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-item">
                            <div class="stat-number" data-count="<?php echo (int)($profile['stats_experience_years'] ?? 5); ?>">0</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-count="<?php echo (int)($profile['stats_projects'] ?? 120); ?>">0</div>
                            <div class="stat-label">Projects Completed</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-count="<?php echo (int)($profile['stats_clients'] ?? 85); ?>">0</div>
                            <div class="stat-label">Happy Clients</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" data-count="<?php echo (int)($profile['stats_awards'] ?? 15); ?>">0</div>
                            <div class="stat-label">Awards Won</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Photo -->
            <div class="col-lg-5 order-lg-2 order-1 text-center">
                <div class="hero-photo-wrapper" data-aos="fade-left">
                    <?php if (!empty($profile['photo'])): ?>
                        <img src="<?php echo e(uploadUrl($profile['photo'])); ?>" alt="<?php echo e($profile['full_name'] ?? 'Profile Photo'); ?>" class="hero-photo" loading="eager">
                    <?php else: ?>
                        <div class="hero-photo d-flex align-items-center justify-content-center" style="background: var(--gradient-primary); font-size: 8rem; color: #fff;">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- SERVICES PREVIEW                             -->
<!-- ============================================ -->
<?php if (!empty($services)): ?>
<section class="section section-alt" id="services-preview">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">What I Do</span>
            <h2 class="section-title">My <span class="highlight">Services</span></h2>
            <p class="section-desc">Professional services to help you achieve your digital goals.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($services as $index => $service): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo e($service['icon']); ?>"></i>
                    </div>
                    <h5><?php echo e($service['title']); ?></h5>
                    <p class="text-secondary mb-0"><?php echo e(truncate($service['description'], 120)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="<?php echo url('services'); ?>" class="btn btn-outline-primary">
                View All Services <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================ -->
<!-- FEATURED PORTFOLIO                           -->
<!-- ============================================ -->
<?php if (!empty($featuredPortfolio)): ?>
<section class="section" id="portfolio-preview">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">My Work</span>
            <h2 class="section-title">Featured <span class="highlight">Projects</span></h2>
            <p class="section-desc">A selection of my recent work and projects.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredPortfolio as $index => $project): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="portfolio-card">
                    <div class="card-image">
                        <?php if (!empty($project['image'])): ?>
                            <img src="<?php echo e(uploadUrl($project['image'])); ?>" alt="<?php echo e($project['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 16/10; background: var(--bg-tertiary);">
                                <i class="bi bi-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-overlay">
                            <?php if (!empty($project['live_url'])): ?>
                                <a href="<?php echo e($project['live_url']); ?>" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-up-right"></i> Live</a>
                            <?php endif; ?>
                            <?php if (!empty($project['github_url'])): ?>
                                <a href="<?php echo e($project['github_url']); ?>" target="_blank" class="btn btn-sm btn-glass"><i class="bi bi-github"></i> Code</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($project['title']); ?></h5>
                        <p class="card-text"><?php echo e(truncate($project['short_description'] ?? $project['description'] ?? '', 100)); ?></p>
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
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="<?php echo url('portfolio'); ?>" class="btn btn-outline-primary">
                View All Projects <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================ -->
<!-- TESTIMONIALS PREVIEW                         -->
<!-- ============================================ -->
<?php if (!empty($testimonials)): ?>
<section class="section section-alt" id="testimonials-preview">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">Testimonials</span>
            <h2 class="section-title">What <span class="highlight">Clients Say</span></h2>
            <p class="section-desc">Feedback from people I've had the pleasure of working with.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($testimonials as $index => $testimonial): ?>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($testimonial['photo'])): ?>
                            <img src="<?php echo e(uploadUrl($testimonial['photo'])); ?>" alt="<?php echo e($testimonial['name']); ?>" class="testimonial-avatar">
                        <?php else: ?>
                            <div class="testimonial-avatar d-flex align-items-center justify-content-center" style="background: rgba(var(--primary-rgb), 0.1); font-size: 1.5rem; color: var(--primary);">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="testimonial-author"><?php echo e($testimonial['name']); ?></div>
                            <div class="testimonial-role"><?php echo e($testimonial['position']); ?>, <?php echo e($testimonial['company']); ?></div>
                        </div>
                    </div>
                    <?php echo starRating((int)$testimonial['rating']); ?>
                    <p class="testimonial-text"><?php echo e(truncate($testimonial['review'], 250)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================ -->
<!-- FEATURED BLOG POSTS                          -->
<!-- ============================================ -->
<?php if (!empty($featuredPosts)): ?>
<section class="section" id="blog-preview">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-subtitle">Blog</span>
            <h2 class="section-title">Latest <span class="highlight">Articles</span></h2>
            <p class="section-desc">Insights, tutorials, and thoughts on web development.</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredPosts as $index => $post): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="blog-card">
                    <div class="card-image">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?php echo e(uploadUrl($post['featured_image'])); ?>" alt="<?php echo e($post['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 16/9; background: var(--bg-tertiary);">
                                <i class="bi bi-journal-text" style="font-size: 3rem; color: var(--text-muted);"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($post['category_name'])): ?>
                            <span class="category-badge"><?php echo e($post['category_name']); ?></span>
                        <?php endif; ?>
                        <h5 class="card-title">
                            <a href="<?php echo url('blog/' . $post['slug']); ?>"><?php echo e($post['title']); ?></a>
                        </h5>
                        <div class="card-meta">
                            <span><i class="bi bi-calendar3"></i> <?php echo formatDate($post['published_at']); ?></span>
                            <span><i class="bi bi-clock"></i> <?php echo (int)$post['reading_time']; ?> min read</span>
                            <span><i class="bi bi-eye"></i> <?php echo formatNumber((int)$post['views']); ?></span>
                        </div>
                        <p class="card-text"><?php echo e(truncate($post['excerpt'] ?? strip_tags($post['content']), 100)); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="<?php echo url('blog'); ?>" class="btn btn-outline-primary">
                Read More Articles <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================ -->
<!-- CTA SECTION                                  -->
<!-- ============================================ -->
<section class="section section-alt" id="cta">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <div class="glass-card p-5 text-center mx-auto" style="max-width: 700px;">
                <h2 class="mb-3">Let's Work <span class="gradient-text">Together</span></h2>
                <p class="text-secondary mb-4">Have a project in mind? Let's discuss how I can help bring your ideas to life.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?php echo url('contact'); ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-envelope"></i> Get In Touch
                    </a>
                    <a href="<?php echo url('portfolio'); ?>" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-collection"></i> View Portfolio
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
