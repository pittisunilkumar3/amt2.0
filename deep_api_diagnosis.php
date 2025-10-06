<?php
/**
 * Deep API Diagnosis - Internal Server Error Investigation
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>🔍 Deep API Diagnosis</h1>";
echo "<p><strong>Investigating Internal Server Error for Menu API</strong></p>";

// 1. Basic Environment Check
echo "<h2>1. 🌐 Environment Check</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "✅ PHP Version: " . phpversion() . "<br>";
echo "✅ Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "✅ Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "✅ Current Script: " . __FILE__ . "<br>";
echo "</div>";

// 2. File Structure Check
echo "<h2>2. 📁 File Structure Check</h2>";
$critical_files = [
    '/api/index.php' => 'CodeIgniter Entry Point',
    '/api/application/config/config.php' => 'Main Config',
    '/api/application/config/database.php' => 'Database Config',
    '/api/application/config/routes.php' => 'Routes Config',
    '/api/application/controllers/Teacher_webservice.php' => 'API Controller',
    '/api/application/models/Teacher_permission_model.php' => 'Permission Model',
    '/api/.htaccess' => 'URL Rewriting'
];

foreach ($critical_files as $file => $description) {
    $full_path = __DIR__ . $file;
    $status = file_exists($full_path) ? '✅' : '❌';
    $size = file_exists($full_path) ? ' (' . filesize($full_path) . ' bytes)' : '';
    echo "<div>$status <strong>$description:</strong> $file$size</div>";
}

// 3. Database Connection Test
echo "<h2>3. 🗄️ Database Connection Test</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div style='color: green;'>✅ Database connection successful</div>";
    
    // Test critical tables
    $tables = ['staff', 'roles', 'staff_roles', 'sidebar_menus', 'sidebar_sub_menus'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table LIMIT 1");
            $count = $stmt->fetch()['count'];
            echo "<div>✅ Table '$table': $count records</div>";
        } catch (Exception $e) {
            echo "<div style='color: red;'>❌ Table '$table': " . $e->getMessage() . "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</div>";
}

// 4. CodeIgniter Bootstrap Test
echo "<h2>4. 🚀 CodeIgniter Bootstrap Test</h2>";
try {
    // Test if we can include CodeIgniter files
    $ci_system_path = __DIR__ . '/api/system/core/CodeIgniter.php';
    if (file_exists($ci_system_path)) {
        echo "✅ CodeIgniter system files found<br>";
    } else {
        echo "❌ CodeIgniter system files missing<br>";
    }
    
    // Check application path
    $app_path = __DIR__ . '/api/application';
    if (is_dir($app_path)) {
        echo "✅ Application directory exists<br>";
    } else {
        echo "❌ Application directory missing<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ CodeIgniter test failed: " . $e->getMessage() . "</div>";
}

// 5. Test Direct API Access
echo "<h2>5. 🔗 Direct API Access Test</h2>";

// Test different URL patterns
$test_urls = [
    'http://localhost/amt/api/index.php/teacher/menu',
    'http://localhost/amt/api/teacher/menu',
    'http://localhost/amt/api/index.php/teacher_webservice/menu'
];

foreach ($test_urls as $url) {
    echo "<h4>Testing: $url</h4>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['staff_id' => 1]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<div style='background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    
    if ($error) {
        echo "<div style='color: red;'>❌ cURL Error: $error</div>";
    } else {
        echo "<div><strong>HTTP Status:</strong> {$info['http_code']}</div>";
        echo "<div><strong>Content Type:</strong> {$info['content_type']}</div>";
        echo "<div><strong>Total Time:</strong> {$info['total_time']}s</div>";
        
        // Split headers and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE) ?: $info['header_size'];
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        if ($info['http_code'] == 200) {
            echo "<div style='color: green;'>✅ Success!</div>";
            $json_data = json_decode($body, true);
            if ($json_data) {
                echo "<div><strong>API Status:</strong> " . ($json_data['status'] ?? 'Unknown') . "</div>";
                echo "<div><strong>Message:</strong> " . ($json_data['message'] ?? 'No message') . "</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ HTTP Error {$info['http_code']}</div>";
        }
        
        echo "<details style='margin-top: 10px;'>";
        echo "<summary>🔍 View Response Details</summary>";
        echo "<div><strong>Headers:</strong></div>";
        echo "<pre style='background: #f0f0f0; padding: 5px; font-size: 12px;'>" . htmlspecialchars($headers) . "</pre>";
        echo "<div><strong>Body:</strong></div>";
        echo "<pre style='background: #f0f0f0; padding: 5px; font-size: 12px; max-height: 200px; overflow-y: auto;'>" . htmlspecialchars($body) . "</pre>";
        echo "</details>";
    }
    
    echo "</div>";
    echo "<hr>";
}

// 6. PHP Error Log Check
echo "<h2>6. 📋 Error Log Investigation</h2>";

// Check PHP error log
$php_error_log = ini_get('error_log');
if ($php_error_log && file_exists($php_error_log)) {
    echo "<div>📄 PHP Error Log: $php_error_log</div>";
    $recent_errors = shell_exec("tail -20 " . escapeshellarg($php_error_log));
    if ($recent_errors) {
        echo "<div><strong>Recent PHP Errors:</strong></div>";
        echo "<pre style='background: #ffebee; padding: 10px; border: 1px solid #f44336; max-height: 200px; overflow-y: auto;'>";
        echo htmlspecialchars($recent_errors);
        echo "</pre>";
    }
} else {
    echo "<div>ℹ️ PHP error log not configured or accessible</div>";
}

// Check Apache error log (Windows XAMPP path)
$apache_error_log = 'C:/xampp/apache/logs/error.log';
if (file_exists($apache_error_log)) {
    echo "<div>📄 Apache Error Log: $apache_error_log</div>";
    $recent_apache_errors = shell_exec("powershell -Command \"Get-Content '$apache_error_log' | Select-Object -Last 20\"");
    if ($recent_apache_errors) {
        echo "<div><strong>Recent Apache Errors:</strong></div>";
        echo "<pre style='background: #ffebee; padding: 10px; border: 1px solid #f44336; max-height: 200px; overflow-y: auto;'>";
        echo htmlspecialchars($recent_apache_errors);
        echo "</pre>";
    }
} else {
    echo "<div>ℹ️ Apache error log not found at expected location</div>";
}

// Check CodeIgniter logs
$ci_log_path = __DIR__ . '/api/application/logs/';
if (is_dir($ci_log_path)) {
    $log_files = glob($ci_log_path . '*.php');
    if ($log_files) {
        echo "<div>📄 CodeIgniter Logs Found:</div>";
        foreach ($log_files as $log_file) {
            echo "<div>- " . basename($log_file) . "</div>";
            // Read recent entries from the most recent log
            if ($log_file === end($log_files)) {
                $log_content = file_get_contents($log_file);
                $recent_lines = array_slice(explode("\n", $log_content), -20);
                echo "<div><strong>Recent entries from " . basename($log_file) . ":</strong></div>";
                echo "<pre style='background: #fff3cd; padding: 10px; border: 1px solid #ffc107; max-height: 200px; overflow-y: auto;'>";
                echo htmlspecialchars(implode("\n", $recent_lines));
                echo "</pre>";
            }
        }
    } else {
        echo "<div>ℹ️ No CodeIgniter log files found</div>";
    }
} else {
    echo "<div>ℹ️ CodeIgniter logs directory not found</div>";
}

// 7. Manual API Logic Test
echo "<h2>7. 🧪 Manual API Logic Test</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px;'>";

try {
    // Test database queries manually
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    
    // Test staff query
    echo "<h4>Testing Staff Query:</h4>";
    $stmt = $pdo->prepare("SELECT id, name, surname, employee_id, is_active FROM staff WHERE id = ? AND is_active = 1");
    $stmt->execute([1]);
    $staff = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($staff) {
        echo "✅ Staff ID 1 found: {$staff->name} {$staff->surname}<br>";
        
        // Test role query
        echo "<h4>Testing Role Query:</h4>";
        $stmt = $pdo->prepare("
            SELECT r.id, r.name, r.slug, r.is_superadmin
            FROM staff_roles sr
            JOIN roles r ON r.id = sr.role_id
            WHERE sr.staff_id = ? AND sr.is_active = 1
        ");
        $stmt->execute([1]);
        $role = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($role) {
            echo "✅ Role found: {$role->name} (Superadmin: " . ($role->is_superadmin ? 'Yes' : 'No') . ")<br>";
        } else {
            echo "⚠️ No role assigned to staff ID 1<br>";
        }
        
        // Test menu query
        echo "<h4>Testing Menu Query:</h4>";
        $stmt = $pdo->query("
            SELECT COUNT(*) as count
            FROM sidebar_menus sm
            WHERE sm.is_active = 1 AND sm.sidebar_display = 1
        ");
        $menu_count = $stmt->fetch()['count'];
        echo "✅ Available menus: $menu_count<br>";
        
        echo "<div style='background: #e8f5e8; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "✅ <strong>Manual API logic test passed!</strong> The database queries work correctly.";
        echo "</div>";
        
    } else {
        echo "❌ Staff ID 1 not found or inactive<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Manual test error: " . $e->getMessage() . "</div>";
}

echo "</div>";

// 8. Recommendations
echo "<h2>8. 💡 Recommendations & Next Steps</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
echo "<h4>Based on the diagnosis above, try these fixes:</h4>";
echo "<ol>";
echo "<li><strong>Check Error Logs:</strong> Review the error logs shown above for specific error messages</li>";
echo "<li><strong>Test URL Variations:</strong> Try the different URLs tested above in Postman</li>";
echo "<li><strong>Enable CI Debugging:</strong> Temporarily enable CodeIgniter debugging</li>";
echo "<li><strong>Check File Permissions:</strong> Ensure all files are readable by the web server</li>";
echo "<li><strong>Verify XAMPP Setup:</strong> Make sure mod_rewrite is enabled in Apache</li>";
echo "</ol>";
echo "</div>";

echo "<h2>9. 🔧 Quick Fixes</h2>";
echo "<button onclick='enableDebugging()' style='padding: 10px; margin: 5px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer;'>🐛 Enable CI Debugging</button>";
echo "<button onclick='testSimpleEndpoint()' style='padding: 10px; margin: 5px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;'>🧪 Test Simple Endpoint</button>";
echo "<button onclick='checkApacheModules()' style='padding: 10px; margin: 5px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;'>🔍 Check Apache Modules</button>";

echo "<div id='quick-results' style='margin-top: 20px;'></div>";

?>

<script>
function enableDebugging() {
    document.getElementById('quick-results').innerHTML = '<p>⏳ Enabling debugging...</p>';
    fetch('enable_debugging.php', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            document.getElementById('quick-results').innerHTML = '<div style="background: #e8f5e8; padding: 10px; border-radius: 5px;">' + data + '</div>';
        })
        .catch(error => {
            document.getElementById('quick-results').innerHTML = '<div style="background: #ffebee; padding: 10px; border-radius: 5px;">Error: ' + error + '</div>';
        });
}

function testSimpleEndpoint() {
    document.getElementById('quick-results').innerHTML = '<p>⏳ Testing simple endpoint...</p>';
    fetch('test_simple_endpoint.php', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            document.getElementById('quick-results').innerHTML = '<div style="background: #e3f2fd; padding: 10px; border-radius: 5px;">' + data + '</div>';
        });
}

function checkApacheModules() {
    document.getElementById('quick-results').innerHTML = '<p>⏳ Checking Apache modules...</p>';
    fetch('check_apache_modules.php', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            document.getElementById('quick-results').innerHTML = '<div style="background: #f8f9fa; padding: 10px; border-radius: 5px;">' + data + '</div>';
        });
}
</script>

<style>
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 20px; line-height: 1.6; }
h1, h2, h4 { color: #333; }
.success { color: #28a745; }
.error { color: #dc3545; }
.warning { color: #ffc107; }
.info { color: #17a2b8; }
pre { font-size: 12px; }
details { margin: 10px 0; }
summary { cursor: pointer; font-weight: bold; }
</style>