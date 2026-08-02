<?php
/**
 * Global Helper Functions
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

/** Escape output safely */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, CHARSET, false);
}

/** Generate site URL */
function url(string $path = ''): string {
    return SITE_URL . ltrim($path, '/');
}

/** Generate asset URL */
function asset(string $path): string {
    return SITE_URL . 'assets/' . ltrim($path, '/') . '?v=' . ASSET_VERSION;
}

/** Upload URL */
function uploadUrl(?string $path): string {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) return $path;
    return SITE_URL . 'assets/uploads/' . ltrim($path, '/');
}

/** Redirect helper with headers_sent fallback */
function redirect(string $url): void {
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url) . '"></noscript>';
    exit;
}

/** Slugify text */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text ?: 'n-a');
}

/** Truncate text with ellipsis */
function truncate(string $text, int $length = 100, string $append = '...'): string {
    $cleanText = strip_tags($text);
    if (mb_strlen($cleanText) <= $length) return $cleanText;
    return mb_substr($cleanText, 0, $length) . $append;
}

/** Format date */
function formatDate(?string $date, string $format = 'M d, Y'): string {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/** Time ago helper */
function timeAgo(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' mins ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
    return date('M d, Y', $time);
}

/** Format numbers (e.g. 1.5k) */
function formatNumber(int $number): string {
    if ($number >= 1000000) return round($number / 1000000, 1) . 'M';
    if ($number >= 1000) return round($number / 1000, 1) . 'K';
    return (string)$number;
}

/** Active nav class */
function activeClass(string $pageSlug): string {
    $currentPage = $_GET['page'] ?? 'home';
    return $currentPage === $pageSlug ? 'active' : '';
}

/** Calculate reading time in minutes */
function readingTime(string $content): int {
    $wordCount = str_word_count(strip_tags($content));
    return max(1, (int)ceil($wordCount / 200));
}

/** Render flash messages */
function renderFlashMessages(): string {
    $messages = Session::getFlash();
    if (empty($messages)) return '';
    
    $html = '';
    foreach ($messages as $msg) {
        $typeClass = $msg['type'] === 'error' ? 'danger' : $msg['type'];
        $iconClass = $msg['type'] === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill';
        
        $html .= '<div class="alert alert-' . $typeClass . ' alert-dismissible fade show" role="alert">';
        $html .= '<i class="' . $iconClass . ' me-2"></i>' . e($msg['message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
    }
    return $html;
}

/** Star rating HTML */
function starRating(int $rating): string {
    $rating = min(5, max(1, $rating));
    $html = '<div class="star-rating text-warning">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="bi bi-star-fill"></i>';
        } else {
            $html .= '<i class="bi bi-star"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}

/** Get client IP address */
function getClientIp(): string {
    return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Rate limiter helper */
function isRateLimited(string $key, int $maxAttempts = 5, int $decaySeconds = 60): bool {
    $sessionKey = 'rate_limit_' . $key;
    $now = time();
    $data = $_SESSION[$sessionKey] ?? ['attempts' => 0, 'reset' => $now + $decaySeconds];
    
    if ($now > $data['reset']) {
        $data = ['attempts' => 1, 'reset' => $now + $decaySeconds];
    } else {
        $data['attempts']++;
    }
    
    $_SESSION[$sessionKey] = $data;
    return $data['attempts'] > $maxAttempts;
}

/** Get setting from database */
function getSetting(string $key, string $default = ''): string {
    static $settingsCache = null;
    if ($settingsCache === null) {
        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
            $settingsCache = array_column($rows, 'setting_value', 'setting_key');
        } catch (Exception $e) {
            $settingsCache = [];
        }
    }
    return $settingsCache[$key] ?? $default;
}
