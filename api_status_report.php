<?php
/**
 * API Status Report
 * Quick check of API components and configuration
 */

echo "<h1>🔍 Disable Reason API Status Report</h1>";

// Check 1: Controller file
echo "<h2>📁 File System Check</h2>";
$files_to_check = [
    'api/application/controllers/Disable_reason_api.php' => 'Controller',
    'api/application/models/Disable_reason_model.php' => 'Model',
    'api/application/models/Setting_model.php' => 'Setting Model',
    'api/application/helpers/json_output_helper.php' => 'JSON Helper',
    'api/application/config/routes.php' => 'Routes Config',
    'api/application/config/constants.php' => 'Constants Config',
    'api/application/libraries/Customlib.php' => 'Custom Library',
    'api/application/core/MY_Model.php' => 'Base Model'
];

$file_status = [];
foreach ($files_to_check as $file => $description) {
    $exists = file_exists($file);
    $file_status[] = ['file' => $file, 'desc' => $description, 'exists' => $exists];
    $icon = $exists ? '✅' : '❌';
    echo "<p>$icon <strong>$description:</strong> $file</p>";
}

// Check 2: Database connection
echo "<h2>🗄️ Database Check</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ <strong>Database Connection:</strong> Success</p>";
    
    // Check disable_reason table
    $stmt = $pdo->query("SHOW TABLES LIKE 'disable_reason'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ <strong>disable_reason table:</strong> Exists</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM disable_reason");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>📊 <strong>Records in table:</strong> $count</p>";
    } else {
        echo "<p>❌ <strong>disable_reason table:</strong> Missing</p>";
    }
    
    // Check sch_settings table
    $stmt = $pdo->query("SHOW TABLES LIKE 'sch_settings'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ <strong>sch_settings table:</strong> Exists</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM sch_settings");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>📊 <strong>Settings records:</strong> $count</p>";
    } else {
        echo "<p>❌ <strong>sch_settings table:</strong> Missing</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ <strong>Database Connection:</strong> Failed - " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check 3: Routes configuration
echo "<h2>🛣️ Routes Check</h2>";
if (file_exists('api/application/config/routes.php')) {
    $routes_content = file_get_contents('api/application/config/routes.php');
    $disable_reason_routes = [
        'disable-reason/list' => 'List endpoint',
        'disable-reason/get' => 'Get endpoint',
        'disable-reason/create' => 'Create endpoint',
        'disable-reason/update' => 'Update endpoint',
        'disable-reason/delete' => 'Delete endpoint'
    ];
    
    foreach ($disable_reason_routes as $route => $desc) {
        $exists = strpos($routes_content, $route) !== false;
        $icon = $exists ? '✅' : '❌';
        echo "<p>$icon <strong>$desc:</strong> $route</p>";
    }
} else {
    echo "<p>❌ <strong>Routes file:</strong> Not found</p>";
}

// Check 4: Constants
echo "<h2>🔧 Constants Check</h2>";
if (file_exists('api/application/config/constants.php')) {
    $constants_content = file_get_contents('api/application/config/constants.php');
    $required_constants = [
        'INSERT_RECORD_CONSTANT' => 'Insert constant',
        'UPDATE_RECORD_CONSTANT' => 'Update constant',
        'DELETE_RECORD_CONSTANT' => 'Delete constant'
    ];
    
    foreach ($required_constants as $constant => $desc) {
        $exists = strpos($constants_content, $constant) !== false;
        $icon = $exists ? '✅' : '❌';
        echo "<p>$icon <strong>$desc:</strong> $constant</p>";
    }
} else {
    echo "<p>❌ <strong>Constants file:</strong> Not found</p>";
}

// Check 5: Quick API test
echo "<h2>🧪 Quick API Test</h2>";
$test_url = 'http://localhost/amt/api/disable-reason/list';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $test_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<p>❌ <strong>API Test:</strong> cURL Error - " . htmlspecialchars($error) . "</p>";
} else {
    $icon = ($http_code == 200) ? '✅' : '❌';
    echo "<p>$icon <strong>API Test:</strong> HTTP $http_code</p>";
    
    $json = json_decode($response, true);
    if ($json) {
        echo "<p>📄 <strong>Response Status:</strong> " . ($json['status'] ?? 'N/A') . "</p>";
        echo "<p>💬 <strong>Response Message:</strong> " . htmlspecialchars($json['message'] ?? 'N/A') . "</p>";
    }
}

// Summary
echo "<h2>📋 Summary</h2>";
$total_files = count($file_status);
$existing_files = count(array_filter($file_status, function($f) { return $f['exists']; }));

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<p><strong>File Status:</strong> $existing_files/$total_files files exist</p>";

if ($existing_files == $total_files) {
    echo "<p style='color: green;'>✅ <strong>All required files are present</strong></p>";
} else {
    echo "<p style='color: red;'>❌ <strong>Some required files are missing</strong></p>";
}

if ($http_code == 200) {
    echo "<p style='color: green;'>✅ <strong>API is responding correctly</strong></p>";
    echo "<p><strong>Recommendation:</strong> Run the comprehensive test to verify all endpoints.</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>API is not responding correctly</strong></p>";
    echo "<p><strong>Recommendation:</strong> Check the error logs and fix any issues before testing.</p>";
}
echo "</div>";

echo "<h2>🚀 Next Steps</h2>";
echo "<ol>";
echo "<li>Run <code>comprehensive_api_test.php</code> for full endpoint testing</li>";
echo "<li>Use <code>curl_api_test.bat</code> for command-line testing</li>";
echo "<li>Check the API documentation for usage examples</li>";
echo "<li>Monitor error logs for any issues</li>";
echo "</ol>";
?>
