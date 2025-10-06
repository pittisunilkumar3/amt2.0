<?php
/**
 * Enable CodeIgniter Debugging
 */

$config_file = __DIR__ . '/api/application/config/config.php';

if (file_exists($config_file)) {
    $content = file_get_contents($config_file);
    
    // Enable debugging and logging
    $changes = [
        "define('ENVIRONMENT', isset(\$_SERVER['CI_ENV']) ? \$_SERVER['CI_ENV'] : 'development');" => "define('ENVIRONMENT', 'development');",
        "\$config['log_threshold'] = 0;" => "\$config['log_threshold'] = 4;",
    ];
    
    $updated = false;
    foreach ($changes as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $updated = true;
        }
    }
    
    // Also check index.php for environment
    $index_file = __DIR__ . '/api/index.php';
    if (file_exists($index_file)) {
        $index_content = file_get_contents($index_file);
        if (strpos($index_content, "define('ENVIRONMENT', isset(\$_SERVER['CI_ENV']) ? \$_SERVER['CI_ENV'] : 'production');") !== false) {
            $index_content = str_replace(
                "define('ENVIRONMENT', isset(\$_SERVER['CI_ENV']) ? \$_SERVER['CI_ENV'] : 'production');",
                "define('ENVIRONMENT', 'development');",
                $index_content
            );
            file_put_contents($index_file, $index_content);
            echo "✅ Environment set to development in index.php<br>";
        }
    }
    
    if ($updated) {
        file_put_contents($config_file, $content);
        echo "✅ Debugging enabled in config.php<br>";
    }
    
    echo "✅ CodeIgniter debugging has been enabled<br>";
    echo "📋 Error logging level set to maximum<br>";
    echo "🔄 Try your API request again to see detailed errors<br>";
    
} else {
    echo "❌ Config file not found";
}
?>