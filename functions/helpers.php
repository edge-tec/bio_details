<?php
/**
 * ============================================
 * Helper Functions
 * ============================================
 * 
 * Utility functions used throughout the application.
 * 
 * @package PersonalBiography
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

/**
 * Escape output for safe HTML display
 * Shorthand for htmlspecialchars
 *
 * @param string|null $value
 * @return string
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 *
 * @param string $url
 * @param int $statusCode HTTP status code
 */
function redirect(string $url, int $statusCode = 302): void
{
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Generate asset URL with cache busting version
 *
 * @param string $path Relative path from assets directory
 * @return string Full URL to asset
 */
function asset(string $path): string
{
    return SITE_URL . 'assets/' . ltrim($path, '/') . '?v=' . ASSET_VERSION;
}

/**
 * Generate full URL for a page
 *
 * @param string $page Page slug
 * @return string Full URL
 */
function url(string $page = ''): string
{
    if (empty($page) || $page === 'home') {
        return rtrim(SITE_URL, '/') . '/';
    }
    return rtrim(SITE_URL, '/') . '/' . ltrim($page, '/');
}

/**
 * Generate URL for uploaded file
 *
 * @param string|null $filename
 * @param string $default Default image path
 * @return string
 */
function uploadUrl(?string $filename, string $default = 'images/placeholder.svg'): string
{
    if ($filename && file_exists(UPLOADS_PATH . $filename)) {
        return SITE_URL . 'assets/uploads/' . $filename;
    }
    return SITE_URL . 'assets/' . $default;
}

/**
 * Get "time ago" string from a date
 *
 * @param string $datetime
 * @return string
 */
function timeAgo(string $datetime): string
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Just now';
    }
    if ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }
    $years = floor($diff / 31536000);
    return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
}

/**
 * Calculate reading time for text
 *
 * @param string $text
 * @param int $wordsPerMinute
 * @return int Minutes
 */
function readingTime(string $text, int $wordsPerMinute = 200): int
{
    $wordCount = str_word_count(strip_tags($text));
    return max(1, (int) ceil($wordCount / $wordsPerMinute));
}

/**
 * Generate slug from text
 *
 * @param string $text
 * @return string
 */
function slugify(string $text): string
{
    return SEO::slugify($text);
}

/**
 * Truncate text to a certain length
 *
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate(string $text, int $length = 150, string $suffix = '...'): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Format date for display
 *
 * @param string|null $date
 * @param string $format
 * @return string
 */
function formatDate(?string $date, string $format = 'M d, Y'): string
{
    if (empty($date)) {
        return '';
    }
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : '';
}

/**
 * Get current page slug
 *
 * @return string
 */
function currentPage(): string
{
    return isset($_GET['page']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['page']) : 'home';
}

/**
 * Check if current page matches
 *
 * @param string $page
 * @return bool
 */
function isPage(string $page): bool
{
    return currentPage() === $page;
}

/**
 * Get active class for navigation
 *
 * @param string $page
 * @return string
 */
function activeClass(string $page): string
{
    return isPage($page) ? 'active' : '';
}

/**
 * Render flash messages as Bootstrap alerts
 *
 * @return string HTML
 */
function renderFlashMessages(): string
{
    $messages = Session::getFlash();
    if (empty($messages)) {
        return '';
    }

    $html = '';
    foreach ($messages as $msg) {
        $type = match ($msg['type']) {
            'error'   => 'danger',
            'success' => 'success',
            'warning' => 'warning',
            'info'    => 'info',
            default   => 'info',
        };
        $html .= '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        $html .= e($msg['message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
    }
    return $html;
}

/**
 * Get setting value from settings array
 *
 * @param string $key Setting key
 * @param string $default Default value
 * @return string
 */
function getSetting(string $key, string $default = ''): string
{
    global $siteSettings;
    return $siteSettings[$key] ?? $default;
}

/**
 * Format number with suffix (1.5k, 2.3M)
 *
 * @param int $number
 * @return string
 */
function formatNumber(int $number): string
{
    if ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M';
    }
    if ($number >= 1000) {
        return number_format($number / 1000, 1) . 'K';
    }
    return (string) $number;
}

/**
 * Generate star rating HTML
 *
 * @param int $rating 1-5
 * @return string HTML
 */
function starRating(int $rating): string
{
    $html = '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="bi bi-star-fill text-warning"></i>';
        } else {
            $html .= '<i class="bi bi-star text-warning"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}

/**
 * Get pagination data
 *
 * @param int $totalItems Total number of items
 * @param int $perPage Items per page
 * @param int $currentPage Current page number
 * @return array Pagination data
 */
function paginate(int $totalItems, int $perPage, int $currentPage = 1): array
{
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total'        => $totalItems,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
        'has_prev'     => $currentPage > 1,
        'has_next'     => $currentPage < $totalPages,
    ];
}

/**
 * Render pagination HTML
 *
 * @param array $pagination Pagination data from paginate()
 * @param string $baseUrl Base URL for page links
 * @return string HTML
 */
function renderPagination(array $pagination, string $baseUrl): string
{
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    // Previous
    if ($pagination['has_prev']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . '?p=' . ($pagination['current_page'] - 1)) . '"><i class="bi bi-chevron-left"></i></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-left"></i></span></li>';
    }

    // Page numbers
    $start = max(1, $pagination['current_page'] - 2);
    $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . '?p=1') . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $pagination['current_page'] ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . e($baseUrl . '?p=' . $i) . '">' . $i . '</a></li>';
    }

    if ($end < $pagination['total_pages']) {
        if ($end < $pagination['total_pages'] - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . '?p=' . $pagination['total_pages']) . '">' . $pagination['total_pages'] . '</a></li>';
    }

    // Next
    if ($pagination['has_next']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl . '?p=' . ($pagination['current_page'] + 1)) . '"><i class="bi bi-chevron-right"></i></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link"><i class="bi bi-chevron-right"></i></span></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Get client IP address
 *
 * @return string
 */
function getClientIp(): string
{
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = explode(',', $_SERVER[$header])[0];
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

/**
 * Simple rate limiter using session
 *
 * @param string $key Rate limit key
 * @param int $maxAttempts Maximum attempts
 * @param int $decaySeconds Time window
 * @return bool True if rate limited (blocked)
 */
function isRateLimited(string $key, int $maxAttempts = 5, int $decaySeconds = 60): bool
{
    $rateLimitKey = '_rate_limit_' . $key;
    $attempts = Session::get($rateLimitKey, []);

    // Remove expired attempts
    $now = time();
    $attempts = array_filter($attempts, fn($ts) => ($now - $ts) < $decaySeconds);

    if (count($attempts) >= $maxAttempts) {
        return true;
    }

    // Record this attempt
    $attempts[] = $now;
    Session::set($rateLimitKey, $attempts);
    return false;
}
