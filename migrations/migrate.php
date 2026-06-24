<?php

require_once __DIR__ . '/../bootstrap.php';

$db = App\Core\App::db();

$files = glob(__DIR__ . '/*.sql');
sort($files);

$statements = [];
foreach ($files as $file) {
    $sql = file_get_contents($file);
    $parts = explode(';', $sql);
    foreach ($parts as $part) {
        $part = trim($part);
        if (!empty($part)) {
            $statements[] = $part;
        }
    }
}

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
