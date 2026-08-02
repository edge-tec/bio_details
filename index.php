<?php
/**
 * ============================================
 * Dynamic Personal Biography Website
 * Front Controller / Router
 * ============================================
 * 
 * All requests are routed through this file via .htaccess.
 * It loads configuration, initializes services, and
 * dispatches to the appropriate page.
 * 
 * @package PersonalBiography
 * @version 1.0.0
 */

// Define application running constant
define('APP_RUNNING', true);

// ============================================
// LOAD CONFIGURATION
// ============================================

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/routes.php';

// ============================================
// AUTOLOAD CLASSES
// ============================================

spl_autoload_register(function (string $className): void {
    $classFile = CLASSES_PATH . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// ============================================
// LOAD HELPER FUNCTIONS
// ============================================

require_once FUNCTIONS_PATH . 'helpers.php';

// ============================================
// INITIALIZE SESSION
// ============================================

Session::start();

// ============================================
// DETERMINE CURRENT PAGE
// ============================================

$page = isset($_GET['page']) ? trim($_GET['page']) : 'home';
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Check if page exists in routes
if (!isset($routes[$page])) {
    $page = '404';
    http_response_code(404);
}

// ============================================
// LOAD SEO DATA
// ============================================

$seo = new SEO();
$seoData = $seo->getPageSeo($page);

// Set page title
$pageTitle = $seoData['meta_title'] ?? ($pageTitles[$page] ?? ucfirst($page)) . ' | ' . SITE_NAME;

// ============================================
// GET GLOBAL DATA
// ============================================

$db = Database::getInstance();

// Get site settings
$siteSettings = [];
try {
    $settingsResult = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
    foreach ($settingsResult as $setting) {
        $siteSettings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Settings table may not exist yet
    $siteSettings = [];
}

// Get social links
$socialLinks = [];
try {
    $socialLinks = $db->fetchAll(
        "SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order ASC"
    );
} catch (Exception $e) {
    $socialLinks = [];
}

// ============================================
// RENDER PAGE
// ============================================

$pageFile = PAGES_PATH . $routes[$page] . '.php';

if (!file_exists($pageFile)) {
    $page = '404';
    $pageFile = PAGES_PATH . '404.php';
    http_response_code(404);
}

// Include header template
require_once TEMPLATES_PATH . 'header.php';

// Include page content
require_once $pageFile;

// Include footer template
require_once TEMPLATES_PATH . 'footer.php';
