<?php
/**
 * ============================================
 * Authentication Class
 * ============================================
 * 
 * Handles admin authentication with bcrypt password
 * hashing, login rate limiting, and session management.
 * 
 * @package PersonalBiography
 */

class Auth
{
    /** @var Database Database instance */
    private Database $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Attempt to login with credentials
     *
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => ?array]
     */
    public function login(string $email, string $password): array
    {
        // Validate inputs
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return ['success' => false, 'message' => 'Invalid email address.', 'user' => null];
        }

        // Get user by email
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND is_active = 1",
            [$email]
        );

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password.', 'user' => null];
        }

        // Check if account is locked
        if ($this->isLocked($user)) {
            $remainingMinutes = $this->getLockoutRemaining($user);
            return [
                'success' => false,
                'message' => "Account is locked. Try again in {$remainingMinutes} minutes.",
                'user'    => null
            ];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->incrementLoginAttempts($user['id']);
            return ['success' => false, 'message' => 'Invalid email or password.', 'user' => null];
        }

        // Successful login
        $this->resetLoginAttempts($user['id']);
        $this->updateLastLogin($user['id']);
        $this->createSession($user);
        $this->logActivity($user['id'], 'login', 'User logged in successfully');

        return ['success' => true, 'message' => 'Login successful.', 'user' => $user];
    }

    /**
     * Logout the current user
     */
    public function logout(): void
    {
        $userId = Session::get('user_id');
        if ($userId) {
            $this->logActivity($userId, 'logout', 'User logged out');
        }
        Session::destroy();
    }

    /**
     * Check if user is currently logged in
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return Session::has('user_id') && Session::has('logged_in') && Session::get('logged_in') === true;
    }

    /**
     * Get current logged-in user data
     *
     * @return array|null
     */
    public static function getUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id'    => Session::get('user_id'),
            'name'  => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'role'  => Session::get('user_role'),
        ];
    }

    /**
     * Require authentication - redirect to login if not authenticated
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            Session::flash('error', 'Please login to access this page.');
            header('Location: ' . SITE_URL . 'admin/login.php');
            exit;
        }
    }

    /**
     * Change user password
     *
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.'];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->update('users', ['password' => $hashedPassword], 'id = ?', [$userId]);
        $this->logActivity($userId, 'password_change', 'Password changed successfully');

        return ['success' => true, 'message' => 'Password changed successfully.'];
    }

    /**
     * Hash a password
     *
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // ========================================
    // PRIVATE METHODS
    // ========================================

    /**
     * Create user session
     */
    private function createSession(array $user): void
    {
        session_regenerate_id(true);
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);
        Session::set('logged_in', true);
        Session::set('login_time', time());
    }

    /**
     * Check if account is locked due to too many failed attempts
     */
    private function isLocked(array $user): bool
    {
        if ($user['locked_until'] === null) {
            return false;
        }
        return strtotime($user['locked_until']) > time();
    }

    /**
     * Get remaining lockout time in minutes
     */
    private function getLockoutRemaining(array $user): int
    {
        if ($user['locked_until'] === null) {
            return 0;
        }
        $remaining = strtotime($user['locked_until']) - time();
        return max(1, (int) ceil($remaining / 60));
    }

    /**
     * Increment failed login attempts
     */
    private function incrementLoginAttempts(int $userId): void
    {
        $this->db->query(
            "UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?",
            [$userId]
        );

        // Check if should lock
        $user = $this->db->fetch("SELECT login_attempts FROM users WHERE id = ?", [$userId]);
        if ($user && $user['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', time() + LOGIN_LOCKOUT_DURATION);
            $this->db->update('users', ['locked_until' => $lockUntil], 'id = ?', [$userId]);
            $this->logActivity($userId, 'account_locked', 'Account locked due to too many failed login attempts');
        }
    }

    /**
     * Reset login attempts after successful login
     */
    private function resetLoginAttempts(int $userId): void
    {
        $this->db->update(
            'users',
            ['login_attempts' => 0, 'locked_until' => null],
            'id = ?',
            [$userId]
        );
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin(int $userId): void
    {
        $this->db->update(
            'users',
            ['last_login' => date('Y-m-d H:i:s')],
            'id = ?',
            [$userId]
        );
    }

    /**
     * Log user activity
     */
    private function logActivity(int $userId, string $action, string $details): void
    {
        try {
            $this->db->insert('activity_logs', [
                'user_id'    => $userId,
                'action'     => $action,
                'details'    => $details,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (Exception $e) {
            // Don't let logging failure break authentication
            error_log('Activity log error: ' . $e->getMessage());
        }
    }
}
