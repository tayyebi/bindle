<?php

require_once __DIR__ . '/../bootstrap.php';

$db = App\Core\App::db();

$migration = file_get_contents(__DIR__ . '/001_initial.sql');
$statements = explode(';', $migration);

$db->pdo()->exec('BEGIN');
try {
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            $db->pdo()->exec($stmt);
        }
    }
    $db->pdo()->exec('COMMIT');
    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    $db->pdo()->exec('ROLLBACK');
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
