<?php
/**
 * Privacy Policy Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }
$siteName = $profile['full_name'] ?? SITE_NAME;
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Privacy <span class="gradient-text">Policy</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Privacy Policy</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="legal-content" data-aos="fade-up">
            <p><strong>Last Updated:</strong> <?php echo date('F d, Y'); ?></p>
            
            <h2>1. Introduction</h2>
            <p>Welcome to <?php echo e($siteName); ?>'s website. This Privacy Policy explains how I collect, use, disclose, and safeguard your information when you visit my website.</p>
            
            <h2>2. Information I Collect</h2>
            <p>I may collect information about you in a variety of ways:</p>
            <ul>
                <li><strong>Personal Data:</strong> Name, email address, phone number, and other contact information you voluntarily provide through contact forms.</li>
                <li><strong>Usage Data:</strong> Information about how you use the website, including your IP address, browser type, operating system, referring URLs, and pages viewed.</li>
                <li><strong>Cookies:</strong> Small data files stored on your device that help improve your browsing experience.</li>
            </ul>
            
            <h2>3. How I Use Your Information</h2>
            <p>I use the information I collect to:</p>
            <ul>
                <li>Respond to your inquiries and contact requests</li>
                <li>Improve website functionality and user experience</li>
                <li>Monitor and analyze usage patterns</li>
                <li>Send newsletters (only if you have opted in)</li>
                <li>Prevent fraud and maintain security</li>
            </ul>
            
            <h2>4. Cookies</h2>
            <p>This website uses cookies to enhance your experience. You can choose to accept or decline cookies. Most web browsers automatically accept cookies, but you can modify your browser settings to decline cookies if you prefer.</p>
            
            <h2>5. Third-Party Services</h2>
            <p>I may use third-party services such as Google Analytics to monitor and analyze the use of this website. These services may collect information sent by your browser as part of a web page request.</p>
            
            <h2>6. Data Security</h2>
            <p>I implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet or electronic storage is 100% secure.</p>
            
            <h2>7. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access, update, or delete your personal information</li>
                <li>Opt out of marketing communications</li>
                <li>Request a copy of your data</li>
                <li>Withdraw consent at any time</li>
            </ul>
            
            <h2>8. Contact</h2>
            <p>If you have questions about this Privacy Policy, please <a href="<?php echo url('contact'); ?>">contact me</a>.</p>
        </div>
    </div>
</section>
