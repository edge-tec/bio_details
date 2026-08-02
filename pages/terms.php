<?php
/**
 * Terms of Service Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }
$siteName = $profile['full_name'] ?? SITE_NAME;
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Terms of <span class="gradient-text">Service</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Terms of Service</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="legal-content" data-aos="fade-up">
            <p><strong>Last Updated:</strong> <?php echo date('F d, Y'); ?></p>
            
            <h2>1. Agreement to Terms</h2>
            <p>By accessing and using this website, you accept and agree to be bound by these Terms of Service. If you disagree with any part of these terms, you may not access the website.</p>
            
            <h2>2. Intellectual Property</h2>
            <p>The content on this website, including text, graphics, logos, images, and software, is the property of <?php echo e($siteName); ?> and is protected by copyright and intellectual property laws.</p>
            
            <h2>3. Use License</h2>
            <p>Permission is granted to temporarily view the materials on this website for personal, non-commercial use only. This license does not include:</p>
            <ul>
                <li>Modifying or copying the materials</li>
                <li>Using the materials for commercial purposes</li>
                <li>Attempting to reverse engineer any software</li>
                <li>Removing any copyright or proprietary notations</li>
            </ul>
            
            <h2>4. Disclaimer</h2>
            <p>The materials on this website are provided on an 'as is' basis. <?php echo e($siteName); ?> makes no warranties, expressed or implied, and hereby disclaims all warranties including, without limitation, implied warranties of merchantability and fitness for a particular purpose.</p>
            
            <h2>5. Limitations</h2>
            <p>In no event shall <?php echo e($siteName); ?> be liable for any damages arising out of the use or inability to use the materials on this website.</p>
            
            <h2>6. Links</h2>
            <p>This website may contain links to third-party websites. These links are provided for convenience only and do not signify endorsement of the content on those sites.</p>
            
            <h2>7. Modifications</h2>
            <p><?php echo e($siteName); ?> may revise these Terms of Service at any time without notice. By using this website, you agree to be bound by the current version of these terms.</p>
            
            <h2>8. Contact</h2>
            <p>If you have questions about these Terms, please <a href="<?php echo url('contact'); ?>">contact me</a>.</p>
        </div>
    </div>
</section>
