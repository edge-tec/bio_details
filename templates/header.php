<?php
/**
 * ============================================
 * Header Template
 * ============================================
 * 
 * Main header with HTML5 doctype, SEO meta tags,
 * navigation bar with glassmorphism design,
 * dark/light mode toggle, and responsive hamburger.
 * 
 * @package PersonalBiography
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

// Get profile data for schema
$profile = [];
try {
    $profile = $db->fetch("SELECT * FROM profile LIMIT 1") ?? [];
} catch (Exception $e) {
    $profile = [];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Page Title -->
    <title><?php echo e($pageTitle); ?></title>
    
    <!-- SEO Meta Tags -->
    <?php echo $seo->renderMetaTags($seoData, $page); ?>
    
    <!-- Favicon -->
    <?php if (!empty(getSetting('site_favicon'))): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo e(uploadUrl(getSetting('site_favicon'))); ?>">
    <?php else: ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">
    <?php endif; ?>
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YcnS/4tCnYmkGfGMTQ2ens3p5BhCGkqTEPsM" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    
    <!-- JSON-LD Schema -->
    <?php if ($page === 'home'): ?>
        <?php echo $seo->renderPersonSchema($profile, $socialLinks); ?>
        <?php echo $seo->renderWebsiteSchema(); ?>
    <?php endif; ?>
    
    <?php
    // Breadcrumb schema for non-home pages
    if ($page !== 'home' && $page !== '404'):
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('home')],
            ['name' => $pageTitles[$page] ?? ucfirst($page), 'url' => url($page)]
        ];
        echo $seo->renderBreadcrumbSchema($breadcrumbs);
    endif;
    ?>
    
    <!-- Google Analytics -->
    <?php if (!empty(getSetting('ga_tracking_id'))): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(getSetting('ga_tracking_id')); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e(getSetting('ga_tracking_id')); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Google Search Console Verification -->
    <?php if (!empty(getSetting('gsc_verification'))): ?>
    <meta name="google-site-verification" content="<?php echo e(getSetting('gsc_verification')); ?>">
    <?php endif; ?>
</head>
<body>
    <!-- ============================================ -->
    <!-- LOADING SCREEN                               -->
    <!-- ============================================ -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <span class="loading-text"><?php echo e($profile['full_name'] ?? SITE_NAME); ?></span>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- NAVIGATION BAR                               -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg fixed-top glass-nav" id="mainNav">
        <div class="container">
            <!-- Brand / Logo -->
            <a class="navbar-brand brand-logo" href="<?php echo url('home'); ?>">
                <?php if (!empty(getSetting('site_logo'))): ?>
                    <img src="<?php echo e(uploadUrl(getSetting('site_logo'))); ?>" alt="<?php echo e(SITE_NAME); ?>" height="40">
                <?php else: ?>
                    <span class="brand-text">
                        <span class="brand-accent">&lt;</span><?php echo e($profile['full_name'] ?? 'JD'); ?><span class="brand-accent">/&gt;</span>
                    </span>
                <?php endif; ?>
            </a>
            
            <!-- Dark/Light Mode Toggle (visible on mobile too) -->
            <div class="d-flex align-items-center order-lg-last ms-lg-3">
                <button type="button" class="btn btn-theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-fill theme-icon-dark"></i>
                </button>
            </div>
            
            <!-- Hamburger Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <?php foreach ($navMenu as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo activeClass($item['slug']); ?>" href="<?php echo url($item['slug']); ?>">
                            <i class="<?php echo e($item['icon']); ?> d-lg-none me-2"></i>
                            <?php echo e($item['label']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php $flashHtml = renderFlashMessages(); ?>
    <?php if (!empty($flashHtml)): ?>
    <div class="container mt-5 pt-5">
        <?php echo $flashHtml; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <main id="main-content">
