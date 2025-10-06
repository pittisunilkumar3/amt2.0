<?php
/**
 * Check Apache Modules
 */

echo "<h3>Apache Module Check</h3>";

// Check if mod_rewrite is enabled
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $rewrite_enabled = in_array('mod_rewrite', $modules);
    
    echo "✅ Apache modules function available<br>";
    echo "🔍 mod_rewrite: " . ($rewrite_enabled ? "✅ Enabled" : "❌ Disabled") . "<br>";
    
    $important_modules = ['mod_rewrite', 'mod_ssl', 'mod_headers'];
    foreach ($important_modules as $module) {
        $status = in_array($module, $modules) ? "✅ Enabled" : "❌ Disabled";
        echo "🔍 $module: $status<br>";
    }
} else {
    echo "⚠️ apache_get_modules() not available (might be running on different server)<br>";
}

// Check .htaccess functionality
echo "<h4>Testing .htaccess functionality:</h4>";

$htaccess_path = __DIR__ . '/api/.htaccess';
if (file_exists($htaccess_path)) {
    echo "✅ .htaccess file exists<br>";
    $htaccess_content = file_get_contents($htaccess_path);
    echo "📋 .htaccess content:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($htaccess_content) . "</pre>";
} else {
    echo "❌ .htaccess file missing<br>";
}

// Test URL rewriting
echo "<h4>Testing URL rewriting:</h4>";
$test_urls = [
    'http://localhost/amt/api/index.php' => 'Direct index.php access',
    'http://localhost/amt/api/' => 'Directory access (should redirect to index.php)',
];

foreach ($test_urls as $url => $description) {
    echo "<strong>$description:</strong> ";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        echo "✅ Working (HTTP $http_code)<br>";
    } else {
        echo "❌ Error (HTTP $http_code)<br>";
    }
}

// Check PHP configuration
echo "<h4>PHP Configuration:</h4>";
$php_settings = [
    'display_errors' => ini_get('display_errors'),
    'log_errors' => ini_get('log_errors'),
    'error_reporting' => error_reporting(),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
];

foreach ($php_settings as $setting => $value) {
    echo "🔍 $setting: $value<br>";
}

echo "<h4>CodeIgniter Environment Check:</h4>";
$ci_index = __DIR__ . '/api/index.php';
if (file_exists($ci_index)) {
    $ci_content = file_get_contents($ci_index);
    if (preg_match("/define\('ENVIRONMENT',\s*['\"]([^'\"]*)['\"].*\);/", $ci_content, $matches)) {
        echo "🔍 CodeIgniter Environment: " . $matches[1] . "<br>";
    } else {
        echo "⚠️ Could not determine CodeIgniter environment<br>";
    }
} else {
    echo "❌ CodeIgniter index.php not found<br>";
}
?>