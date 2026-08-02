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

/**
 * Calculate pagination offsets and metadata.
 *
 * @param int $totalItems  Total number of records
 * @param int $perPage     Items per page
 * @param int $currentPage Current page number (1-indexed)
 * @return array Pagination data array
 */
function paginate(int $totalItems, int $perPage = 9, int $currentPage = 1): array {
    $perPage     = max(1, $perPage);
    $totalPages  = max(1, (int)ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset      = ($currentPage - 1) * $perPage;

    return [
        'total_items'  => $totalItems,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
        'has_prev'     => $currentPage > 1,
        'has_next'     => $currentPage < $totalPages,
        'prev_page'    => max(1, $currentPage - 1),
        'next_page'    => min($totalPages, $currentPage + 1),
    ];
}

/**
 * Send HTML Email notification
 */
function sendEmail(string $to, string $subject, string $messageBody, string $fromEmail = '', string $fromName = ''): bool {
    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $siteName  = getSetting('site_name', SITE_NAME);
    $fromEmail = (!empty($fromEmail) && filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) ? $fromEmail : (getSetting('contact_email') ?: 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'eliteali.com'));
    $fromName  = !empty($fromName) ? $fromName : $siteName;

    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>" . "\r\n";
    $headers .= "Reply-To: {$fromEmail}" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    return @mail($to, $subject, $messageBody, $headers);
}

/**
 * Send contact form message notification to admin
 */
function sendContactNotification(array $contactData): bool {
    // Admin recipient email (from Settings table, or profile email, or config ADMIN_EMAIL)
    $toEmail = getSetting('contact_email');
    if (empty($toEmail)) {
        try {
            $db = Database::getInstance();
            $prof = $db->fetch("SELECT email FROM profile LIMIT 1");
            $toEmail = $prof['email'] ?? '';
        } catch (Exception $e) {}
    }
    if (empty($toEmail)) {
        $toEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '';
    }

    if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $name     = e($contactData['name'] ?? 'Visitor');
    $email    = e($contactData['email'] ?? 'No email');
    $phone    = e($contactData['phone'] ?? 'N/A');
    $subject  = e($contactData['subject'] ?? 'New Contact Inquiry');
    $message  = nl2br(e($contactData['message'] ?? ''));
    $ip       = e($contactData['ip_address'] ?? getClientIp());
    $siteName = getSetting('site_name', SITE_NAME);

    $emailSubject = "📩 New Contact Message from {$name} - {$siteName}";

    $htmlBody = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
            .email-card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
            .header { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: #ffffff; padding: 25px 30px; text-align: center; }
            .header h2 { margin: 0; font-size: 22px; font-weight: 700; }
            .content { padding: 30px; }
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .info-table td { padding: 10px 12px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
            .info-table td.label { font-weight: 600; color: #64748b; width: 130px; }
            .message-box { background: #f8fafc; border-left: 4px solid #2563eb; padding: 18px; border-radius: 6px; font-size: 15px; line-height: 1.6; color: #1e293b; margin-top: 15px; }
            .footer { background: #f8fafc; padding: 15px 30px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #edf2f7; }
        </style>
    </head>
    <body>
        <div class='email-card'>
            <div class='header'>
                <h2>📬 New Contact Message Received</h2>
            </div>
            <div class='content'>
                <p>You have received a new contact message from your website <strong>{$siteName}</strong>.</p>
                <table class='info-table'>
                    <tr><td class='label'>Sender Name:</td><td><strong>{$name}</strong></td></tr>
                    <tr><td class='label'>Email Address:</td><td><a href='mailto:{$email}' style='color: #2563eb;'>{$email}</a></td></tr>
                    <tr><td class='label'>Phone Number:</td><td>{$phone}</td></tr>
                    <tr><td class='label'>Subject:</td><td>{$subject}</td></tr>
                    <tr><td class='label'>IP Address:</td><td>{$ip}</td></tr>
                </table>
                <div style='font-weight: 600; color: #475569; margin-top: 20px;'>Message Content:</div>
                <div class='message-box'>{$message}</div>
            </div>
            <div class='footer'>
                This notification was automatically sent from your website <strong>{$siteName}</strong>.
            </div>
        </div>
    </body>
    </html>
    ";

    return sendEmail($toEmail, $emailSubject, $htmlBody, $email, $name);
}
