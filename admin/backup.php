<?php
/**
 * Database Backup Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

// Download Database Backup SQL
if (isset($_GET['download']) && $_GET['download'] === 'sql') {
    $tables = $db->fetchAll("SHOW TABLES");
    $dbNameKey = "Tables_in_" . DB_NAME;
    
    $sqlDump = "-- Database Backup for " . DB_NAME . "\n";
    $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $tRow) {
        $tableName = $tRow[$dbNameKey] ?? reset($tRow);
        
        // Show create table
        $createRow = $db->fetch("SHOW CREATE TABLE `{$tableName}`");
        $sqlDump .= "-- Table: {$tableName}\n";
        $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        $sqlDump .= $createRow['Create Table'] . ";\n\n";

        // Table rows
        $rows = $db->fetchAll("SELECT * FROM `{$tableName}`");
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $values = array_map(function ($val) use ($db) {
                    if ($val === null) return 'NULL';
                    return $db->getPdo()->quote($val);
                }, array_values($row));
                $sqlDump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
            }
            $sqlDump .= "\n";
        }
    }
    
    $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="backup_' . DB_NAME . '_' . date('Y-m-d_His') . '.sql"');
    header('Content-Length: ' . strlen($sqlDump));
    echo $sqlDump;
    exit;
}
?>

<div class="mb-4">
    <h3 class="fw-bold">Database Backup Manager</h3>
    <p class="text-secondary">Export a complete SQL dump of all database tables and seed data.</p>
</div>

<div class="admin-card p-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="fw-bold mb-2"><i class="bi bi-database-down text-primary me-2"></i>Export Full Database Dump</h5>
            <p class="text-secondary mb-0">Generate a raw .sql file containing all 20+ tables schema and records for easy restoration or deployment.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?php echo SITE_URL; ?>admin/backup?download=sql" class="btn btn-primary btn-lg">
                <i class="bi bi-download me-1"></i> Download Backup (.sql)
            </a>
        </div>
    </div>
</div>
