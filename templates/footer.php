<?php
/**
 * ============================================
 * Footer Template
 * ============================================
 * 
 * Site footer with social links, quick links,
 * newsletter, cookie consent, scroll-to-top,
 * and script loading.
 * 
 * @package PersonalBiography
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}
?>
    </main><!-- End Main Content -->

    <!-- ============================================ -->
    <!-- FOOTER                                       -->
    <!-- ============================================ -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row g-4">
                    <!-- About Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget">
                            <h5 class="footer-title">
                                <span class="brand-accent">&lt;</span><?php echo e($profile['full_name'] ?? SITE_NAME); ?><span class="brand-accent">/&gt;</span>
                            </h5>
                            <p class="footer-desc">
                                <?php echo e(truncate($profile['bio_short'] ?? SITE_DESCRIPTION, 200)); ?>
                            </p>
                            <div class="footer-social">
                                <?php foreach ($socialLinks as $social): ?>
                                <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" title="<?php echo e($social['platform']); ?>" style="--social-color: <?php echo e($social['color'] ?? '#2563EB'); ?>">
                                    <i class="<?php echo e($social['icon']); ?>"></i>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <div class="footer-widget">
                            <h5 class="footer-title">Quick Links</h5>
                            <ul class="footer-links">
                                <li><a href="<?php echo url('home'); ?>"><i class="bi bi-chevron-right"></i> Home</a></li>
                                <li><a href="<?php echo url('about'); ?>"><i class="bi bi-chevron-right"></i> About</a></li>
                                <li><a href="<?php echo url('portfolio'); ?>"><i class="bi bi-chevron-right"></i> Portfolio</a></li>
                                <li><a href="<?php echo url('blog'); ?>"><i class="bi bi-chevron-right"></i> Blog</a></li>
                                <li><a href="<?php echo url('contact'); ?>"><i class="bi bi-chevron-right"></i> Contact</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Services Links -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h5 class="footer-title">Services</h5>
                            <ul class="footer-links">
                                <li><a href="<?php echo url('services'); ?>"><i class="bi bi-chevron-right"></i> Web Development</a></li>
                                <li><a href="<?php echo url('services'); ?>"><i class="bi bi-chevron-right"></i> PHP Development</a></li>
                                <li><a href="<?php echo url('services'); ?>"><i class="bi bi-chevron-right"></i> SEO Optimization</a></li>
                                <li><a href="<?php echo url('services'); ?>"><i class="bi bi-chevron-right"></i> Digital Marketing</a></li>
                                <li><a href="<?php echo url('services'); ?>"><i class="bi bi-chevron-right"></i> API Development</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Newsletter -->
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h5 class="footer-title">Newsletter</h5>
                            <p class="footer-desc">Subscribe to get the latest updates and insights.</p>
                            <form class="newsletter-form" id="newsletterForm" method="post" action="<?php echo url('contact'); ?>">
                                <?php echo Session::csrfField(); ?>
                                <input type="hidden" name="action" value="newsletter">
                                <div class="input-group">
                                    <input type="email" name="newsletter_email" class="form-control" placeholder="Your email" required aria-label="Email for newsletter">
                                    <button class="btn btn-primary" type="submit" aria-label="Subscribe">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </div>
                            </form>
                            <div class="footer-contact-info mt-3">
                                <?php if (!empty(getSetting('contact_email'))): ?>
                                <p><i class="bi bi-envelope me-2"></i><?php echo e(getSetting('contact_email')); ?></p>
                                <?php endif; ?>
                                <?php if (!empty(getSetting('contact_phone'))): ?>
                                <p><i class="bi bi-telephone me-2"></i><?php echo e(getSetting('contact_phone')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0"><?php echo e(getSetting('footer_text', '© ' . date('Y') . ' ' . ($profile['full_name'] ?? SITE_NAME) . '. All rights reserved.')); ?></p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-legal">
                            <a href="<?php echo url('privacy'); ?>">Privacy Policy</a>
                            <span class="separator">|</span>
                            <a href="<?php echo url('terms'); ?>">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- ============================================ -->
    <!-- SCROLL TO TOP BUTTON                         -->
    <!-- ============================================ -->
    <button type="button" class="btn btn-scroll-top" id="scrollTopBtn" aria-label="Scroll to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- ============================================ -->
    <!-- COOKIE CONSENT                               -->
    <!-- ============================================ -->
    <?php if (getSetting('cookie_consent_enabled', '1') === '1'): ?>
    <div class="cookie-consent" id="cookieConsent">
        <div class="cookie-content">
            <div class="cookie-text">
                <i class="bi bi-shield-check me-2"></i>
                We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.
                <a href="<?php echo url('privacy'); ?>" class="cookie-link">Learn More</a>
            </div>
            <div class="cookie-actions">
                <button type="button" class="btn btn-sm btn-primary" id="cookieAccept">Accept</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="cookieDecline">Decline</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ============================================ -->
    <!-- SCRIPTS                                      -->
    <!-- ============================================ -->
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
</body>
</html>
