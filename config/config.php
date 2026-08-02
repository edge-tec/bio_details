<?php
/**
 * ============================================
 * Dynamic Personal Biography Website
 * Configuration File
 * ============================================
 * 
 * Central configuration constants and settings.
 * All paths, URLs, and global settings are defined here.
 * 
 * @package PersonalBiography
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

// ============================================
// ENVIRONMENT
// ============================================

/** Debug mode - set to false in production */
define('DEBUG_MODE', false);

/** Error reporting based on environment */
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ============================================
// PATHS
// ============================================

/** Root directory path */
define('ROOT_PATH', dirname(__DIR__) . '/');

/** Config directory */
define('CONFIG_PATH', ROOT_PATH . 'config/');

/** Classes directory */
define('CLASSES_PATH', ROOT_PATH . 'classes/');

/** Functions directory */
define('FUNCTIONS_PATH', ROOT_PATH . 'functions/');

/** Templates directory */
define('TEMPLATES_PATH', ROOT_PATH . 'templates/');

/** Pages directory */
define('PAGES_PATH', ROOT_PATH . 'pages/');

/** Admin directory */
define('ADMIN_PATH', ROOT_PATH . 'admin/');

/** Assets directory */
define('ASSETS_PATH', ROOT_PATH . 'assets/');

/** Uploads directory */
define('UPLOADS_PATH', ROOT_PATH . 'assets/uploads/');

/** Logs directory */
define('LOGS_PATH', ROOT_PATH . 'logs/');

// ============================================
// SITE SETTINGS
// ============================================

/** Site URL - change this to your domain */
define('SITE_URL', 'http://localhost/about bio/');

/** Site name */
define('SITE_NAME', 'Dynamic Personal Biography');

/** Site description */
define('SITE_DESCRIPTION', 'Professional Personal Biography & Portfolio Website');

/** Admin email */
define('ADMIN_EMAIL', 'admin@example.com');

/** Default timezone */
date_default_timezone_set('Asia/Dhaka');

/** Default character encoding */
define('CHARSET', 'UTF-8');

// ============================================
// DATABASE SETTINGS
// ============================================

/** Database host */
define('DB_HOST', 'localhost');

/** Database name */
define('DB_NAME', 'biography_db');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASS', '');

/** Database charset */
define('DB_CHARSET', 'utf8mb4');

/** Database table prefix (optional) */
define('DB_PREFIX', '');

// ============================================
// SESSION SETTINGS
// ============================================

/** Session name */
define('SESSION_NAME', 'BIO_SESSION');

/** Session lifetime in seconds (2 hours) */
define('SESSION_LIFETIME', 7200);

/** CSRF token name */
define('CSRF_TOKEN_NAME', 'csrf_token');

// ============================================
// SECURITY SETTINGS
// ============================================

/** Max login attempts before lockout */
define('MAX_LOGIN_ATTEMPTS', 5);

/** Login lockout duration in seconds (15 minutes) */
define('LOGIN_LOCKOUT_DURATION', 900);

/** Password minimum length */
define('PASSWORD_MIN_LENGTH', 8);

// ============================================
// UPLOAD SETTINGS
// ============================================

/** Maximum upload file size in bytes (5MB) */
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

/** Allowed image types */
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);

/** Allowed image extensions */
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);

/** Allowed document types */
define('ALLOWED_DOC_TYPES', ['application/pdf']);

/** Allowed document extensions */
define('ALLOWED_DOC_EXTENSIONS', ['pdf']);

// ============================================
// PAGINATION
// ============================================

/** Default items per page */
define('ITEMS_PER_PAGE', 9);

/** Blog posts per page */
define('BLOG_PER_PAGE', 6);

/** Gallery items per page */
define('GALLERY_PER_PAGE', 12);

// ============================================
// CACHE SETTINGS
// ============================================

/** Enable page caching */
define('CACHE_ENABLED', false);

/** Cache lifetime in seconds (1 hour) */
define('CACHE_LIFETIME', 3600);

// ============================================
// API KEYS (optional)
// ============================================

/** Google Analytics tracking ID */
define('GA_TRACKING_ID', '');

/** Google Maps API key */
define('GOOGLE_MAPS_API_KEY', '');

/** Google Search Console verification */
define('GSC_VERIFICATION', '');

// ============================================
// ASSET VERSIONING
// ============================================

/** CSS/JS version for cache busting */
define('ASSET_VERSION', '1.0.0');
