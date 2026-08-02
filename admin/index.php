<?php
/**
 * Admin Panel Front Controller / Router
 * @package PersonalBiography
 */

define('APP_RUNNING', true);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/routes.php';

spl_autoload_register(function (string $className): void {
    $classFile = CLASSES_PATH . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

require_once FUNCTIONS_PATH . 'helpers.php';

Session::start();

$page = $_GET['page'] ?? 'dashboard';
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Handle logout directly
if ($page === 'logout') {
    $auth = new Auth();
    $auth->logout();
    Session::flash('success', 'You have been logged out.');
    redirect(SITE_URL . 'admin/login');
}

// Handle login page separately
if ($page === 'login') {
    if (Auth::isLoggedIn()) {
        redirect(SITE_URL . 'admin/dashboard');
    }
    require_once __DIR__ . '/login.php';
    exit;
}

// Require login for all other admin routes
Auth::requireLogin();

$db = Database::getInstance();
$adminFile = __DIR__ . '/' . $page . '.php';

if (!file_exists($adminFile)) {
    $page = 'dashboard';
    $adminFile = __DIR__ . '/dashboard.php';
}

$adminTitle = ucfirst(str_replace('_', ' ', $page)) . ' Manager';

// Execute admin page controller logic FIRST before sending any HTML headers
ob_start();
require_once $adminFile;
$adminContent = ob_get_clean();

// Now render admin layout cleanly
require_once TEMPLATES_PATH . 'admin/header.php';
echo $adminContent;
require_once TEMPLATES_PATH . 'admin/footer.php';
