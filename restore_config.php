<?php
/**
 * Restore Original Configuration
 */

$config_file = __DIR__ . '/api/application/config/config.php';
$backup_files = glob($config_file . '.backup.*');

if ($backup_files) {
    // Get the most recent backup
    $latest_backup = end($backup_files);
    
    if (copy($latest_backup, $config_file)) {
        echo "✅ Configuration restored from backup: " . basename($latest_backup);
    } else {
        echo "❌ Failed to restore configuration";
    }
} else {
    echo "❌ No backup files found";
}
?>