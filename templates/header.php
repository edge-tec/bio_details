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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
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
    <script>
        (function() {
            var loader = document.getElementById('loading-screen');
            if (loader) {
                var dismiss = function() {
                    loader.style.transition = 'opacity 0.3s ease';
                    loader.style.opacity = '0';
                    setTimeout(function() { loader.style.display = 'none'; }, 300);
                };
                window.addEventListener('load', dismiss);
                setTimeout(dismiss, 300);
            }
        })();
    </script>

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

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <?php foreach ($navMenu as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo activeClass($item['slug']); ?>" href="<?php echo url($item['slug']); ?>">
                                <i class="bi <?php echo $item['icon']; ?> me-1"></i>
                                <?php echo e($item['label']); ?>

                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Theme Toggle Button -->
                <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
                    <button class="btn btn-icon theme-toggle-btn" id="themeToggle" type="button" aria-label="Toggle Dark/Light Mode" title="Toggle Dark/Light Mode">
                        <i class="bi bi-sun-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="main-content">
