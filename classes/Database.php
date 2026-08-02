<?php
/**
 * ============================================
 * Database Class
 * ============================================
 * 
 * PDO wrapper with singleton pattern providing
 * convenient methods for database operations.
 * All queries use prepared statements.
 * 
 * @package PersonalBiography
 */

class Database
{
    /** @var Database|null Singleton instance */
    private static ?Database $instance = null;

    /** @var PDO Database connection */
    private PDO $pdo;

    /**
     * Private constructor - use getInstance()
     */
    private function __construct()
    {
        $this->pdo = getDatabaseConnection();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Get singleton instance
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get raw PDO connection
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared query
     *
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters to bind
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError($e, $sql);
            throw $e;
        }
    }

    /**
     * Fetch a single row
     *
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array|null
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows
     *
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return array
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single column value
     *
     * @param string $sql SQL query
     * @param array $params Parameters
     * @return mixed
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Insert a record
     *
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @return int Last insert ID
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update records
     *
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @param string $where WHERE clause with placeholders
     * @param array $whereParams Parameters for WHERE clause
     * @return int Number of affected rows
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClauses = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        
        $sql = "UPDATE `{$table}` SET {$setClauses} WHERE {$where}";
        $params = array_merge(array_values($data), $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete records
     *
     * @param string $table Table name
     * @param string $where WHERE clause with placeholders
     * @param array $params Parameters for WHERE clause
     * @return int Number of affected rows
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Get row count
     *
     * @param string $table Table name
     * @param string $where Optional WHERE clause
     * @param array $params Optional parameters
     * @return int
     */
    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where}";
        return (int) $this->fetchColumn($sql, $params);
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Check if a record exists
     *
     * @param string $table Table name
     * @param string $where WHERE clause
     * @param array $params Parameters
     * @return bool
     */
    public function exists(string $table, string $where, array $params = []): bool
    {
        return $this->count($table, $where, $params) > 0;
    }

    /**
     * Log database errors
     *
     * @param PDOException $e Exception
     * @param string $sql SQL query that failed
     */
    private function logError(PDOException $e, string $sql): void
    {
        $logFile = LOGS_PATH . 'db_errors.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $message = sprintf(
            "[%s] SQL Error: %s | Query: %s | File: %s:%d\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $sql,
            $e->getFile(),
            $e->getLine()
        );
        
        error_log($message, 3, $logFile);
    }
}
