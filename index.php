<?php
/**
 * Front Controller / Router
 * @package PersonalBiography
 */

// Enable output buffering
ob_start();

define('APP_RUNNING', true);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/routes.php';

spl_autoload_register(function (string $className): void {
    $classFile = CLASSES_PATH . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

require_once FUNCTIONS_PATH . 'helpers.php';

Session::start();

$db = Database::getInstance();
$seo = new SEO();

$page = $_GET['page'] ?? 'home';
$page = trim($page, '/');
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Fallback for root index requests or indexphp rewrites
if (empty($page) || $page === 'index' || $page === 'indexphp') {
    $page = 'home';
}

if (!isset($routes[$page])) {
    $page = '404';
    http_response_code(404);
}

$pageFile = PAGES_PATH . $routes[$page];

if (!file_exists($pageFile)) {
    $page = '404';
    $pageFile = PAGES_PATH . '404.php';
    http_response_code(404);
}

$pageTitles = [
    'home'         => 'Home - ' . SITE_NAME,
    'about'        => 'About Me - ' . SITE_NAME,
    'skills'       => 'My Skills - ' . SITE_NAME,
    'experience'   => 'Work Experience - ' . SITE_NAME,
    'education'    => 'Education - ' . SITE_NAME,
    'portfolio'    => 'Portfolio & Projects - ' . SITE_NAME,
    'projects'     => 'Portfolio & Projects - ' . SITE_NAME,
    'services'     => 'My Services - ' . SITE_NAME,
    'achievements' => 'Achievements - ' . SITE_NAME,
    'certificates' => 'Certificates - ' . SITE_NAME,
    'resume'       => 'Resume / CV - ' . SITE_NAME,
    'blog'         => 'Blog & Articles - ' . SITE_NAME,
    'blog-single'  => 'Article - ' . SITE_NAME,
    'gallery'      => 'Photo Gallery - ' . SITE_NAME,
    'testimonials' => 'Testimonials - ' . SITE_NAME,
    'contact'      => 'Contact Me - ' . SITE_NAME,
    'privacy'      => 'Privacy Policy - ' . SITE_NAME,
    'terms'        => 'Terms of Service - ' . SITE_NAME,
    '404'          => 'Page Not Found - ' . SITE_NAME,
];

$pageTitle = $pageTitles[$page] ?? ucfirst($page) . ' - ' . SITE_NAME;
$seoData = $seo->getPageSEO($page);
$profile = $db->fetch("SELECT * FROM profile LIMIT 1") ?: [];
$socialLinks = $db->fetchAll("SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order ASC") ?: [];

$navMenu = [
    ['slug' => 'home', 'label' => 'Home', 'icon' => 'bi-house-door'],
    ['slug' => 'about', 'label' => 'About', 'icon' => 'bi-person'],
    ['slug' => 'skills', 'label' => 'Skills', 'icon' => 'bi-gear'],
    ['slug' => 'experience', 'label' => 'Experience', 'icon' => 'bi-briefcase'],
    ['slug' => 'education', 'label' => 'Education', 'icon' => 'bi-mortarboard'],
    ['slug' => 'portfolio', 'label' => 'Portfolio', 'icon' => 'bi-collection'],
    ['slug' => 'services', 'label' => 'Services', 'icon' => 'bi-tools'],
    ['slug' => 'blog', 'label' => 'Blog', 'icon' => 'bi-journal-text'],
    ['slug' => 'gallery', 'label' => 'Gallery', 'icon' => 'bi-images'],
    ['slug' => 'testimonials', 'label' => 'Testimonials', 'icon' => 'bi-chat-quote'],
    ['slug' => 'contact', 'label' => 'Contact', 'icon' => 'bi-envelope'],
];

require_once TEMPLATES_PATH . 'header.php';
require_once $pageFile;
require_once TEMPLATES_PATH . 'footer.php';

// Flush output buffer
ob_end_flush();
