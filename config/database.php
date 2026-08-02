<?php
/**
 * ============================================
 * Database Connection Configuration
 * ============================================
 * 
 * PDO MySQL connection setup with error handling.
 * This file initializes the database connection
 * used throughout the application.
 * 
 * @package PersonalBiography
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

/**
 * Get PDO database connection instance
 * 
 * @return PDO|null
 */
function getDatabaseConnection(): ?PDO
{
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error
            $logFile = LOGS_PATH . 'db_errors.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            error_log(
                date('[Y-m-d H:i:s]') . ' Database Connection Error: ' . $e->getMessage() . PHP_EOL,
                3,
                $logFile
            );
            
            if (DEBUG_MODE) {
                die('Database Connection Error: ' . $e->getMessage());
            } else {
                die('A database error occurred. Please try again later.');
            }
        }
    }
    
    return $pdo;
}
