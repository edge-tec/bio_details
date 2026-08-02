<?php
/**
 * ============================================
 * Session Management Class
 * ============================================
 * 
 * Handles secure session management, CSRF token
 * generation/validation, and flash messages.
 * 
 * @package PersonalBiography
 */

class Session
{
    /**
     * Start session with secure settings
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            
            // Set HTTPS only cookie if using SSL
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', '1');
            }
            
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'httponly'  => true,
                'samesite'  => 'Lax',
            ]);
            
            session_start();
            
            // Regenerate session ID periodically (every 30 minutes)
            if (!isset($_SESSION['_last_regenerate'])) {
                $_SESSION['_last_regenerate'] = time();
            } elseif (time() - $_SESSION['_last_regenerate'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['_last_regenerate'] = time();
            }
        }
    }

    /**
     * Set a session value
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key
     *
     * @param string $key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session completely
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    /**
     * Generate CSRF token
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (!self::has(CSRF_TOKEN_NAME)) {
            self::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
        return self::get(CSRF_TOKEN_NAME);
    }

    /**
     * Get CSRF token input field HTML
     *
     * @return string
     */
    public static function csrfField(): string
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validate CSRF token
     *
     * @param string|null $token Token from form submission
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if ($token === null || !self::has(CSRF_TOKEN_NAME)) {
            return false;
        }
        
        $valid = hash_equals(self::get(CSRF_TOKEN_NAME), $token);
        
        // Regenerate token after validation
        self::remove(CSRF_TOKEN_NAME);
        
        return $valid;
    }

    /**
     * Set a flash message
     *
     * @param string $type Message type (success, error, warning, info)
     * @param string $message Message text
     */
    public static function flash(string $type, string $message): void
    {
        $flash = self::get('_flash', []);
        $flash[] = ['type' => $type, 'message' => $message];
        self::set('_flash', $flash);
    }

    /**
     * Get all flash messages and clear them
     *
     * @return array
     */
    public static function getFlash(): array
    {
        $flash = self::get('_flash', []);
        self::remove('_flash');
        return $flash;
    }

    /**
     * Check if there are flash messages
     *
     * @return bool
     */
    public static function hasFlash(): bool
    {
        $flash = self::get('_flash', []);
        return !empty($flash);
    }
}
