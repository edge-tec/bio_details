<?php
/**
 * 404 Page Not Found
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }
?>

<section class="error-page">
    <div class="container">
        <div class="text-center" data-aos="zoom-in">
            <div class="error-code">404</div>
            <h2 class="mb-3">Oops! Page Not Found</h2>
            <p class="text-secondary mb-4" style="max-width: 500px; margin-left: auto; margin-right: auto;">
                The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?php echo url('home'); ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-house"></i> Back to Home
                </a>
                <a href="<?php echo url('contact'); ?>" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-envelope"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</section>
